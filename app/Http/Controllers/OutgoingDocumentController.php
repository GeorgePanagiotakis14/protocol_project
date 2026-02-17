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

        // ✅ Δεν περνάμε τα attachments στο create
        $data = $validated;
        unset($data['attachments']);

        $document = DB::transaction(function () use ($data, $selectedYear) {
            $aa = null;
            $yearToSave = $selectedYear;

            // Αν είναι απάντηση -> παίρνει Α/Α από το incoming + ίδιο protocol_year με το incoming
            if (!empty($data['reply_to_incoming_id'])) {
                $incoming = \App\Models\IncomingDocument::findOrFail($data['reply_to_incoming_id']);
                $aa = (int) $incoming->aa;
                $yearToSave = (int) ($incoming->protocol_year ?? $selectedYear);
            } else {
                // Ανεξάρτητο εξερχόμενο -> παίρνει νέο από counter του επιλεγμένου έτους
                $aa = $this->nextProtocolNumber($selectedYear);
            }

            // γράφουμε σωστά στο record
            return OutgoingDocument::create([
                ...$data,
                'protocol_year' => $yearToSave,
                'aa' => $aa,
                'protocol_number' => (string) $aa,
            ]);
        });

        // ✅ Αποθήκευση ΟΛΩΝ των PDF
        $files = $request->file('attachments');

        // Folder date: ίδια λογική με πριν (αν έχει incoming_date αλλιώς now)
        $date = $document->incoming_date
            ? Carbon::parse($document->incoming_date)
            : now();

        $year = $date->format('Y');
        $folderDate = $date->format('Y-m-d');

        foreach ($files as $i => $file) {
            $filename = 'outgoing_' . $document->aa . '_' . now()->format('His') . '_' . ($i + 1) . '.pdf';

            $path = $file->storeAs(
                "{$year}/outgoing/{$folderDate}",
                $filename,
                'public'
            );

            // ✅ γράφει στο νέο table outgoing_document_attachments
            $document->attachments()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);

            // ✅ backward compatibility: το 1ο γράφεται και στο attachment_path
            if ($i === 0) {
                $document->update(['attachment_path' => $path]);
            }
        }

        audit_log('outgoing', 'create', $document->id);

        return redirect()->back()->with('success', 'Το εξερχόμενο καταχωρήθηκε επιτυχώς.');
    }

    public function edit($id)
    {
        $document = OutgoingDocument::findOrFail($id);
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
            'summary'             => 'nullable|string',
            'comments'            => 'nullable|string',

            // ✅ ΤΩΡΑ ΠΟΛΛΑ PDF ΟΠΩΣ ΣΤΟ INCOMING
            'attachments'         => 'nullable|array',
            'attachments.*'       => 'file|mimes:pdf|max:51200',
        ]);

        $doc = OutgoingDocument::findOrFail($id);

        // αφαιρούμε τα attachments από τα δεδομένα του update
        $data = $validated;
        unset($data['attachments']);

        // αποθήκευση στη βάση
        $doc->update($data);

        // αν ανέβηκαν νέα PDF
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');

            $date = $doc->incoming_date
                ? Carbon::parse($doc->incoming_date)
                : now();

            $year = $date->format('Y');
            $folderDate = $date->format('Y-m-d');

            foreach ($files as $i => $file) {
                $filename = 'outgoing_' . $doc->aa . '_' . now()->format('His') . '_' . ($i + 1) . '.pdf';

                $path = $file->storeAs(
                    "{$year}/outgoing/{$folderDate}",
                    $filename,
                    'public'
                );

                $doc->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);

                // backward compatibility: το 1ο γράφεται και στο attachment_path
                if ($i === 0) {
                    $doc->update(['attachment_path' => $path]);
                }
            }
        }

        return redirect()
            ->route('outgoing.index')
            ->with('success', 'Το εξερχόμενο ενημερώθηκε.');
    }

    public function destroy($id)
    {
        audit_log('outgoing', 'delete', $id);

        OutgoingDocument::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Το εξερχόμενο διαγράφηκε.');
    }

    // ✅ Inline προβολή PDF (όπως στα εισερχόμενα)
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

        // Τίτλος καρτέλας (σωστός)
        $title = $att->original_name ?: ('outgoing_' . $doc->aa . '.pdf');

        // Το PDF συνεχίζει να σερβίρεται από το υπάρχον route outgoing.attachments.view
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
}
