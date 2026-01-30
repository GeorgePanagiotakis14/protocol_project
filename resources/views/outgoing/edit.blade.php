<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Επεξεργασία Εξερχομένου
        </h2>
    </x-slot>

    <div class="card">
        <h2>Επεξεργασία Εξερχομένου</h2>

        <form method="POST" action="{{ route('outgoing.update', $document->id) }}">
            @csrf
            @method('PUT')

            <input name="protocol_number" value="{{ $document->protocol_number }}"><br><br>
            <input name="incoming_protocol" value="{{ $document->incoming_protocol }}"><br><br>
            <input type="date" name="incoming_date" value="{{ $document->incoming_date }}"><br><br>
            <input name="subject" value="{{ $document->subject }}"><br><br>
            <input name="sender" value="{{ $document->sender }}"><br><br>
            <input type="date" name="document_date" value="{{ $document->document_date }}"><br><br>

            <input name="incoming_document_number" value="{{ $document->incoming_document_number }}"><br><br>

            <textarea name="summary">{{ $document->summary }}</textarea><br><br>
            <textarea name="comments">{{ $document->comments }}</textarea><br><br>

            <button type="submit">Αποθήκευση</button>
        </form>
    </div>

</x-app-layout>
