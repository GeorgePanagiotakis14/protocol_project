<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            Διαχείριση Χρηστών
        </h2>
    </x-slot>

    <div class="card">
        @if(session('success'))
            <div style="margin-bottom: 15px; color: green;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">

            <a style = " background-color: #1bb97f ; border-radius: 10px; color: white; border: 1px solid #555555;" href="{{ route('admin.users.create') }} ">+ Νέος Χρήστης</a>
        </div>

        <table border="1" width="100%" cellpadding="5">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Όνομα</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Ενέργειες</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->is_admin ? 'Ναι' : 'Όχι' }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $u) }}">Επεξεργασία</a>

                            @if(!$u->is_admin)
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $u) }}"
                                      style="display:inline;"
                                      onsubmit="return confirm('Είσαι σίγουρος ότι θέλεις να διαγράψεις τον χρήστη;');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="margin-left:8px;">
                                        Διαγραφή
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" align="center">Δεν υπάρχουν χρήστες</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 15px;">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
