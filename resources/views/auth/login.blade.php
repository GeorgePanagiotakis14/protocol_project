<x-guest-layout>

    <!-- Τίτλος έξω από πλαίσιο, μεγάλο και λευκό 
    <h1 class="text-white text-6xl font-bold mb-6 text-center select-none">
        Σύστημα Διαχείρισης Πρωτοκόλλων
    </h1> -->

    <!-- Τετράγωνο λευκό πλαίσιο -->
    <div class="w-80 h-80 bg-white rounded-lg shadow-lg p-6 flex flex-col justify-between">

        <!-- Μήνυμα σύνδεσης (όχι bold) -->
        <p class="text-black text-center text-base font-normal mb-4 select-none">
            Συνδεθείτε για να εισέλθετε στο σύστημα.
        </p>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="flex flex-col justify-between h-full">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full text-sm"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-3">
                <x-input-label for="password" :value="__('Κωδικός')" />
                <x-text-input
                    id="password"
                    class="block mt-1 w-full text-sm"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="mt-4 flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                />
                <label for="remember_me" class="ml-2 block text-sm text-gray-600 select-none">
                    Θυμήσου με
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4">
                <x-primary-button class="text-sm px-4 py-2">
                    Σύνδεση
                </x-primary-button>
            </div>
        </form>

    </div>

</x-guest-layout>
