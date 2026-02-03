<?php

namespace App\Http\Controllers;

use App\Models\OutgoingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OutgoingDocumentController extends Controller
{
    public function index()
    {
        $documents = OutgoingDocument::orderBy('aa', 'asc')->paginate(10);
        return view('outgoing.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reply_to_incoming_id'      => 'nullable|exists:incoming_documents,id',

            'protocol_number'           => 'required|string',   // το hidden που στέλνεις από τη φόρμα
            'incoming_protocol'         => 'nullable|string',
            'incoming_date'             => 'nullable|date',
            'subject'                   => 'nullable|string',
            'sender'                    => 'nullable|string',
            'document_date'             => 'nullable|date',
            'incoming_document_number'  => 'nullable|string',
            'summary'                   => 'nullable|string',
            'comments'                  => 'nullable|string',

            // ✅ ΥΠΟΧΡΕΩΤΙΚΟ PDF
            'attachment'                => 'required|file|mimes:pdf|max:10240',
        ]);

        // ✅ Δεν περνάμε το attachment στο create
        $data = $validated;
        unset($data['attachment']);

        $document = DB::transaction(function () use ($data) {
            // ✅ Το “Α/Α” του εξερχομένου = protocol_number (είτε σειριακό είτε από απάντηση)
            $aa = (int) $data['protocol_number'];

            return OutgoingDocument::create([
                ...$data,
                'aa' => $aa,
            ]);
        });

        // ✅ Αποθήκευση PDF
        $file = $request->file('attachment');
        $filename = 'outgoing_' . $document->aa . '_' . now()->format('Ymd_His') . '.pdf';
        $path = $file->storeAs('outgoing_attachments', $filename, 'public');

        $document->update(['attachment_path' => $path]);

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
        $validated = $request->validate([
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

            // ✅ ΥΠΟΧΡΕΩΤΙΚΟ PDF (ίδιο όπως incoming)
            'attachment'                => 'required|file|mimes:pdf|max:10240',
        ]);

        // ✅ Δεν περνάμε το attachment στο update array
        $data = $validated;
        unset($data['attachment']);

        $aa = (int) $data['protocol_number'];

        $doc = OutgoingDocument::findOrFail($id);

        $doc->update([
            ...$data,
            'aa' => $aa,
        ]);

        // ✅ Αποθήκευση PDF (αντικατάσταση)
        $file = $request->file('attachment');
        $filename = 'outgoing_' . $doc->aa . '_' . now()->format('Ymd_His') . '.pdf';
        $path = $file->storeAs('outgoing_attachments', $filename, 'public');

        $doc->update(['attachment_path' => $path]);

        audit_log('outgoing', 'update', $id);

        return redirect()->route('outgoing.index')->with('success', 'Το εξερχόμενο ενημερώθηκε.');
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
}
