<?php

namespace App\Http\Controllers;

use App\Models\IncomingDocument;
use League\CommonMark\Renderer\HtmlDecorator;

class CommonDocumentController extends Controller
{
    public function index()
    {
        $year = (int) session('protocol_year', now()->year);

        // ✅ “Κοινά” = Εισερχόμενα που έχουν τουλάχιστον 1 εξερχόμενο-απάντηση
        // ✅ Ταξινόμηση με Α/Α εισερχομένου (αύξουσα)
        // ✅ Φέρνουμε μαζί και τα εξερχόμενα (απαντήσεις) ταξινομημένα με Α/Α εξερχομένου (αύξουσα)
        $incomingGroups = IncomingDocument::with(['outgoingReplies' => function ($q) {
                $q->orderBy('aa', 'asc');
            }])
            ->where('protocol_year', $year)
            ->whereHas('outgoingReplies')
            ->orderBy('aa', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('documents.common', compact('incomingGroups'));
    }
}
