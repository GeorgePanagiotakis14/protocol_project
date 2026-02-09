<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            Εισερχόμενα Έγγραφα
        </h2>
    </x-slot>

    <div class="card">

        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>Α/Α</th>
                    <th>Ημερομηνία Παραλαβής</th>
                    <th>Αριθμός Πρωτοκόλλου</th>
                    <th>Τόπος που εκδόθηκε</th>
                    <th>Αρχή που το εξέδωσε</th>
                    <th>Χρονολογία εγγράφου</th>
                    <th>Περίληψη</th>
                    <th>Φάκελος Αρχείου</th>
                    <th>Συνημμένο</th>
                    @auth
                    @if(auth()->user()->isAdmin())
                    <th>Ενέργειες</th>
                    @endif
                    @endauth

                </tr>
            </thead>

            <tbody>
                @forelse($documents as $doc)
                    <tr
                        @if(($doc->outgoing_replies_count ?? 0) > 0)
                            style="background-color:#f5f5dc;"
                        @endif
                    >
                        <td>{{ $doc->aa }}</td>
                        <td>{{ $doc->incoming_date }}</td>
                        <td>{{ $doc->protocol_number }}</td>
                        <td>{{ $doc->sender }}</td>
                        <td>{{ $doc->subject }}</td>
                        <td>{{ $doc->document_date }}</td>
                        <td>{{ $doc->summary }}</td>
                        <td>{{ $doc->comments }}</td>
                        <td style="text-align:center; white-space:nowrap;">
                            @if($doc->attachment_path)

                                <a href="{{ route('incoming.attachments.index', $doc->id) }}"
                                   style="color:#2563eb; font-weight:600; text-decoration:underline;">
                                    Προβολή
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        
                        @auth
                           @if(auth()->user()->isAdmin())
                             <td style="text-align:center;">
                                  <a href="{{ route('incoming.edit', $doc->id) }}"
                                   style="color:#16a34a; font-weight:600; text-decoration:underline;">
                                   Επεξεργασία
                                  </a>
                              </td>
                           @endif
                        @endauth

                    </tr>
                @empty
                    <tr>
                        <td colspan="9" align="center">Δεν υπάρχουν εγγραφές</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $documents->links() }}
    </div>

</x-app-layout>
