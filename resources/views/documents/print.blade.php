<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Εκτύπωση Πρωτοκόλλων</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width:100%; border-collapse: collapse; font-size:12px; }
        th, td { border:1px solid #000; padding:4px; vertical-align:top; }
        th { background-color:#f0f0f0; }
        .divider-col { width:10px; background:#1f6feb; }
    </style>
</head>
<body onload="window.print()">

<h3>
    Πρωτόκολλα από {{ $from->format('d/m/Y') }} έως {{ $to->format('d/m/Y') }}
</h3>

<table>
    <thead>
        <tr>
            <!-- Εισερχόμενα -->
            <th>Α/Α</th>
            <th>Ημερομηνία Παραλαβής</th>
            <th>Αριθμός Πρωτοκόλλου</th>
            <th>Θέμα</th>
            <th>Τόπος που εκδόθηκε</th>
            <th>Φάκελος Αρχείου</th>
            <th>Περίληψη</th>
            <th>Συνημμένο</th>

            <th class="divider-col"></th>

            <!-- Εξερχόμενα -->
            <th>Αρχή στην οποία απευθύνεται</th>
            <th>Περίληψη</th>
            <th>Χρονολογία</th>
            <th>Σχετικοί Αριθμοί</th>
            <th>Φάκελος Αρχείου</th>
            <th>Παρατηρήσεις</th>
            <th>Συνημμένο</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documents as $doc)
            @php
                $incoming = $doc['incoming'] ?? null;
                $outgoingList = $doc['outgoing'] ?? collect();
                $rowspan = max(1, count($outgoingList));
            @endphp

            @for($i = 0; $i < $rowspan; $i++)
                <tr>
                    {{-- Εισερχόμενα --}}
                    @if($i === 0)
                        <td rowspan="{{ $rowspan }}">{{ $doc['display_aa'] ?? '' }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $incoming->incoming_date ?? '' }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $incoming->protocol_number ?? '' }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $incoming->subject ?? '' }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $incoming->sender ?? '' }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $incoming->comments ?? '' }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $incoming->summary ?? '' }}</td>
                        <td rowspan="{{ $rowspan }}" style="text-align:center;">
                            {{ $incoming->attachment_path ? 'Ναι' : '—' }}
                        </td>
                    @endif

                    <td class="divider-col"></td>

                    {{-- Εξερχόμενα --}}
                    @php $out = $outgoingList[$i] ?? null; @endphp
                    <td>{{ $out ? $out->sender : '' }}</td>
                    <td>{{ $out ? $out->summary : '' }}</td>
                    <td>{{ $out ? $out->document_date : '' }}</td>
                    <td>{{ $out ? $out->incoming_document_number : '' }}</td>
                    <td>{{ $out ? $out->incoming_protocol : '' }}</td>
                    <td>{{ $out ? $out->comments : '' }}</td>
                    <td style="text-align:center;">
                        {{ $out ? ($out->attachment_path ? 'Ναι' : '—') : '—' }}
                    </td>

                </tr>
            @endfor
        @endforeach
    </tbody>
</table>

</body>
</html>
