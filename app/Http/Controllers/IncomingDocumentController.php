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
             ->paginate(10);

        return view('incoming.index', compact('documents'));
    }


    public function store(Request $request)
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

            // ΥΠΟΧΡΕΩΤΙΚΟ PDF
            'attachment'        => 'required|file|mimes:pdf|max:10240',
        ]);

        // ❗ ΔΕΝ στέλνουμε το attachment στο create
        $data = $validated;
        unset($data['attachment']);

        $document = DB::transaction(function () use ($data) {
            $nextAa = (int) (IncomingDocument::lockForUpdate()->max('aa') ?? 0) + 1;

            return IncomingDocument::create([
                ...$data,
                'aa' => $nextAa,
                'protocol_number' => (string) $nextAa,
            ]);
        });

        // Αποθήκευση PDF
        $file = $request->file('attachment');

        // Decide which date to use
        $date = $document->incoming_date
            ? Carbon::parse($document->incoming_date)
            : now();

        // Year + folder date
        $year = $date->format('Y');
        $folderDate = $date->format('Y-m-d');

        // Filename
        $filename = 'incoming_' . $document->aa . '_' . now()->format('His') . '.pdf';
        
        // Store
        $path = $file->storeAs(
            "{$year}/incoming/{$folderDate}",
            $filename,
            'public'
        );

        $document->update([
            'attachment_path' => $path,
        ]);

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

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }   

}
