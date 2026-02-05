<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col items-center justify-center font-sans antialiased"
      style="background: linear-gradient(135deg, #1f2937 0%, #3b82f6 100%); position: relative;">

    <!-- Τίτλος πάνω από το πλαίσιο -->
    <h1 style="font-weight: 400; font-size: 3.5rem; margin-bottom: 2rem; color: white;">
        Σύστημα Διαχείρισης Πρωτοκόλλων
    </h1>

    <!-- Λευκό τετράγωνο πλαίσιο για login -->
    <div class="w-80 h-80 bg-white rounded-lg shadow-lg p-6 flex flex-col justify-between z-10">
        {{ $slot }}
    </div>

    <!-- Logo κάτω δεξιά, fixed ώστε να μη χρειάζεται scroll -->
    <img src="{{ asset('images/library-sparta-logo.png') }}" 
        alt="Library Sparta Logo" 
        class="w-16 h-16 fixed bottom-0 right-0 opacity-50 pointer-events-none select-none z-20">

</body>
</html>
