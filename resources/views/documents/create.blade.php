<x-app-layout>

    
@php
    // ✅ Επιλεγμένο έτος από middleware (ή τρέχον αν δεν υπάρχει)
    $selectedYear = isset($selectedYear) ? (int) $selectedYear : (int) now()->year;

    // ✅ ΚΟΙΝΟΣ επόμενος αριθμός πρωτοκόλλου (για εισερχόμενο + ανεξάρτητο εξερχόμενο) ΑΝΑ ΕΤΟΣ
    $current = (int) (DB::table('protocol_counters')->where('year', $selectedYear)->value('current') ?? 0);
    $nextAa = $current + 1;

    // ✅ Και οι 2 φόρμες να δείχνουν το ΙΔΙΟ επόμενο Α/Α
    $nextIncomingAa = $nextAa;
    $nextOutgoingAa = $nextAa;

    // ✅ Για αναζήτηση (Επιλογή Β): πληκτρολογείς Α/Α -> βρίσκουμε το ID (ΜΟΝΟ του επιλεγμένου έτους)
    $incomingPairs = \App\Models\IncomingDocument::where('protocol_year', $selectedYear)
        ->orderBy('protocol_number', 'asc')
        ->get(['id', 'protocol_number']);

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

        /* ✅ Wrapper για file input + κουμπί ώστε το validation bubble να εμφανίζεται σε σωστό σημείο */
        .file-picker-wrap {
            position: relative;
            display: inline-block;
            width: 100%;
            margin-bottom: 10px;
        }

        /* ✅ Κρύβει το file input χωρίς display:none (ώστε να δουλεύει required + native bubble) */
        .hidden-file-input {
            position: absolute;
            left: 0;
            top: 0;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
    </style>

    <div class="doc-page">
        <div class="doc-container">

            <div style="text-align: center; margin: 40px 0;">
                <h2 class="fw-bold mb-4" style="font-size:2.2rem;">
                    Συμπλήρωση Εγγράφων
                </h2>
            </div>

            {{-- ✅ ΕΠΙΛΟΓΗ ΕΤΟΥΣ --}}
            @include('partials.protocol-year-selector')

            <div class="doc-wrapper">

                <!-- ΕΙΣΕΡΧΟΜΕΝΑ -->
                <div class="doc-box">
                    <div class="doc-title">ΕΙΣΕΡΧΟΜΕΝΑ ΕΓΓΡΑΦΑ</div>

                    <form id="incomingForm" action="{{ route('incoming.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <label>Α/Α</label>
                        <input type="number" class="aa-readonly" value="{{ $nextIncomingAa }}" readonly>
                        <input type="hidden" name="protocol_number" value="{{ $nextIncomingAa }}">

                        <label>Ημερομηνία Παραλαβής</label>
                        <input type="date" name="incoming_date" required>

                        <label>Αριθμός Εισερχομένου Εγγράφου</label>
                        <input type="text" name="incoming_protocol" required>

                        <label>Τόπος που εκδόθηκε</label>
                        <input type="text" name="sender" required>

                        <label>Αρχή που το έχει εκδόσει</label>
                        <input type="text" name="subject" required>

                        <label>Χρονολογία Εγγράφου</label>
                        <input type="date" name="document_date" required>

                        <label>Περίληψη</label>
                        <textarea name="summary" required></textarea>

                        <label>Φάκελος Αρχείου</label>
                        <input type="text" name="comments" required>

                        <label>Συνημμένα (PDF)</label>

                        <div class="file-picker-wrap">
                            <input
                                type="file"
                                name="attachments[]"
                                id="incoming_attachments"
                                accept="application/pdf"
                                multiple
                                required
                                class="hidden-file-input"
                            >

                            <button
                                type="button"
                                id="incoming_add_files_btn"
                                style="padding:6px 12px; border-radius:6px; background:#16a34a; color:#fff; border:none; cursor:pointer;"
                            >
                                Προσθήκη αρχείων
                            </button>
                        </div>

                        <small style="display:block; margin-top:6px; opacity:.7;">
                            Επιτρέπεται μόνο αρχείο PDF (μέχρι 50MB).
                        </small>

                        <ul id="incoming_attachments_list" style="margin-top:8px; padding-left:18px;"></ul>
                        @if ($errors->getBag('incoming')->any())
                         <div style="border:1px solid red; padding:8px; margin-bottom:10px; color:red;">
                            {{ $errors->getBag('incoming')->first() }}
                         </div>
                        @endif

                        <button class="save-btn">Αποθήκευση Εισερχομένου</button>
                    </form>
                </div>

                <!-- ΕΞΕΡΧΟΜΕΝΑ -->
                <div class="doc-box">
                    <div class="doc-title">ΕΞΕΡΧΟΜΕΝΑ ΕΓΓΡΑΦΑ</div>

                    <form
                        action="{{ route('outgoing.store') }}"
                        enctype="multipart/form-data"
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
                        <textarea name="summary" required></textarea>

                        <label>Χρονολογία</label>
                        <input type="date" name="document_date" required>

                        <label>Σχετικοί Αριθμοί</label>
                        <input type="text" name="incoming_document_number" required>

                        <label>Φάκελος Αρχείου</label>
                        <input type="text" name="incoming_protocol" required>

                        <label>Παρατηρήσεις</label>
                        <textarea name="comments" required></textarea>

                        <label>Συνημμένα (PDF)</label>

                        <div class="file-picker-wrap">
                            <input
                                type="file"
                                name="attachments[]"
                                id="outgoing_attachments"
                                accept="application/pdf"
                                multiple
                                required
                                class="hidden-file-input"
                            />

                            <button
                                type="button"
                                id="outgoing_add_files_btn"
                                style="padding:4px 10px; border-radius:6px; background:#16a34a; color:#fff; border:none; cursor:pointer;"
                            >
                                Προσθήκη αρχείων
                            </button>
                        </div>

                        <small style="display:block; margin-top:6px; opacity:.7;">
                            Επιτρέπεται μόνο αρχείο PDF (μέχρι 50MB).
                        </small>

                        <ul id="outgoing_attachments_list" style="margin-top:8px; padding-left:18px;"></ul>

                       @if ($errors->getBag('outgoing')->any())
                        <div style="border:1px solid red; padding:8px; margin-bottom:10px; color:red;">
                          {{ $errors->getBag('outgoing')->first() }}
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
            const input = document.getElementById('incoming_attachments');
            const list  = document.getElementById('incoming_attachments_list');
            const btn   = document.getElementById('incoming_add_files_btn');

            if (!input || !list || !btn) return;

            const dt = new DataTransfer();

            function fileKey(file) {
                return `${file.name}__${file.size}__${file.lastModified}`;
            }

            function syncInputFiles() {
                input.files = dt.files;
            }

            function renderList() {
                list.innerHTML = '';

                const files = Array.from(dt.files);
                if (files.length === 0) return;

                files.forEach((file, index) => {
                    const li = document.createElement('li');

                    const nameSpan = document.createElement('span');
                    nameSpan.textContent = file.name;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.textContent = 'Αφαίρεση';
                    removeBtn.style.marginLeft = '10px';
                    removeBtn.style.borderRadius = '4px';
                    removeBtn.style.padding = '2px 6px';
                    removeBtn.style.background = '#dc2626';
                    removeBtn.style.color = '#fff';
                    removeBtn.style.border = 'none';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.style.lineHeight = '1';

                    removeBtn.addEventListener('click', () => {
                        const rebuild = new DataTransfer();
                        Array.from(dt.files).forEach((f, i) => {
                            if (i !== index) rebuild.items.add(f);
                        });

                        while (dt.items.length) dt.items.remove(0);
                        Array.from(rebuild.files).forEach(f => dt.items.add(f));

                        syncInputFiles();
                        renderList();
                    });

                    li.appendChild(nameSpan);
                    li.appendChild(removeBtn);
                    list.appendChild(li);
                });
            }

            btn.addEventListener('click', () => input.click());

            input.addEventListener('change', () => {
                const selected = Array.from(input.files || []);
                if (selected.length === 0) return;

                const existingKeys = new Set(Array.from(dt.files).map(fileKey));

                selected.forEach((file) => {
                    if (file.type !== 'application/pdf') return;
                    const key = fileKey(file);
                    if (!existingKeys.has(key)) {
                        dt.items.add(file);
                        existingKeys.add(key);
                    }
                });

                syncInputFiles();
                renderList();
            });

            renderList();

            // ✅ Extra-safe: επειδή το file input είναι hidden, δείχνουμε σίγουρα bubble όταν λείπει
            const form = document.getElementById('incomingForm');
            if (form) {
                form.addEventListener('submit', (e) => {
                    if (!input.files || input.files.length === 0) {
                        e.preventDefault();
                        input.reportValidity();
                    }
                });
            }
        })();
    </script>

    <script>
        (function () {
            const input = document.getElementById('outgoing_attachments');
            const list  = document.getElementById('outgoing_attachments_list');
            const btn   = document.getElementById('outgoing_add_files_btn');

            if (!input || !list || !btn) return;

            const dt = new DataTransfer();

            function fileKey(file) {
                return `${file.name}__${file.size}__${file.lastModified}`;
            }

            function syncInputFiles() {
                input.files = dt.files;
            }

            function renderList() {
                list.innerHTML = '';

                const files = Array.from(dt.files);
                if (files.length === 0) return;

                files.forEach((file, index) => {
                    const li = document.createElement('li');

                    const nameSpan = document.createElement('span');
                    nameSpan.textContent = file.name;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.textContent = 'Αφαίρεση';
                    removeBtn.style.marginLeft = '10px';
                    removeBtn.style.padding = '2px 6px';
                    removeBtn.style.borderRadius = '4px';
                    removeBtn.style.background = '#dc2626';
                    removeBtn.style.color = '#fff';
                    removeBtn.style.border = 'none';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.style.lineHeight = '1';

                    removeBtn.addEventListener('click', () => {
                        const rebuild = new DataTransfer();
                        Array.from(dt.files).forEach((f, i) => {
                            if (i !== index) rebuild.items.add(f);
                        });

                        while (dt.items.length) dt.items.remove(0);
                        Array.from(rebuild.files).forEach(f => dt.items.add(f));

                        syncInputFiles();
                        renderList();
                    });

                    li.appendChild(nameSpan);
                    li.appendChild(removeBtn);
                    list.appendChild(li);
                });
            }

            btn.addEventListener('click', () => input.click());

            input.addEventListener('change', () => {
                const selected = Array.from(input.files || []);
                if (selected.length === 0) return;

                const existingKeys = new Set(Array.from(dt.files).map(fileKey));

                selected.forEach((file) => {
                    if (file.type !== 'application/pdf') return;

                    const key = fileKey(file);
                    if (!existingKeys.has(key)) {
                        dt.items.add(file);
                        existingKeys.add(key);
                    }
                });

                syncInputFiles();
                renderList();
            });

            renderList();

            // ✅ Extra-safe bubble για required file
            const form = document.getElementById('outgoingForm');
            if (form) {
                form.addEventListener('submit', (e) => {
                    if (!input.files || input.files.length === 0) {
                        e.preventDefault();
                        input.reportValidity();
                    }
                });
            }
        })();
    </script>

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

