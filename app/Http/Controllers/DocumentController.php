<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\IncomingDocument;
use App\Models\OutgoingDocument;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllDocumentsExport;

class DocumentController extends Controller
{
    public function index()
    {
        return view('menu');
    }

    public function createIncoming()
    {
        return view('incoming.create');
    }

    public function createOutgoing()
    {
        return view('outgoing.create');
    }

    public function storeIncoming(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,png'
        ]);

        $request->file('file')->store('incoming', 'public');

        return redirect('/')->with('success', 'Εισερχόμενο αρχείο αποθηκεύτηκε');
    }

    public function storeOutgoing(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,png'
        ]);

        $request->file('file')->store('outgoing', 'public');

        return redirect('/')->with('success', 'Εξερχόμενο αρχείο αποθηκεύτηκε');
    }

    public function incomingList()
    {
        $files = Storage::disk('public')->files('incoming');
        return view('incoming.list', compact('files'));
    }

    public function outgoingList()
    {
        $files = Storage::disk('public')->files('outgoing');
        return view('outgoing.list', compact('files'));
    }

    public function all()
    {
        // Όλα τα εισερχόμενα με τα εξερχόμενά τους
        $incomingDocs = IncomingDocument::with('outgoingReplies')->get();

        // Όλα τα εξερχόμενα που δεν έχουν εισερχόμενο
        $orphanOutgoings = OutgoingDocument::whereNull('reply_to_incoming_id')->get();

        $allDocuments = [];

        // Προσθέτουμε τα εισερχόμενα με τα εξερχόμενά τους
        foreach($incomingDocs as $in) {
            $outgoingList = $in->outgoingReplies ?? collect();
            
            $allDocuments[] = [
                'type' => 'incoming',
                'date' => $in->incoming_date ?? $in->created_at,
                'incoming' => $in,
                'outgoing' => $outgoingList
            ];
        }

        // Προσθέτουμε τα "μόνο εξερχόμενα" χωρίς εισερχόμενο
        foreach($orphanOutgoings as $out) {
            $allDocuments[] = [
                'type' => 'outgoing',
                'date' => $out->document_date ?? $out->created_at,
                'incoming' => null,
                'outgoing' => collect([$out])
            ];
        }

        // Ταξινόμηση όλων κατά ημερομηνία
        usort($allDocuments, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        // Προσθήκη συνεχούς αρίθμησης
        $documents = [];
        $counter = 1;
        foreach($allDocuments as $doc) {
            $doc['display_aa'] = $counter++;
            $documents[] = $doc;
        }

        return view('documents.all', compact('documents'));
    }
}
