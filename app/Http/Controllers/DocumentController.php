<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
}
