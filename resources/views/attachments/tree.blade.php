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
                    <details class="mb-3">
                        <summary class="cursor-pointer font-semibold">📁 {{ $year }}</summary>

                        {{-- Level 1 indent --}}
                        <div class="ml-8 mt-2 border-l border-gray-200 pl-4">
                            @foreach($types as $type => $dates)
                                <details class="mb-2">
                                    <summary class="cursor-pointer font-semibold">
                                        {{ $type === 'incoming' ? '📥 Incoming' : '📤 Outgoing' }}
                                    </summary>

                                    {{-- Level 2 indent --}}
                                    <div class="ml-10 mt-2 border-l border-gray-200 pl-4">
                                        @foreach($dates as $date => $items)
                                            <details class="mb-2">
                                                <summary class="cursor-pointer">
                                                    🗓️ {{ $date }}
                                                    <span class="text-gray-500">({{ $items->count() }})</span>
                                                </summary>

                                                {{-- Level 3 indent (files) --}}
                                                <ul class="ml-12 pl-4 mt-2 space-y-1 w-full text-left">

                                                    @foreach($items as $it)
                                                        @php
                                                            $viewer = $it['type'] === 'incoming'
                                                                ? route('incoming.attachments.viewer', [$it['doc_id'], $it['attachment_id']])
                                                                : route('outgoing.attachments.viewer', [$it['doc_id'], $it['attachment_id']]);
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
</x-app-layout>
