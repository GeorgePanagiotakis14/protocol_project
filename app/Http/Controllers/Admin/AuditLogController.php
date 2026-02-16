<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\IncomingDocument;
use App\Models\OutgoingDocument;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Επιλεγμένο έτος από session (όπως σε όλα τα υπόλοιπα)
        $selectedYear = (int) session('protocol_year', now()->year);

        // ✅ IDs εγγράφων του επιλεγμένου έτους
        $incomingIds = IncomingDocument::where('protocol_year', $selectedYear)->pluck('id');
        $outgoingIds = OutgoingDocument::where('protocol_year', $selectedYear)->pluck('id');

        $query = AuditLog::with('user')
            ->where(function ($q) use ($incomingIds, $outgoingIds) {
                $q->where(function ($q2) use ($incomingIds) {
                    $q2->where('section', 'incoming')
                       ->whereIn('document_id', $incomingIds);
                })->orWhere(function ($q2) use ($outgoingIds) {
                    $q2->where('section', 'outgoing')
                       ->whereIn('document_id', $outgoingIds);
                });
            })
            ->latest();

        if ($request->filled('section') && $request->section !== 'all') {
            $query->where('section', $request->section);
        }

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
