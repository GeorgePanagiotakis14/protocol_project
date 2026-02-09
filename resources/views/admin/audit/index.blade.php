<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold mb-4" style="text-align: center; font-size:2.2rem; text-3xl text-gray-800 leading-tight font-size:2.2rem;">
            Audit Log
        </h2>
    </x-slot>

    <div class="card" style="margin-bottom:20px; text-align:center;">
        <form method="GET"
            style="display:flex; gap:15px; align-items:center; justify-content:center;">
            
            <label>
                Ενότητα
                <select name="section">
                    <option value="all">All</option>
                    <option value="incoming" {{ request('section') === 'incoming' ? 'selected' : '' }}>Incoming</option>
                    <option value="outgoing" {{ request('section') === 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                </select>
            </label>

            <label>
                Ενέργεια
                <select name="action">
                    <option value="all">All</option>
                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                </select>
            </label>

            <button type="submit"
                    style="padding:8px 17px; border:1px solid #555555; border-radius:8px;">
                Εφαρμογή
            </button>
        </form>
    </div>


    <div class="card">
        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>Ημερομηνία</th>
                    <th>Ενότητα</th>
                    <th>Ενέργεια</th>
                    <th>Έγγραφο</th>
                    <th>Χρήστης</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($log->section) }}</td>
                        <td>{{ ucfirst($log->action) }}</td>
                        <td>#{{ $log->document_id }}</td>
                        <td>
                            {{ $log->user?->name ?? 'System' }}
                            <br>
                            <small>{{ $log->user?->email }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" align="center">Δεν υπάρχουν καταγραφές</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:15px;">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
