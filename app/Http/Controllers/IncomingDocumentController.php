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
        $documents = IncomingDocument::withCount('outgoingReplies')
             ->orderBy('aa', 'asc')
             ->paginate(25);

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


         // ❗ Δεν περνάμε τα attachments στο create
         $data = $validated;
         unset($data['attachments']);

         $document = DB::transaction(function () use ($data) {
             $nextAa = $this->nextProtocolNumber();


             return IncomingDocument::create([
                 ...$data,
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

            $year = $date->format('Y');
            $folderDate = $date->format('Y-m-d');

         foreach ($files as $i => $file) {
             $filename = 'incoming_' . $document->aa . '_' . now()->format('His') . '_' . ($i + 1) . '.pdf';

             $path = $file->storeAs(
                "{$year}/incoming/{$folderDate}",
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
        $document = IncomingDocument::findOrFail($id);
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
        ]);

        IncomingDocument::findOrFail($id)->update($validated);

        audit_log('incoming', 'update', $id);

        return redirect()->route('incoming.index')->with('success', 'Το εισερχόμενο ενημερώθηκε.');
    }

    public function destroy($id)
    {
        audit_log('incoming', 'delete', $id);

        IncomingDocument::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Το εισερχόμενο διαγράφηκε.');
    }

    public function downloadAttachment($id)
    {
        $doc = IncomingDocument::findOrFail($id);

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
    $doc = IncomingDocument::with('attachments')->findOrFail($id);

    $backUrl = $request->query('return') ?: route('incoming.index');

    return view('incoming.attachments', compact('doc', 'backUrl'));
    }


    public function attachmentsView($id, $attachmentId)
    {
        $doc = IncomingDocument::findOrFail($id);

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
    $doc = IncomingDocument::findOrFail($id);
    $att = $doc->attachments()->findOrFail($attachmentId);

    // Τίτλος καρτέλας (σωστός)
    $title = $att->original_name ?: ('incoming_' . $doc->aa . '.pdf');

    // Το PDF συνεχίζει να σερβίρεται από το υπάρχον route incoming.attachments.view
    $pdfUrl = route('incoming.attachments.view', [$doc->id, $att->id]);

    return view('incoming.viewer', compact('doc', 'att', 'title', 'pdfUrl'));
   }

   private function nextProtocolNumber(): int
   {
    return DB::transaction(function () {
        $row = DB::table('protocol_counters')->lockForUpdate()->where('id', 1)->first();

        $next = ((int) $row->current) + 1;

        DB::table('protocol_counters')->where('id', 1)->update([
            'current' => $next,
            'updated_at' => now(),
        ]);

        return $next;
    });
   }


}
