<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Μενού αρχικής σελίδας
        </h2>
    </x-slot>

    <div class="card" style="padding:20px; max-width:600px; margin:auto;">
        <h1 style="font-size: 1.4rem; margin-bottom: 12px;">Μενού αρχικής σελίδας</h1>

        <ul style="display:flex; flex-direction:column; gap:8px; margin-bottom:18px;">
            <li><a href="{{ route('documents.create') }}">Νέο εισερχόμενο</a></li>
            <li><a href="{{ route('documents.create') }}">Νέο εξερχόμενο</a></li>
            <li><a href="{{ route('incoming.index') }}">Φάκελος εισερχομένων</a></li>
            <li><a href="{{ route('outgoing.index') }}">Φάκελος εξερχομένων</a></li>
            <li><a href="{{ route('documents.all') }}">Όλα τα Πρωτόκολλα</a></li>
        </ul>

        {{-- ✅ Backup (μόνο για Admin) --}}
        @auth
            @if(method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                <hr style="margin: 14px 0;">

                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <form method="POST" action="{{ route('admin.backup.run') }}">
                        @csrf
                        <button type="submit"
                                style="padding: 8px 14px; border-radius: 8px; background:#111827; color:#fff; border:none; cursor:pointer;">
                            🔄 Δημιουργία Backup
                        </button>
                    </form>

                    <a href="{{ route('admin.backup.downloadLatest') }}"
                       style="padding: 8px 14px; border-radius: 8px; background:#2563eb; color:#fff; text-decoration:none;">
                        ⬇️ Κατέβασμα τελευταίου Backup
                    </a>
                </div>

                <div style="margin-top:10px; font-size:12px; opacity:.75;">
                    *Δεν χρειάζεται να τρέξεις χειροκίνητα* <code>php artisan backup:run</code> για να “φανεί” το κουμπί.
                    Το κουμπί απλά καλεί τα routes <code>admin.backup.run</code> και <code>admin.backup.downloadLatest</code>.
                </div>
                
            @endif
        @endauth
    </div>

</x-app-layout>
