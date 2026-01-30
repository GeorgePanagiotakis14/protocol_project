<?php

namespace App\Http\Controllers;

use App\Models\OutgoingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ]);

        $document = DB::transaction(function () use ($validated) {
            // ✅ Το “Α/Α” του εξερχομένου = protocol_number (είτε σειριακό είτε από απάντηση)
            $aa = (int) $validated['protocol_number'];

            return OutgoingDocument::create([
                ...$validated,
                'aa' => $aa,
            ]);
        });

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
        ]);

        $aa = (int) $validated['protocol_number'];

        OutgoingDocument::findOrFail($id)->update([
            ...$validated,
            'aa' => $aa,
        ]);

        audit_log('outgoing', 'update', $id);

        return redirect()->route('outgoing.index')->with('success', 'Το εξερχόμενο ενημερώθηκε.');
    }

    public function destroy($id)
    {
        audit_log('outgoing', 'delete', $id);

        OutgoingDocument::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Το εξερχόμενο διαγράφηκε.');
    }
}
