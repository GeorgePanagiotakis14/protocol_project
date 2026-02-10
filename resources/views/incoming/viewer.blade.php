<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Προβολή Συνημμένου (Α/Α: {{ $doc->aa }})
        </h2>
    </x-slot>

    <div class="card">
        <h2>{{ $title }}</h2>

        <div style="margin-top:12px; height:80vh; border:1px solid #ddd;">
            <iframe
                src="{{ $pdfUrl }}"
                style="width:100%; height:100%; border:0;"
            ></iframe>
        </div>

        <div style="margin-top:15px;">
            <a href="{{ route('incoming.attachments.index', $doc->id) }}{{ request('return') ? '?return=' . urlencode(request('return')) : '' }}"
               style="color:#111; text-decoration:underline;">
                ← Επιστροφή στα Συνημμένα
            </a>
        </div>
    </div>

    {{-- ✅ 100% fix: τίτλος καρτέλας από εμάς --}}
    <script>
        document.title = @json($title);
    </script>
</x-app-layout>
