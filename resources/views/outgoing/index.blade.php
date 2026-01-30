<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Εξερχόμενα Έγγραφα
        </h2>
    </x-slot>

    <div class="card">
        <h2>Εξερχόμενα Έγγραφα</h2>

        <table border="1" width="100%" cellpadding="5">
            <thead>
            <tr>
                <th>Α/Α</th>
                <th>Αρχή στην οποία απευθύνεται</th>
                <th>Περίληψη</th>
                <th>Χρονολογία</th>
                <th>Σχετικοί Αριθμοί</th>
                <th>Φάκελος Αρχείου</th>
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
                </tr>
            @empty
                <tr>
                    <td colspan="6" align="center">Δεν υπάρχουν εγγραφές</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $documents->links() }}
    </div>

</x-app-layout>
