<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            Επεξεργασία Εισερχόμενου Εγγράφου
        </h2>
    </x-slot>

    <div class="card">

        {{-- Εμφάνιση σφαλμάτων --}}
        @if ($errors->any())
            <div style="color:red; margin-bottom:10px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('incoming.update', $document->id) }}"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <label>Α/Α</label><br>
            <input type="text" name="protocol_number"
                   value="{{ old('protocol_number', $document->protocol_number) }}">
            <br><br>

            <label>Ημερομηνία Παραλαβής</label><br>
            <input type="date" name="incoming_date"
                   value="{{ old('incoming_date', $document->incoming_date) }}">
            <br><br>

            <label>Αριθμός Πρωτοκόλλου</label><br>
            <input type="text" name="incoming_protocol"
                   value="{{ old('incoming_protocol', $document->incoming_protocol) }}">
            <br><br>

            <label>Τόπος που εκδόθηκε</label><br>
            <input type="text" name="subject"
                   value="{{ old('subject', $document->subject) }}">
            <br><br>

            <label>Αρχή που το εξέδωσε</label><br>
            <input type="text" name="sender"
                   value="{{ old('sender', $document->sender) }}">
            <br><br>

            <label>Χρονολογία εγγράφου</label><br>
            <input type="date" name="document_date"
                   value="{{ old('document_date', $document->document_date) }}">
            <br><br>

            <label>Περίληψη</label><br>
            <textarea name="summary" rows="4">{{ old('summary', $document->summary) }}</textarea>
            <br><br>

            <label>Φάκελος Αρχείου</label><br>
            <textarea name="comments" rows="3">{{ old('comments', $document->comments) }}</textarea>
            <br><br>

            <label>Συνημμένο</label><br>
            <input type="file" name="attachment" accept="application/pdf">
            <br><br>

            <button type="submit"
                    style="border: 3px solid black; padding: 8px 16px; border-radius: 5px; font-weight: normal;">
                Αποθήκευση
            </button>

        </form>

    </div>

</x-app-layout>
