<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

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
