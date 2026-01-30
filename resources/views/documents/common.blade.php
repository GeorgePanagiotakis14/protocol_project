<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Κοινή Προβολή Εγγράφων
        </h2>
    </x-slot>

    <style>
        .proto-wrap { padding: 18px 0; overflow-x: auto; }
        table.proto {
            border-collapse: collapse;
            min-width: 1400px;
            width: 100%;
            background: #fff;
            font-size: 13px;
        }
        table.proto th, table.proto td {
            border: 1px dotted #000;
            padding: 8px 10px;
            vertical-align: top;
            white-space: normal;
            word-break: break-word;
        }
        table.proto th {
            font-weight: 700;
            text-align: center;
            background: #f8fafc;
        }
        .divider-col {
            width: 14px;
            min-width: 14px;
            background: #1f6feb; /* “μπλε” σαν το excel bar */
            border-left: 0;
            border-right: 0;
            padding: 0 !important;
        }
        .num { text-align: center; white-space: nowrap; }
        .date { text-align: center; white-space: nowrap; }
    </style>

    <div class="proto-wrap">
        <table class="proto">
            <thead>
                <tr>
                    {{-- INCOMING --}}
                    <th>Α/Α</th>
                    <th>Ημερομηνία παραλαβής</th>
                    <th>Αριθμός εισερχομένου εγγράφου</th>
                    <th>Τόπος που εκδόθηκε</th>
                    <th>Αρχή που το έχει εκδώσει</th>
                    <th>Χρονολογία εγγράφου</th>
                    <th>Περίληψη</th>
                    <th>Φάκελος αρχείου</th>

                    <th class="divider-col"></th>

                    {{-- OUTGOING --}}
                    <th>Αρχή στην οποία απευθύνεται</th>
                    <th>Περίληψη εξερχομένου εγγράφου</th>
                    <th>Χρονολογία</th>
                    <th>Σχετικοί αριθμοί</th>
                    <th>Φάκελος αρχείου</th>
                    <th>Παρατηρήσεις</th>
                </tr>
            </thead>

            <tbody>
                @forelse($incomingGroups as $incoming)
                    @php
                        $replies = $incoming->outgoingReplies ?? collect();
                        $rowspan = max(1, $replies->count());
                    @endphp

                    @foreach($replies as $idx => $out)
                        <tr>
                            {{-- Incoming columns: εμφανίζονται 1 φορά με rowspan --}}
                            @if($idx === 0)
                                <td class="num" rowspan="{{ $rowspan }}">{{ $incoming->aa }}</td>
                                <td class="date" rowspan="{{ $rowspan }}">{{ $incoming->incoming_date }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $incoming->incoming_protocol }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $incoming->sender }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $incoming->subject }}</td>
                                <td class="date" rowspan="{{ $rowspan }}">{{ $incoming->document_date }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $incoming->summary }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $incoming->comments }}</td>

                                <td class="divider-col" rowspan="{{ $rowspan }}"></td>
                            @endif

                            {{-- Outgoing columns: 1 γραμμή ανά εξερχόμενο --}}
                            <td>{{ $out->sender }}</td>
                            <td>{{ $out->summary }}</td>
                            <td class="date">{{ $out->document_date }}</td>
                            <td>{{ $out->incoming_document_number }}</td>
                            <td>{{ $out->incoming_protocol }}</td>
                            <td>{{ $out->comments }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="15" style="text-align:center;">Δεν υπάρχουν κοινές εγγραφές.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-app-layout>
