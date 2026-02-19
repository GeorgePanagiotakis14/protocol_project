<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Εισερχόμενα - Εξερχόμενα</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #1f2937;
            color: #fff;
            position: fixed;
            padding: 20px;
        }
        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 18px;
        }
        .sidebar a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            margin-bottom: 15px;
        }
        .sidebar a.active {
         color: #ffffff;
         font-weight: 500;
         background: rgba(255, 255, 255, 0.12);
         border-radius: 6px;

         /* αγκαλιάζει */
         padding: 6px 10px;

         /* ΑΥΤΟ ΚΑΝΕΙ ΤΟ MAGIC */
         line-height: 1.4;
         display: inline-block;
         }

        .sidebar a:hover {
            color: #f2f3f3;
        }
        .content {
            margin-left: 250px;
            padding: 0px;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 6px;
        }
        /* FIX: Επαναφέρει τα πλαίσια στους πίνακες μέσα σε card */
        .card table {
            width: 100%;
            border-collapse: collapse;
        }

        .card table th,
        .card table td {
           border: 1px solid #030303; /* ουδέτερο γκρι */
           padding: 6px;
           vertical-align: top;
        }

        .home-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            width: 100%;
            max-width: 1200px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Μενού</h2>

    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
    Αρχική
    </a>

    <a href="{{ route('documents.create') }}" class="{{ request()->routeIs('documents.create') ? 'active' : '' }}">
    Συμπλήρωση Εισερχομένου / Εξερχομένου
    </a>


    <hr>

    <a href="{{ route('incoming.index') }}" class="{{ request()->routeIs('incoming.*') ? 'active' : '' }}">
    Εμφάνιση Εισερχομένων
    </a>
    
    <a href="{{ route('outgoing.index') }}" class="{{ request()->routeIs('outgoing.*') ? 'active' : '' }}">
    Εμφάνιση Εξερχομένων
    </a>

    <a href="{{ route('documents.common') }}" class="{{ request()->routeIs('documents.common') ? 'active' : '' }}">
    Κοινά Εισερχόμενα - Εξερχόμενα
    </a>
    
    <a href="{{ route('documents.all') }}" class="{{ request()->routeIs('documents.all') ? 'active' : '' }}">
    Όλα τα πρωτόκολλα
    </a>

    <a href="{{ route('attachments.tree') }}" class="{{ request()->routeIs('attachments.tree') ? 'active' : '' }}">
    Επισυναπτόμενα
    </a>




    @if(auth()->user()?->is_admin)
    <a href="{{ route('admin.audit.index') }}"
       class="{{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
        Audit Log
    </a>
    @endif

    @if(auth()->user()?->is_admin)
    <a href="{{ route('admin.users.index') }}"
       class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        Διαχείριση Χρηστών
    </a>
    @endif

    <hr>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="
            background:none;
            border:none;
            color:#cbd5e1;
            padding:0;
            cursor:pointer;
            margin-top:20px;
        ">
            Αποσύνδεση
        </button>
    </form>
</div>

<div class="content">

    @isset($header)
        <div style="margin-bottom: 25px;">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

</div>

<!-- ✅ GLOBAL DATE-YEAR LOCK -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('input[type="date"][data-year]');

  inputs.forEach((input) => {
    const y = parseInt(input.dataset.year, 10);
    if (!y || Number.isNaN(y)) return;

    const yyyy = String(y).padStart(4, '0');
    const min = `${yyyy}-01-01`;
    const max = `${yyyy}-12-31`;

    // ✅ Κλείδωμα ΜΟΝΟ μέσα στο επιλεγμένο έτος
    input.min = min;
    input.max = max;

    // ✅ Βάζουμε default ΜΟΝΟ όταν ο χρήστης πατήσει το date input (όχι στο focus)
    const setTempDefaultIfEmpty = () => {
      if (!input.value) {
        input.dataset.tempDefault = '1';
        input.value = min; // ανοίγει στο σωστό έτος
      }
    };

    // User intent events (πριν ανοίξει ο native picker)
    input.addEventListener('pointerdown', setTempDefaultIfEmpty, { capture: true });
    input.addEventListener('mousedown', setTempDefaultIfEmpty, { capture: true });
    input.addEventListener('touchstart', setTempDefaultIfEmpty, { capture: true });

    // ❌ ΣΗΜΑΝΤΙΚΟ: ΔΕΝ βάζουμε focus handler
    // γιατί στο submit ο browser κάνει focus στο πρώτο invalid required field
    // και τότε γέμιζε μόνο του με 01/01.

    // Αν ο χρήστης όντως αλλάξει ημερομηνία, καθαρίζουμε το flag
    input.addEventListener('change', () => {
      delete input.dataset.tempDefault;
    });

    // (Προαιρετικό) Αν θες να μπορεί να μείνει 01/01 κανονικά, δεν κάνουμε auto-clear στο blur.
    input.addEventListener('blur', () => {
      delete input.dataset.tempDefault;
    });
  });
});
</script>


</body>
</html>

