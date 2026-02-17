<?php

namespace App\Http\Controllers;

use App\Models\IncomingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IncomingDocumentController extends Controller
{
    public function index()
    {
        $year = (int) session('protocol_year', now()->year);

        $documents = IncomingDocument::withCount('outgoingReplies')
            ->where('protocol_year', $year)
            ->orderBy('aa', 'asc')
            ->paginate(25)
            ->withQueryString();

        return view('incoming.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('incoming', [
            'protocol_number'   => 'nullable|string',
            'incoming_protocol' => 'nullable|string',
            'incoming_date'     => 'nullable|date',
            'subject'           => 'nullable|string',
            'sender'            => 'nullable|string',
            'document_date'     => 'nullable|date',
            'summary'           => 'nullable|string',
            'comments'          => 'nullable|string',

            'attachments'       => 'required|array|min:1',
            'attachments.*'     => 'file|mimes:pdf|max:51200',
        ]);

        $year = (int) session('protocol_year', now()->year);

        // ❗ Δεν περνάμε τα attachments στο create
        $data = $validated;
        unset($data['attachments']);

        $document = DB::transaction(function () use ($data, $year) {
            $nextAa = $this->nextProtocolNumber($year);

            return IncomingDocument::create([
                ...$data,
                'protocol_year' => $year,
                'aa' => $nextAa,
                'protocol_number' => (string) $nextAa,
            ]);
        });

        // Αποθήκευση ΟΛΩΝ των PDF
        $files = $request->file('attachments');

        // Decide which date to use for folder
        $date = $document->incoming_date
            ? Carbon::parse($document->incoming_date)
            : now();

        $yearFolder = $date->format('Y');
        $folderDate = $date->format('Y-m-d');

        foreach ($files as $i => $file) {
            $filename = 'incoming_' . $document->aa . '_' . now()->format('His') . '_' . ($i + 1) . '.pdf';

            $path = $file->storeAs(
                "{$yearFolder}/incoming/{$folderDate}",
                $filename,
                'public'
            );

            // Δημιουργία row στο νέο table
            $document->attachments()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);

            // Backward compatibility: το 1ο αρχείο γράφεται και στο attachment_path
            if ($i === 0) {
                $document->update(['attachment_path' => $path]);
            }
        }

        audit_log('incoming', 'create', $document->id);

        return redirect()->back()->with('success', 'Το εισερχόμενο καταχωρήθηκε επιτυχώς.');
    }

   public function edit($id)
  {
    $year = (int) session('protocol_year', now()->year);

    $document = IncomingDocument::with('attachments')
        ->where('protocol_year', $year)
        ->findOrFail($id);

    return view('incoming.edit', compact('document'));
  }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'protocol_number'   => 'nullable|string',
            'incoming_protocol' => 'nullable|string',
            'incoming_date'     => 'nullable|date',
            'subject'           => 'nullable|string',
            'sender'            => 'nullable|string',
            'document_date'     => 'nullable|date',
            'summary'           => 'nullable|string',
            'comments'          => 'nullable|string',
            // ✅ NEW (edit multi-attachments)
            'attachments'       => 'nullable|array',
            'attachments.*'     => 'file|mimes:pdf|max:51200',
        ]);

        $year = (int) session('protocol_year', now()->year);

        $doc = IncomingDocument::where('protocol_year', $year)->findOrFail($id);

        // ❗ Δεν περνάμε τα attachments στο update()
        $data = $validated;
        unset($data['attachments']);

        $doc->update($data);

        // ✅ Αν ανέβηκαν νέα PDF, τα αποθηκεύουμε όπως στο store
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');

            $date = $doc->incoming_date
                ? Carbon::parse($doc->incoming_date)
                : now();

            $yearFolder = $date->format('Y');
            $folderDate = $date->format('Y-m-d');

            foreach ($files as $i => $file) {
                $filename = 'incoming_' . $doc->aa . '_' . now()->format('His') . '_' . ($i + 1) . '.pdf';

                $path = $file->storeAs(
                    "{$yearFolder}/incoming/{$folderDate}",
                    $filename,
                    'public'
                );

                $doc->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);

                // Backward compatibility: το 1ο νέο αρχείο γράφεται και στο attachment_path
                if ($i === 0) {
                    $doc->update(['attachment_path' => $path]);
                }
            }
        }

        audit_log('incoming', 'update', $id);

        return redirect()->route('incoming.index')->with('success', 'Το εισερχόμενο ενημερώθηκε.');
    }

    public function destroy($id)
    {
        $year = (int) session('protocol_year', now()->year);

        audit_log('incoming', 'delete', $id);

        IncomingDocument::where('protocol_year', $year)->findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Το εισερχόμενο διαγράφηκε.');
    }

    public function downloadAttachment($id)
    {
        $year = (int) session('protocol_year', now()->year);

        $doc = IncomingDocument::where('protocol_year', $year)->findOrFail($id);

        abort_unless($doc->attachment_path, 404);

        $filePath = storage_path('app/public/' . $doc->attachment_path);
        abort_unless(file_exists($filePath), 404);

        $filename = 'incoming_' . $doc->aa . '.pdf';
        $fallback = $filename;

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$fallback}\"; filename*=UTF-8''" . rawurlencode($filename),
        ]);
    }

    public function attachmentsIndex(Request $request, $id)
    {
        $year = (int) session('protocol_year', now()->year);

        $doc = IncomingDocument::with('attachments')
            ->where('protocol_year', $year)
            ->findOrFail($id);

        $backUrl = $request->query('return') ?: route('incoming.index');

        return view('incoming.attachments', compact('doc', 'backUrl'));
    }

    public function attachmentsView($id, $attachmentId)
    {
        $year = (int) session('protocol_year', now()->year);

        $doc = IncomingDocument::where('protocol_year', $year)->findOrFail($id);

        $att = $doc->attachments()->findOrFail($attachmentId);

        abort_unless(Storage::disk('public')->exists($att->path), 404);

        $filename = $att->original_name ?: ('incoming_' . $doc->aa . '.pdf');
        $fallback = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);

        return response()->file(Storage::disk('public')->path($att->path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$fallback}\"; filename*=UTF-8''" . rawurlencode($filename),
        ]);
    }

    public function attachmentsViewer($id, $attachmentId)
    {
        $year = (int) session('protocol_year', now()->year);

        $doc = IncomingDocument::where('protocol_year', $year)->findOrFail($id);
        $att = $doc->attachments()->findOrFail($attachmentId);

        // Τίτλος καρτέλας (σωστός)
        $title = $att->original_name ?: ('incoming_' . $doc->aa . '.pdf');

        // Το PDF συνεχίζει να σερβίρεται από το υπάρχον route incoming.attachments.view
        $pdfUrl = route('incoming.attachments.view', [$doc->id, $att->id]);

        return view('incoming.viewer', compact('doc', 'att', 'title', 'pdfUrl'));
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
    $doc = IncomingDocument::with('attachments')->findOrFail($id);

    $att = $doc->attachments()->findOrFail($attachmentId);

    // 1) Σβήνουμε αρχείο από storage (αν υπάρχει)
    if ($att->path && Storage::disk('public')->exists($att->path)) {
        Storage::disk('public')->delete($att->path);
    }

    // 2) Σβήνουμε row από table
    $att->delete();

    // 3) Backward compatibility: αν το attachment_path δείχνει σε αυτό που διέγραψες,
    //    βάλε το στο "πρώτο" που απομένει ή null.
    if ($doc->attachment_path === $att->path) {
        $first = $doc->attachments()->orderBy('id', 'asc')->first();
        $doc->update(['attachment_path' => $first?->path]);
    }

    $backUrl = $request->query('return') ?: route('incoming.edit', $doc->id);

    return redirect($backUrl)->with('success', 'Το συνημμένο διαγράφηκε.');
  }

}
