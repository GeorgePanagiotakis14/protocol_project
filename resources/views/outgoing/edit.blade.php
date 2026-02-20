<x-app-layout>

    @php
        $selectedYear = (int) ($document->protocol_year ?? request('year') ?? now()->year);
    @endphp

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

        {{-- ✅ MAIN FORM --}}
        <form method="POST"
              action="{{ route('outgoing.update', $document->id) }}"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <label>Α/Α</label><br>
            <input type="text" name="protocol_number"
                   value="{{ $document->protocol_number }}"
                    readonly
                    style="background:#f3f3f3; cursor:not-allowed;"><br><br>
            <br>

            <label>Αρχή στην οποία απευθύνεται</label><br>
            <input type="text" name="sender"
                   value="{{ $document->sender }}"><br><br>
            <br>

            <label>Περίληψη εξερχομένου εγγράφου</label><br>
            <input type="text" name="summary"
                   value="{{ $document->summary }}"><br><br>
            <br>

            <label>Χρονολογία</label><br>
            <input type="date" name="document_date"
                  value="{{ $document->document_date }}"><br><br>


            <label>Σχετικοί Αριθμοί</label><br>
            <input type="text" name="incoming_document_number"
                   value="{{ $document->incoming_document_number }}"><br><br>
            <br>

            <label>Φάκελος Αρχείου</label><br>
            <input type="text" name="incoming_protocol"
                   value="{{ $document->incoming_protocol}}"><br><br>
            <br>

             <label>Παρατηρήσεις</label><br>
            <input type="text" name="comments"
                   value="{{ $document->comments}}"><br><br>
            <br>

            {{-- ✅ Υπάρχοντα PDF --}}
            @if($document->attachments && $document->attachments->count())
                <div style="margin: 10px 0 14px; padding:10px; border:1px solid #ccc; border-radius:8px;">
                    <div style="font-weight:700; margin-bottom:8px;">Υπάρχοντα PDF:</div>

                    <ul style="padding-left:18px;">
                        @foreach($document->attachments as $att)
                            <li style="margin-bottom:6px;">
                                <a href="{{ route('outgoing.attachments.view', [$document->id, $att->id]) }}" target="_blank">
                                    {{ $att->original_name ?? basename($att->path) }}
                                </a>

                                <button type="button"
                                    onclick="if(confirm('Να διαγραφεί το συνημμένο;')){ document.getElementById('del-out-att-{{ $att->id }}').submit(); }"
                                    style="display:inline-block; margin-left:10px; padding:2px 8px; border-radius:6px; background:#dc2626; color:#fff; border:none; cursor:pointer;">
                                    Διαγραφή
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <label>Συνημμένα (PDF)</label><br>

            <input
                type="file"
                name="attachments[]"
                id="outgoing_attachments"
                accept="application/pdf"
                multiple
                style="display:none"
            />

            <button
                type="button"
                id="outgoing_add_files_btn"
                style="padding:6px 12px; border-radius:6px; background:#16a34a; color:#fff; border:none; cursor:pointer;"
            >
                Προσθήκη αρχείων
            </button>

            <small style="display:block; margin-top:6px; opacity:.7;">
                Επιτρέπεται μόνο αρχείο PDF (μέχρι 50MB).
            </small>

            <ul id="outgoing_attachments_list" style="margin-top:8px; padding-left:18px;"></ul>

            <br>

            <button type="submit"
                    style="border: 3px solid black; padding: 8px 16px; border-radius: 5px; font-weight: normal;">
                Αποθήκευση
            </button>

        </form>

        {{-- ✅ Hidden DELETE forms (ΕΞΩ από το main form) --}}
        @if($document->attachments && $document->attachments->count())
            @foreach($document->attachments as $att)
                <form id="del-out-att-{{ $att->id }}"
                      method="POST"
                      action="{{ route('outgoing.attachments.destroy', [$document->id, $att->id]) }}?return={{ urlencode(url()->full()) }}"
                      style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif

    </div>

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
        })();
    </script>
    
    <script>
(function () {
    // Key ανά document για να μην μπλέκουν μεταξύ τους
    const docId = @json($document->id);
    const KEY = 'outgoing_edit_draft_' + docId;

    // Βρες main form (update)
    const form = document.querySelector('form[action*="outgoing"][method="POST"]');
    if (!form) return;

    function snapshotForm() {
        const data = {};
        form.querySelectorAll('input, textarea, select').forEach((el) => {
            if (!el.name) return;

            // Δεν αποθηκεύουμε files
            if (el.type === 'file') return;

            // checkbox/radio
            if (el.type === 'checkbox') {
                data[el.name] = el.checked ? 1 : 0;
                return;
            }
            if (el.type === 'radio') {
                if (el.checked) data[el.name] = el.value;
                return;
            }

            data[el.name] = el.value;
        });

        localStorage.setItem(KEY, JSON.stringify({
            ts: Date.now(),
            data
        }));
    }

    function restoreForm() {
        const raw = localStorage.getItem(KEY);
        if (!raw) return;

        let payload;
        try { payload = JSON.parse(raw); } catch (e) { return; }
        if (!payload || !payload.data) return;

        const data = payload.data;

        Object.keys(data).forEach((name) => {
            const el = form.querySelector(`[name="${CSS.escape(name)}"]`);
            if (!el) return;

            if (el.type === 'checkbox') {
                el.checked = !!Number(data[name]);
                return;
            }
            if (el.type === 'radio') {
                const radio = form.querySelector(`[name="${CSS.escape(name)}"][value="${CSS.escape(String(data[name]))}"]`);
                if (radio) radio.checked = true;
                return;
            }

            el.value = data[name] ?? '';
        });
    }

    // Όταν κάνεις κανονικό save (submit του update), καθάρισε το draft
    form.addEventListener('submit', () => {
        localStorage.removeItem(KEY);
    });

    // Πριν από delete attachment: σώσε draft
    document.querySelectorAll('button[onclick*="del-out-att-"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            snapshotForm();
        }, true);
    });

    document.querySelectorAll('form[id^="del-out-att-"]').forEach((delForm) => {
        delForm.addEventListener('submit', () => {
            snapshotForm();
        }, true);
    });

    // Σε κάθε φόρτωμα edit: επανάφερε draft αν υπάρχει
    restoreForm();

})();
</script>

</x-app-layout>

