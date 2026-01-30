<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Επεξεργασία Χρήστη #{{ $user->id }}
        </h2>
    </x-slot>

    <div class="card" style="max-width: 700px;">
        @if(session('success'))
            <div style="margin-bottom: 15px; color: green;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="margin-bottom: 15px; color: #b91c1c;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h3 style="margin-top:0;">Στοιχεία</h3>
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:10px;">
                <label>Όνομα</label><br>
                <input name="name" value="{{ old('name', $user->name) }}" style="width:100%;">
            </div>

            <div style="margin-bottom:10px;">
                <label>Email</label><br>
                <input name="email" value="{{ old('email', $user->email) }}" style="width:100%;">
            </div>

            <div style="margin-bottom:15px;">
                <label>
                    <input type="checkbox" name="is_admin" value="1"
                        {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                        {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                    Είναι Admin (Superuser)
                </label>
                @if(auth()->id() === $user->id)
                    <div style="font-size:12px; color:#666;">Δεν μπορείς να αφαιρέσεις admin από τον εαυτό σου.</div>
                @endif
            </div>

            <button type="submit">Αποθήκευση</button>
            <a href="{{ route('admin.users.index') }}" style="margin-left:10px;">Πίσω</a>
        </form>

        <hr style="margin:20px 0;">

        <h3>Αλλαγή Κωδικού</h3>
        <form method="POST" action="{{ route('admin.users.password', $user) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:10px;">
                <label>Νέος Κωδικός</label><br>
                <input type="password" name="password" style="width:100%;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Confirm Νέος Κωδικός</label><br>
                <input type="password" name="password_confirmation" style="width:100%;">
            </div>

            <button type="submit">Αλλαγή Κωδικού</button>
        </form>
    </div>
</x-app-layout>
