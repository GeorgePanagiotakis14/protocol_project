<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\IncomingDocument;
use App\Models\OutgoingDocument;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllDocumentsExport;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


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

   public function all(Request $request)
{
    $perPage = 20;

    // Όλα τα εισερχόμενα με τα εξερχόμενά τους
    $incomingDocs = IncomingDocument::with(['outgoingReplies' => function ($q) {
        $q->orderBy('aa', 'asc');
    }])->get();

    // Όλα τα εξερχόμενα που δεν έχουν εισερχόμενο
    $orphanOutgoings = OutgoingDocument::whereNull('reply_to_incoming_id')
        ->orderBy('aa', 'asc')
        ->get();

    $allDocuments = [];

    foreach ($incomingDocs as $in) {
        $outgoingList = $in->outgoingReplies ?? collect();

        $allDocuments[] = [
            'type' => 'incoming',
            'aa' => (int) $in->aa, // πραγματικό Α/Α
            'incoming' => $in,
            'outgoing' => $outgoingList,
        ];
    }

    foreach ($orphanOutgoings as $out) {
        $allDocuments[] = [
            'type' => 'outgoing',
            'aa' => (int) $out->aa, // πραγματικό Α/Α
            'incoming' => null,
            'outgoing' => collect([$out]),
        ];
    }

    // Ταξινόμηση όλων κατά Α/Α (αύξουσα)
    usort($allDocuments, function ($a, $b) {
        return $a['aa'] <=> $b['aa'];
    });

    foreach ($allDocuments as &$doc) {
        $doc['display_aa'] = $doc['aa'];
    }
    unset($doc);

    // ✅ Pagination πάνω στα GROUPS (όχι στις γραμμές του πίνακα)
    $page = Paginator::resolveCurrentPage('page');
    $collection = collect($allDocuments);

    $items = $collection->slice(($page - 1) * $perPage, $perPage)->values();

    $documents = new LengthAwarePaginator(
        $items,
        $collection->count(),
        $perPage,
        $page,
        [
            'path' => $request->url(),
            'query' => $request->query(), // κρατάει query params
        ]
    );

    return view('documents.all', compact('documents'));
}


  public function print(Request $request)
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        // Εισερχόμενα στο range
        $incomingDocs = IncomingDocument::with('outgoingReplies')
            ->whereBetween('incoming_date', [$from, $to])
            ->get();

        // Εξερχόμενα χωρίς εισερχόμενο στο range
        $orphanOutgoings = OutgoingDocument::whereNull('reply_to_incoming_id')
            ->whereBetween('document_date', [$from, $to])
            ->get();

        $allDocuments = [];

        foreach ($incomingDocs as $in) {
            $allDocuments[] = [
                'type' => 'incoming',
                'date' => $in->incoming_date,
                'incoming' => $in,
                'outgoing' => $in->outgoingReplies ?? collect()
            ];
        }

        foreach ($orphanOutgoings as $out) {
            $allDocuments[] = [
                'type' => 'outgoing',
                'date' => $out->document_date,
                'incoming' => null,
                'outgoing' => collect([$out])
            ];
        }

        usort($allDocuments, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        $documents = [];
        $counter = 1;
        foreach ($allDocuments as $doc) {
            $doc['display_aa'] = $counter++;
            $documents[] = $doc;
        }

        // Μόνο εκτύπωση browser, χωρίς PDF
        return view('documents.print', compact('documents', 'from', 'to'));
    }

}
