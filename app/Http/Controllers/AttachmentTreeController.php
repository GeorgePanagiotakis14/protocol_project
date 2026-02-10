<?php

namespace App\Http\Controllers;

use App\Models\IncomingDocumentAttachment;
use App\Models\OutgoingDocumentAttachment;

class AttachmentTreeController extends Controller
{
    public function index()
    {
        $incoming = IncomingDocumentAttachment::with('document:id,aa,subject')
            ->get()
            ->map(function ($a) {
                [$year, $type, $date] = $this->parsePath($a->path);

                return [
                    'year' => $year,
                    'type' => 'incoming',
                    'date' => $date,
                    'doc_id' => $a->incoming_document_id,
                    'attachment_id' => $a->id,
                    'name' => $a->original_name ?? basename($a->path),
                    'filename' => basename($a->path),
                    'aa' => optional($a->document)->aa,
                    'subject' => optional($a->document)->subject,
                ];
            });

        $outgoing = OutgoingDocumentAttachment::with('document:id,aa,subject')
            ->get()
            ->map(function ($a) {
                [$year, $type, $date] = $this->parsePath($a->path);

                return [
                    'year' => $year,
                    'type' => 'outgoing',
                    'date' => $date,
                    'doc_id' => $a->outgoing_document_id,
                    'attachment_id' => $a->id,
                    'name' => $a->original_name ?? basename($a->path),
                    'filename' => basename($a->path),
                    'aa' => optional($a->document)->aa,
                    'subject' => optional($a->document)->subject,
                ];
            });

        $items = $incoming->concat($outgoing);

        $tree = $items->groupBy(['year', 'type', 'date']);

        // Ταξινόμηση ετών (π.χ. 2026, 2025, ...)
        $tree = $tree->sortKeysDesc();

        // Για κάθε έτος και κάθε τύπο, ταξινόμησε τις ημερομηνίες
        $tree = $tree->map(function ($types) {
            return $types->map(function ($dates) {
                // Αν θες ΠΙΟ ΠΡΟΣΦΑΤΑ ΠΡΩΤΑ:
                return $dates->sortKeysDesc();

                // Αν θες ΠΑΛΙΟΤΕΡΑ ΠΡΩΤΑ, βάλε:
                // return $dates->sortKeys();
            });
        });

        return view('attachments.tree', compact('tree'));
    }

    private function parsePath(?string $path): array
    {
        // Αναμένουμε: 2026/incoming/2026-02-14/filename.pdf
        if (!$path) return ['Άλλα', 'unknown', 'Χωρίς ημερομηνία'];

        $parts = explode('/', $path);

        $year = $parts[0] ?? 'Άλλα';
        $type = $parts[1] ?? 'unknown';
        $date = $parts[2] ?? 'Χωρίς ημερομηνία';

        // fallback αν δεν ξεκινάει με έτος
        if (!preg_match('/^\d{4}$/', (string)$year)) {
            return ['Άλλα', $type, $date];
        }

        return [$year, $type, $date];
    }
}
