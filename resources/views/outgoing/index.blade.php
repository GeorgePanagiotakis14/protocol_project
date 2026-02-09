<x-app-layout>

    <x-slot name="header">
        <h2 class="fw-bold mb-4" style="text-align: center; font-size:2.2rem; text-3xl text-gray-800 leading-tight font-size:2.2rem;">
            Εξερχόμενα Έγγραφα
        </h2>
    </x-slot>

    <div class="card">

        <table border="1" width="100%" cellpadding="5">
            <thead>
            <tr>
                <th>Α/Α</th>
                <th>Αρχή στην οποία απευθύνεται</th>
                <th>Περίληψη</th>
                <th>Χρονολογία</th>
                <th>Σχετικοί Αριθμοί</th>
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
                    @if(!is_null($doc->reply_to_incoming_id))
                        style="background-color:#f5f5dc;"
                    @endif
                >
                    <td>{{ $doc->aa }}</td>
                    <td>{{ $doc->sender }}</td>
                    <td>{{ $doc->summary }}</td>
                    <td>{{ $doc->document_date }}</td>
                    <td>{{ $doc->incoming_document_number }}</td>
                    <td>{{ $doc->incoming_protocol }}</td>

                    <td style="text-align:center; white-space:nowrap;">
                        @if($doc->attachment_path)
                            <a href="{{ route('outgoing.attachments.index', $doc->id) }}"

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
                                  <a href="{{ route('outgoing.edit', $doc->id) }}"
                                   style="color:#16a34a; font-weight:600; text-decoration:underline;">
                                   Επεξεργασία
                                  </a>
                              </td>
                           @endif
                        @endauth

                    
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center">Δεν υπάρχουν εγγραφές</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $documents->links() }}
    </div>

</x-app-layout>
