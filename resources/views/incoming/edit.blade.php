<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Επεξεργασία Εισερχομένου
        </h2>
    </x-slot>

    <div class="card">
    

        <form method="POST" action="{{ route('incoming.update', $document->id) }}">
            @csrf
            @method('PUT')
            <label>Αριθμός Πρωτοκόλλου</label><br>
            <input name="protocol_number" value="{{ $document->protocol_number }}"> <br><br>

            <label>Εισερχόμενο Πρωτόκολλο</label><br>
            <input name="incoming_protocol" value="{{ $document->incoming_protocol }}"><br><br>

            <label>Ημερομηνία Παραλαβής</label><br>
            <input type="date" name="incoming_date" value="{{ $document->incoming_date }}"><br><br>

            <label>Θέμα</label><br>
            <input name="subject" value="{{ $document->subject }}"><br><br>

            <label>Αποστολέας</label><br>
            <input name="sender" value="{{ $document->sender }}"><br><br>

            <label>Ημερομηνία Εγγράφου</label><br>
            <input type="date" name="document_date" value="{{ $document->document_date }}"><br><br>

            <label>Περίληψη</label><br>
            <textarea name="summary">{{ $document->summary }}</textarea><br><br>

            <label>Σχόλια</label><br>
            <textarea name="comments">{{ $document->comments }}</textarea><br><br>

            <button type="submit">Αποθήκευση</button>
        </form>
    </div>

</x-app-layout>
