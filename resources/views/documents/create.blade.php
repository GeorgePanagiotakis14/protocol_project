<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Συμπλήρωση Εγγράφων
        </h2>
    </x-slot>

    @php
        // ✅ Επόμενο σειριακό Α/Α (όπως το θέλεις) - ΔΕΝ γράφεται από χρήστη
        $nextIncomingAa = ((int) (\App\Models\IncomingDocument::max('aa') ?? 0)) + 1;
        $nextOutgoingAa = ((int) (\App\Models\OutgoingDocument::max('aa') ?? 0)) + 1;

        // ✅ Για αναζήτηση (Επιλογή Β): πληκτρολογείς Α/Α -> βρίσκουμε το ID
        $incomingPairs = \App\Models\IncomingDocument::orderBy('protocol_number', 'asc')->get(['id', 'protocol_number']);
        $incomingMap = [];
        foreach ($incomingPairs as $row) {
            $incomingMap[(string) $row->protocol_number] = $row->id;
        }
        $incomingAaList = $incomingPairs->pluck('protocol_number')->values();
    @endphp

    <style>
        .doc-page {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .doc-container {
            width: 1100px;
            margin: 0 auto;
        }

        .doc-wrapper {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 30px;
        }

        .doc-box {
            width: 480px;
            background: #ffffff;
            border: 2px solid #000;
            padding: 25px 30px;
            border-radius: 6px;
        }

        .doc-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 25px;
            text-decoration: underline;
        }

        .doc-box label {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .doc-box input,
        .doc-box textarea {
            width: 100%;
            border: 1px solid #000;
            height: 32px;
            margin-bottom: 15px;
            padding-left: 6px;
        }

        .doc-box textarea {
            height: 90px;
            padding-top: 6px;
        }

        .save-btn {
            width: 100%;
            background: #000;
            color: #fff;
            padding: 10px;
            font-weight: bold;
            border: none;
            margin-top: 10px;
        }

        /* ✅ Α/Α να φαίνεται αλλά να μην αλλάζει */
        .aa-readonly {
            background: #f3f4f6;
            cursor: not-allowed;
        }

        /* ✅ Μικρό βοηθητικό κείμενο */
        .hint {
            font-size: 12px;
            color: #444;
            margin-top: -10px;
            margin-bottom: 12px;
        }
    </style>

    <div class="doc-page">
        <div class="doc-container">

            <div style="text-align: center; margin: 40px 0;">
                <h2 class="fw-bold mb-4" style="font-size:2.2rem;">
                    Συμπλήρωση Εγγράφων
                </h2>
            </div>

            <div class="doc-wrapper">

                <!-- ΕΙΣΕΡΧΟΜΕΝΑ -->
                <div class="doc-box">
                    <div class="doc-title">ΕΙΣΕΡΧΟΜΕΝΑ ΕΓΓΡΑΦΑ</div>

                    <form action="{{ route('incoming.store') }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        <label>Α/Α</label>
                        <input type="number" class="aa-readonly" value="{{ $nextIncomingAa }}" readonly>
                        <input type="hidden" name="protocol_number" value="{{ $nextIncomingAa }}">

                        <label>Ημερομηνία Παραλαβής</label>
                        <input type="date" name="incoming_date" required>

                        <label>Αριθμός Εισερχομένου Εγγράφου</label>
                        <input type="text" name="incoming_protocol" required>

                        <label>Τόπος Έκδοσης</label>
                        <input type="text" name="sender">

                        <label>Αρχή Έκδοσης</label>
                        <input type="text" name="subject">

                        <label>Χρονολογία Εγγράφου</label>
                        <input type="date" name="document_date">

                        <label>Περίληψη</label>
                        <textarea name="summary"></textarea>

                        <label>Φάκελος Αρχείου</label>
                        <input type="text" name="comments">

                        <label>Συνημμένο (PDF)</label>
                        <input type="file" name="attachment" accept="application/pdf" required>

                        <small style="display:block; margin-top:4px; opacity:.7;">
                           Επιτρέπεται μόνο αρχείο PDF (μέχρι 50MB).
                        </small>


                        <button class="save-btn">Αποθήκευση Εισερχομένου</button>
                    </form>
                </div>

                <!-- ΕΞΕΡΧΟΜΕΝΑ -->
                <div class="doc-box">
                    <div class="doc-title">ΕΞΕΡΧΟΜΕΝΑ ΕΓΓΡΑΦΑ</div>

                    <form
                        action="{{ route('outgoing.store') }}" enctype="multipart/form-data"

                        method="POST"
                        id="outgoingForm"
                        data-serial-outgoing-aa="{{ (int) $nextOutgoingAa }}"
                        data-map-aa-to-id='@json($incomingMap)'
                    >
                        @csrf

                        <label>Α/Α</label>
                        <input type="number" id="outgoing_aa_view" class="aa-readonly" value="{{ $nextOutgoingAa }}" readonly>
                        <input type="hidden" name="protocol_number" id="outgoing_aa" value="{{ $nextOutgoingAa }}">

                        <label>Απάντηση σε εισερχόμενο με Α/Α</label>
                        <input
                            type="text"
                            id="reply_to_incoming_aa"
                            list="incoming_aa_list"
                            placeholder="Πληκτρολόγησε Α/Α (π.χ. 12) ή άφησέ το κενό"
                            autocomplete="off"
                        >
                        <datalist id="incoming_aa_list">
                            @foreach ($incomingAaList as $aa)
                                <option value="{{ $aa }}"></option>
                            @endforeach
                        </datalist>

                        <div class="hint">
                            Άφησέ το κενό αν <b>δεν</b> είναι απάντηση. Αν βάλεις Α/Α εισερχομένου, το εξερχόμενο θα πάρει αυτό το Α/Α.
                        </div>

                        <input type="hidden" name="reply_to_incoming_id" id="reply_to_incoming_id" value="">

                        <label>Αρχή στην οποία απευθύνεται</label>
                        <input type="text" name="sender" required>

                        <label>Περίληψη Εξερχομένου Εγγράφου</label>
                        <textarea name="summary"></textarea>

                        <label>Χρονολογία</label>
                        <input type="date" name="document_date">

                        <label>Σχετικοί Αριθμοί</label>
                        <input type="text" name="incoming_document_number">

                        <label>Φάκελος Αρχείου</label>
                        <input type="text" name="incoming_protocol">

                        <label>Παρατηρήσεις</label>
                        <textarea name="comments"></textarea>

                        <label>Συνημμένο (PDF)</label>
                        <input type="file" name="attachment" accept="application/pdf" required>

                         <small style="display:block; margin-top:4px; opacity:.7;">
                           Επιτρέπεται μόνο αρχείο PDF (μέχρι 50MB).
                         </small>
                          @if ($errors->any())
                             <div style="border:1px solid red; padding:8px; margin-bottom:10px; color:red;">
                                {{ $errors->first() }}
                            </div>
                          @endif

                        <button class="save-btn">Αποθήκευση Εξερχομένου</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('outgoingForm');
            const serialOutgoingAa = Number(form.dataset.serialOutgoingAa || 0);

            let mapAaToId = {};
            try {
                mapAaToId = JSON.parse(form.dataset.mapAaToId || '{}');
            } catch (e) {
                mapAaToId = {};
            }

            const aaInput = document.getElementById('reply_to_incoming_aa');
            const hiddenReplyId = document.getElementById('reply_to_incoming_id');

            const outgoingAaHidden = document.getElementById('outgoing_aa');
            const outgoingAaView = document.getElementById('outgoing_aa_view');

            function setOutgoingAa(val) {
                outgoingAaHidden.value = String(val);
                outgoingAaView.value = String(val);
            }

            function applyReplySelection() {
                const typed = (aaInput.value || '').trim();

                if (!typed) {
                    hiddenReplyId.value = '';
                    setOutgoingAa(serialOutgoingAa);
                    return;
                }

                const id = mapAaToId[typed];

                if (id) {
                    hiddenReplyId.value = String(id);
                    setOutgoingAa(typed);
                } else {
                    hiddenReplyId.value = '';
                    setOutgoingAa(serialOutgoingAa);
                }
            }

            aaInput.addEventListener('input', applyReplySelection);

            setOutgoingAa(serialOutgoingAa);
        })();
    </script>

</x-app-layout>
