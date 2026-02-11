<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Επισυναπτόμενα
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4 text-left">

                @forelse($tree as $year => $types)
                    <details class="mb-3" data-key="year:{{ $year }}">
                        <summary class="cursor-pointer font-semibold">📁 {{ $year }}</summary>

                        {{-- Level 1 indent --}}
                        <div class="ml-8 mt-2 border-l border-gray-200 pl-4">
                            @foreach($types as $type => $dates)
                                <details class="mb-2" data-key="type:{{ $year }}:{{ $type }}">
                                    <summary class="cursor-pointer font-semibold">
                                        {{ $type === 'incoming' ? '📥 Incoming' : '📤 Outgoing' }}
                                    </summary>

                                    {{-- Level 2 indent --}}
                                    <div class="ml-10 mt-2 border-l border-gray-200 pl-4">
                                        @foreach($dates as $date => $items)
                                            <details class="mb-2" data-key="date:{{ $year }}:{{ $type }}:{{ $date }}">
                                                <summary class="cursor-pointer">
                                                    🗓️ {{ $date }}
                                                    <span class="text-gray-500">({{ $items->count() }})</span>
                                                </summary>

                                                {{-- Level 3 indent (files) --}}
                                                <ul class="ml-12 pl-4 mt-2 space-y-1 w-full text-left">

                                                    @foreach($items as $it)
                                                        @php
                                                            $baseViewer = $it['type'] === 'incoming'
                                                                ? route('incoming.attachments.viewer', [$it['doc_id'], $it['attachment_id']])
                                                                : route('outgoing.attachments.viewer', [$it['doc_id'], $it['attachment_id']]);

                                                            // ΚΡΑΤΑΜΕ ΑΚΡΙΒΩΣ ΤΟ ΔΙΚΟ ΣΟΥ return
                                                            $viewer = $baseViewer . '?return=' . urlencode(url()->full());
                                                        @endphp

                                                        <li class="flex flex-col items-start gap-1">
                                                            <a class="text-indigo-600 hover:underline" href="{{ $viewer }}">
                                                                {{ $it['filename'] ?? $it['name'] }}
                                                            </a>

                                                            <span class="text-gray-500 text-sm">
                                                                (Α/Α: {{ $it['aa'] ?? '-' }}{{ $it['subject'] ? ' — '.$it['subject'] : '' }})
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </details>
                                        @endforeach
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </details>
                @empty
                    <div class="text-gray-600">Δεν υπάρχουν επισυναπτόμενα.</div>
                @endforelse

            </div>
        </div>
    </div>

    <script>
        (function () {
            const STORAGE_KEY = 'attachments_tree_state_v1';

            function saveState() {
                const openKeys = Array.from(document.querySelectorAll('details[data-key][open]'))
                    .map(d => d.getAttribute('data-key'));

                const state = {
                    openKeys,
                    scrollY: window.scrollY || 0
                };

                sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
            }

            function restoreState() {
                const raw = sessionStorage.getItem(STORAGE_KEY);
                if (!raw) return;

                let state;
                try { state = JSON.parse(raw); } catch (e) { return; }

                if (Array.isArray(state.openKeys)) {
                    state.openKeys.forEach(key => {
                        const el = document.querySelector('details[data-key="' + key + '"]');
                        if (el) el.open = true;
                    });
                }

                requestAnimationFrame(() => {
                    window.scrollTo(0, Number(state.scrollY || 0));
                });
            }

            document.addEventListener('toggle', (e) => {
                if (e.target && e.target.matches('details[data-key]')) saveState();
            }, true);

            document.addEventListener('click', (e) => {
                const a = e.target.closest('a');
                if (!a) return;
                if (a.closest('.bg-white')) saveState();
            });

            restoreState();
        })();
    </script>
</x-app-layout>
