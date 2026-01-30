<?php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('audit_log')) {
    function audit_log(string $section, string $action, int $documentId): void
    {
        AuditLog::create([
            'user_id'     => Auth::user()?->id,
            'section'     => $section,
            'action'      => $action,
            'document_id' => $documentId,
        ]);
    }
}
