resources/views/incoming/attachements.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Συνημμένα Εισερχομένου (Α/Α: {{ $doc->aa }})
        </h2>
    </x-slot>

    <div class="card">
        <h2>Συνημμένα για Εισερχόμενο Α/Α: {{ $doc->aa }}</h2>

        @if($doc->attachments->count() > 0)
            <ul style="margin-top:10px; padding-left:18px;">
                @foreach($doc->attachments as $a)
                    <li style="margin-bottom:8px;">
                        {{ $a->original_name ?? basename($a->path) }}
                        —
                        @php
                            $base = route('incoming.attachments.viewer', [$doc->id, $a->id]);
                            $viewerUrl = (!empty($backUrl) && (str_contains($backUrl, '/documents/all') || str_contains($backUrl, '/documents/common')))
                                ? $base . '?return=' . urlencode($backUrl)
                                : $base;
                        @endphp
                        <a href="{{ $viewerUrl }}"
                           style="color:#2563eb; font-weight:600; text-decoration:underline;">
                            Προβολή
                        </a>
                    </li>
                @endforeach
            </ul>

        @elseif($doc->attachment_path)
            {{-- Fallback για παλιά records (πριν τα πολλαπλά attachments) --}}
            <p style="margin-top:10px;">
                Υπάρχει παλιό συνημμένο:
                <a href="{{ route('incoming.attachment', $doc->id) }}"
                   target="_blank"
                   style="color:#2563eb; font-weight:600; text-decoration:underline;">
                    Προβολή
                </a>
            </p>

        @else
            <p style="margin-top:10px;">Δεν υπάρχουν συνημμένα.</p>
        @endif

        @php
        $defaultBack = route('incoming.index');
        $back = $backUrl ?? $defaultBack;

        if (str_contains($back, '/documents/common')) {
            $text = '← Επιστροφή στα Κοινά';
        } elseif (str_contains($back, '/documents/all')) {
            $text = '← Επιστροφή στα Όλα τα Πρωτόκολλα';
        } else {
            $text = '← Επιστροφή στα Εισερχόμενα';
        }
        @endphp

        <a href="{{ $back }}" style="text-decoration:underline;">
            {{ $text }}
        </a>

    </div>
</x-app-layout>
