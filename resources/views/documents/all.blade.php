<x-app-layout>
    <h1 style="margin-bottom:20px; font-size:20px; font-weight:bold;">
        Όλα τα Πρωτόκολλα
    </h1>

    {{-- ΦΙΛΤΡΟ + ΕΚΤΥΠΩΣΗ --}}
    <form method="GET"
          action="{{ route('documents.print') }}"
          target="_blank"
          style="margin-bottom:20px; padding:10px; background:#f3f4f6; border-radius:6px;">

        <label>
            Από:
            <input type="date" name="from" required>
        </label>

        <label style="margin-left:10px;">
            Έως:
            <input type="date" name="to" required>
        </label>

        <button type="submit" name="action" value="print" style="margin-left:15px;">
            🖨️ Εκτύπωση
        </button>
    </form>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: top;
            font-size: 12px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            body {
                font-size: 11px;
            }

            table {
                width: 100%;
                table-layout: fixed;
            }

            th, td {
                padding: 4px;
                font-size: 10px;
                word-wrap: break-word;
            }

            .divider-col {
                width: 6px;
                min-width: 6px;
                background-color: #1f6feb; /* μπλε */
                padding: 0;
            }

            th.divider-col {
                background-color: #1f6feb;
                border: none;
            }

            td.divider-col {
                border: none;
            }


            a {
                text-decoration: none;
                color: black;
            }
        }
    </style>


    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Α/Α</th>
                    <th>Ημερομηνία Παραλαβής</th>
                    <th>Αριθμός Πρωτοκόλλου</th>
                    <th>Θέμα</th>
                    <th>Τόπος που εκδόθηκε</th>
                    <th>Φάκελος Αρχείου</th>
                    <th>Περίληψη</th>
                    <th>Συνημμένο</th>

                    <th class="divider-col"></th>

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
                            @if($i === 0)
                                @if($incoming)
                                    <td rowspan="{{ $rowspan }}">{{ $displayAA }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->incoming_date }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->protocol_number }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->subject }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->sender }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->comments }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $incoming->summary }}</td>
                                    <td rowspan="{{ $rowspan }}" style="text-align:center;">
                                        @if($incoming->attachment_path)
                                        <a href="{{ route('incoming.attachments.index', $incoming->id) }}">Προβολή</a>
                                        
                                        @endif

                                    </td>
                                @else
                                    <td>{{ $displayAA }}</td>
                                    <td colspan="7"></td>
                                @endif
                            @endif

                            <td class="divider-col"></td>

                            @if(isset($outgoingList[$i]))
                                @php $out = $outgoingList[$i]; @endphp
                                <td>{{ $out->sender }}</td>
                                <td>{{ $out->summary }}</td>
                                <td>{{ $out->document_date }}</td>
                                <td>{{ $out->incoming_document_number }}</td>
                                <td>{{ $out->incoming_protocol }}</td>
                                <td>{{ $out->comments }}</td>
                                <td style="text-align:center;">
                                    @if($out->attachment_path)
                                   <a href="{{ route('outgoing.attachments.index', $out->id) }}">Προβολή</a>
                                    
                                    @endif

                                </td>
                            @else
                                <td colspan="7"></td>
                            @endif
                        </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
