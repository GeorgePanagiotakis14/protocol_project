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
    $incomingDocs = IncomingDocument::with(['outgoingReplies' => function ($q) {
        $q->orderBy('aa', 'asc');
    }])->get();

    // Όλα τα εξερχόμενα που δεν έχουν εισερχόμενο
    $orphanOutgoings = OutgoingDocument::whereNull('reply_to_incoming_id')
        ->orderBy('aa', 'asc')
        ->get();

    $allDocuments = [];

    // Προσθέτουμε τα εισερχόμενα με τα εξερχόμενά τους
    foreach ($incomingDocs as $in) {
        $outgoingList = $in->outgoingReplies ?? collect();

        $allDocuments[] = [
            'type' => 'incoming',
            'aa' => (int) $in->aa, // ✅ ΚΥΡΙΟ: πραγματικό Α/Α
            'incoming' => $in,
            'outgoing' => $outgoingList,
        ];
    }

    // Προσθέτουμε τα "μόνο εξερχόμενα" χωρίς εισερχόμενο
    foreach ($orphanOutgoings as $out) {
        $allDocuments[] = [
            'type' => 'outgoing',
            'aa' => (int) $out->aa, // ✅ ΚΥΡΙΟ: πραγματικό Α/Α
            'incoming' => null,
            'outgoing' => collect([$out]),
        ];
    }

    // ✅ Ταξινόμηση όλων κατά Α/Α (αύξουσα)
    usort($allDocuments, function ($a, $b) {
        return $a['aa'] <=> $b['aa'];
    });

    // ✅ Το A/A που θα εμφανίζεται στη σελίδα να είναι το πραγματικό Α/Α
    foreach ($allDocuments as &$doc) {
        $doc['display_aa'] = $doc['aa'];
    }
    unset($doc);

    $documents = $allDocuments;

    return view('documents.all', compact('documents'));
  }

}
