<?php

namespace App\Http\Controllers;

use App\Models\OutgoingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OutgoingDocumentController extends Controller
{
    public function index()
    {
        $year = (int) session('protocol_year', now()->year);

        $documents = OutgoingDocument::where('protocol_year', $year)
            ->orderBy('aa', 'asc')
            ->paginate(25)
            ->withQueryString();

        return view('outgoing.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('outgoing', [
            'reply_to_incoming_id'      => 'nullable|exists:incoming_documents,id',

            'protocol_number'           => 'required|string',
            'incoming_protocol'         => 'nullable|string',
            'incoming_date'             => 'nullable|date',
            'subject'                   => 'nullable|string',
            'sender'                    => 'nullable|string',
            'document_date'             => 'nullable|date',
            'incoming_document_number'  => 'nullable|string',
            'summary'                   => 'nullable|string',
            'comments'                  => 'nullable|string',

            'attachments'               => 'required|array|min:1',
            'attachments.*'             => 'file|mimes:pdf|max:51200',
        ]);

        $selectedYear = (int) session('protocol_year', now()->year);

        $data = $validated;
        unset($data['attachments']);

        $document = DB::transaction(function () use ($data, $selectedYear) {
            $aa = null;
            $yearToSave = $selectedYear;

            if (!empty($data['reply_to_incoming_id'])) {
                $incoming = \App\Models\IncomingDocument::findOrFail($data['reply_to_incoming_id']);
                $aa = (int) $incoming->aa;
                $yearToSave = (int) ($incoming->protocol_year ?? $selectedYear);
            } else {
                $aa = $this->nextProtocolNumber($selectedYear);
            }

            return OutgoingDocument::create([
                ...$data,
                'protocol_year' => $yearToSave,
                'aa' => $aa,
                'protocol_number' => (string) $aa,
            ]);
        });

        $files = $request->file('attachments');

        $date = $document->document_date
            ? Carbon::parse($document->document_date)
            : now();

        $year = (string) $document->protocol_year;

        $folderDate = $date->format('Y-m-d');

        foreach ($files as $i => $file) {
            $filename = 'outgoing_' . $document->aa . '_' . now()->format('His') . '_' . ($i + 1) . '.pdf';

            $path = $file->storeAs(
                "{$year}/outgoing/{$folderDate}",
                $filename,
                'public'
            );

            $document->attachments()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);

            if ($i === 0) {
                $document->update(['attachment_path' => $path]);
            }
        }

        audit_log('outgoing', 'create', $document->id);

        return redirect()
            ->back()
            ->with('success', 'Το εξερχόμενο καταχωρήθηκε επιτυχώς.');
    }

    public function edit($id)
    {
        $year = (int) session('protocol_year', now()->year);

        // 1) Προσπάθησε με το τρέχον έτος (όπως είχες)
        $document = OutgoingDocument::with('attachments')
            ->where('protocol_year', $year)
            ->find($id);

        // 2) Αν δεν βρεθεί (π.χ. είναι σε άλλο έτος), κάνε auto-switch στο σωστό year
        if (!$document) {
            $any = OutgoingDocument::with('attachments')->findOrFail($id);

            return redirect()->route('outgoing.edit', [
                'id' => $any->id,
                'year' => $any->protocol_year,
            ]);
        }

        return view('outgoing.edit', compact('document'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validateWithBag('updateDocument', [
            'reply_to_incoming_id' => 'nullable|exists:incoming_documents,id',
            'protocol_number'     => 'required|string',
            'incoming_protocol'   => 'nullable|string',
            'incoming_date'       => 'nullable|date',
            'subject'             => 'nullable|string',
            'sender'              => 'nullable|string',
            'document_date'       => 'nullable|date',
            'incoming_document_number' => 'nullable|string',
            'summary'             => 'nullable|string',
            'comments'            => 'nullable|string',

            'attachments'         => 'nullable|array',
            'attachments.*'       => 'file|mimes:pdf|max:51200',
        ]);

        $year = (int) session('protocol_year', now()->year);

        $doc = OutgoingDocument::where('protocol_year', $year)->findOrFail($id);

        $data = $validated;
        unset($data['attachments']);

        $doc->update($data);

        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');

            $date = $doc->document_date
                ? Carbon::parse($doc->document_date)
                : now();

            $yearFolder = (string) $doc->protocol_year;

            $folderDate = $date->format('Y-m-d');

            foreach ($files as $i => $file) {
                $filename = 'outgoing_' . $doc->aa . '_' . now()->format('His') . '_' . ($i + 1) . '.pdf';

                $path = $file->storeAs(
                    "{$yearFolder}/outgoing/{$folderDate}",
                    $filename,
                    'public'
                );

                $doc->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);

                if ($i === 0) {
                    $doc->update(['attachment_path' => $path]);
                }
            }
        }

        audit_log('outgoing', 'update', $doc->id);


        return redirect()
            ->route('outgoing.index', ['year' => $doc->protocol_year])
            ->with('success', 'Το εξερχόμενο ενημερώθηκε.');
    }

    public function destroy($id)
    {
        $year = (int) session('protocol_year', now()->year);

        $doc = OutgoingDocument::where('protocol_year', $year)->findOrFail($id);

        audit_log('outgoing', 'delete', $doc->id);

        $doc->delete();

        return redirect()
            ->route('outgoing.index', ['year' => $year])
            ->with('success', 'Το εξερχόμενο διαγράφηκε.');
    }

    public function viewAttachment($id)
    {
        $doc = OutgoingDocument::findOrFail($id);

        abort_unless($doc->attachment_path, 404);

        $filePath = storage_path('app/public/' . $doc->attachment_path);
        abort_unless(file_exists($filePath), 404);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function attachmentsIndex(Request $request, $id)
    {
        $doc = OutgoingDocument::with('attachments')->findOrFail($id);

        $backUrl = $request->query('return') ?: route('outgoing.index');

        return view('outgoing.attachments', compact('doc', 'backUrl'));
    }

    public function attachmentsView($id, $attachmentId)
    {
        $doc = OutgoingDocument::findOrFail($id);

        $att = $doc->attachments()->findOrFail($attachmentId);

        abort_unless(Storage::disk('public')->exists($att->path), 404);

        return response()->file(Storage::disk('public')->path($att->path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function attachmentsViewer($id, $attachmentId)
    {
        $doc = \App\Models\OutgoingDocument::findOrFail($id);
        $att = $doc->attachments()->findOrFail($attachmentId);

        $title = $att->original_name ?: ('outgoing_' . $doc->aa . '.pdf');
        $pdfUrl = route('outgoing.attachments.view', [$doc->id, $att->id]);

        return view('outgoing.viewer', compact('doc', 'att', 'title', 'pdfUrl'));
    }

    private function nextProtocolNumber(int $year): int
    {
        return DB::transaction(function () use ($year) {
            $row = DB::table('protocol_counters')
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                DB::table('protocol_counters')->insert([
                    'year' => $year,
                    'current' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $row = DB::table('protocol_counters')
                    ->where('year', $year)
                    ->lockForUpdate()
                    ->first();
            }

            $next = ((int) $row->current) + 1;

            DB::table('protocol_counters')->where('year', $year)->update([
                'current' => $next,
                'updated_at' => now(),
            ]);

            return $next;
        });
    }

    public function attachmentsDestroy(Request $request, $id, $attachmentId)
    {
        $year = (int) session('protocol_year', now()->year);

        $doc = OutgoingDocument::with('attachments')
            ->where('protocol_year', $year)
            ->findOrFail($id);

        $att = $doc->attachments()->findOrFail($attachmentId);

        if ($att->path && Storage::disk('public')->exists($att->path)) {
            Storage::disk('public')->delete($att->path);
        }

        $att->delete();

        if ($doc->attachment_path === $att->path) {
            $first = $doc->attachments()->orderBy('id', 'asc')->first();
            $doc->update(['attachment_path' => $first?->path]);
        }

        $backUrl = $request->query('return') ?: route('outgoing.edit', [
            'id' => $doc->id,
            'year' => $doc->protocol_year,
        ]);

        return redirect($backUrl)->with('success', 'Το συνημμένο διαγράφηκε.');
    }
}
