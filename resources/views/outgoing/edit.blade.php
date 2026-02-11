<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            Επεξεργασία Εξερχόμενου Εγγράφου
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
              action="{{ route('outgoing.update', $document->id) }}"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <label>Α/Α</label><br>
            <input type="text" name="protocol_number"
                   value="{{ $document->protocol_number }}"><br><br>
            <br>

            <label>Αρχή στην οποία απευθύνεται</label><br>
            <input type="text" name="incoming_protocol"
                   value="{{ $document->incoming_protocol }}"><br><br>
            <br>

            <label>Χρονολογία</label><br>
            <input type="date" name="incoming_date"
                   value="{{ $document->incoming_date }}"><br><br>
            <br>

            <label>Περίληψη</label><br>
            <input type="text" name="subject"
                   value="{{ $document->subject }}"><br><br>
            <br>

            <label>Σχετικοί Αριθμοί</label><br>
            <input type="text" name="sender"
                   value="{{ $document->sender }}"><br><br>
            <br>

            <label>Φάκελος Αρχείου</label><br>
            <input type="date" name="document_date"
                   value="{{ $document->document_date }}"><br><br>
            <br>

            <label>Συνημμένο</label><br>
            <input type="file" name="attachment"><br><br>
            <br>
            
            <button type="submit"
                    style="border: 3px solid black; padding: 8px 16px; border-radius: 5px; font-weight: normal;">
                Αποθήκευση
            </button>




        </form>

    </div>

</x-app-layout>
