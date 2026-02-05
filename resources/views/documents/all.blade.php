<x-app-layout>
    <h1 style="margin-bottom:20px; font-size:32px; font-weight:bold;">Όλα τα Πρωτόκολλα</h1>

    <style>
        .divider-col {
            width: 14px;
            min-width: 14px;
            background: #1f6feb; /* μπλε κάθετη γραμμή */
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: top;
        }
        td a {
            color: #2563eb;
            font-weight: 600;
            text-decoration: underline;
        }
    </style>

    <div class="card">
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

                    <th class="divider-col"></th> <!-- μπλε διαχωριστική στήλη -->

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
                @foreach($documents as $docGroup)
                    @php
                        $incoming = $docGroup['incoming'] ?? null;
                        $outgoingList = $docGroup['outgoing'] ?? collect();
                        $rowspan = max(1, $outgoingList->count());
                        $displayAA = $docGroup['display_aa'];
                    @endphp

                    @for($i = 0; $i < $rowspan; $i++)
                        <tr>
                            {{-- Αριστερή πλευρά: Εισερχόμενα με rowspan --}}
                            @if($i === 0)
                                @if($incoming)
                                    <td rowspan="{{ $rowspan }}">{{ $displayAA }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->incoming_date }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->protocol_number }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->subject }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->sender }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->comments }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->summary }}</td>
                                    <td rowspan="{{ $rowspan }}" style="text-align:center; white-space:nowrap;">
                                        @if($incoming->attachment_path)
                                            <a href="{{ route('incoming.attachment', $incoming->id) }}" target="_blank">Προβολή</a>
                                        @else
                                            —

                                        @endif
                                    </td>
                                @else
                                    {{-- Εξερχόμενο χωρίς εισερχόμενο: Α/Α + κενά --}}
                                    <td>{{ $displayAA }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif
                            @endif

                            {{-- Μπλε διαχωριστική στήλη --}}
                            <td class="divider-col"></td>

                            {{-- Δεξιά πλευρά: Εξερχόμενα --}}
                            @if(isset($outgoingList[$i]))
                                @php $out = $outgoingList[$i]; @endphp
                                <td>{{ $out->sender }}</td>
                                <td>{{ $out->summary }}</td>
                                <td>{{ $out->document_date }}</td>
                                <td>{{ $out->incoming_document_number }}</td>
                                <td>{{ $out->incoming_protocol }}</td>
                                <td>{{ $out->comments ?? '' }}</td>
                                <td style="text-align:center; white-space:nowrap;">
                                    @if($out->attachment_path)
                                        <a href="{{ route('outgoing.attachment', $out->id) }}" target="_blank">Προβολή</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            @else
                                {{-- Κενά δεξιά αν δεν υπάρχει εξερχόμενο --}}
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
