<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Συνημμένα Εξερχομένου (Α/Α: {{ $doc->aa }})
        </h2>
    </x-slot>

    <div class="card">
        <h2>Συνημμένα για Εξερχόμενο Α/Α: {{ $doc->aa }}</h2>

        @if($doc->attachments->count() > 0)
            <ul style="margin-top:10px; padding-left:18px;">
                @foreach($doc->attachments as $a)
                    <li style="margin-bottom:8px;">
                        {{ $a->original_name ?? basename($a->path) }}
                        —
                        <a href="{{ route('outgoing.attachments.viewer', [$doc->id, $a->id]) }}"
                           style="color:#2563eb; font-weight:600; text-decoration:underline;">
                            Προβολή
                        </a>
                    </li>
                @endforeach
            </ul>

        @elseif($doc->attachment_path)
            {{-- ✅ Fallback για παλιά records (μόνο attachment_path) --}}
            <p style="margin-top:10px;">
                Υπάρχει παλιό συνημμένο:
                <a href="{{ route('outgoing.attachment', $doc->id) }}"
                   style="color:#2563eb; font-weight:600; text-decoration:underline;">
                    Προβολή
                </a>
            </p>

        @else
            <p style="margin-top:10px;">Δεν υπάρχουν συνημμένα.</p>
        @endif

       @php
        $defaultBack = route('outgoing.index');
        $back = $backUrl ?? $defaultBack;

         if (str_contains($back, '/documents/all')) {
             $text = '← Επιστροφή στα Όλα τα πρωτόκολλα';
         } else {
             $text = '← Επιστροφή στα Εξερχόμενα';
         }
        @endphp

<a href="{{ $back }}" style="text-decoration:underline;">
    {{ $text }}
</a>


    </div>
</x-app-layout>
