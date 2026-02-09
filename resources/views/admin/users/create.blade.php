<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold mb-4" style="text-align: center; font-size:2.2rem; text-3xl text-gray-800 leading-tight font-size:2.2rem;">
            Νέος Χρήστης
        </h2>
    </x-slot>

    <div class="card" style="max-width:700px; margin:0 auto;">
        @if ($errors->any())
            <div style="text-align: center; margin-bottom: 15px; color: #b91c1c;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div style="margin-bottom:10px;">
                <label>Όνομα</label><br>
                <input name="name" value="{{ old('name') }}" style="width:100%;">
            </div>

            <div style="margin-bottom:10px;">
                <label>Email</label><br>
                <input name="email" value="{{ old('email') }}" style="width:100%;">
            </div>

            <div style="margin-bottom:10px;">
                <label>Password</label><br>
                <input type="password" name="password" style="width:100%;">
            </div>

            <div style="margin-bottom:10px;">
                <label>Confirm Password</label><br>
                <input type="password" name="password_confirmation" style="width:100%;">
            </div>

            <div style="margin-bottom:15px;">
                <label>
                    <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                    Είναι Admin (Superuser)
                </label>
            </div>

            <button type="submit" style="margin-left:10px; padding: 10px 24px; background-color: #3baacf;">Δημιουργία</button>
            <a href="{{ route('admin.users.index') }}" style="margin-left:10px; padding: 10px 24px; background-color: #d4c2bf;">Άκυρο</a>
        </form>
    </div>
</x-app-layout> 
