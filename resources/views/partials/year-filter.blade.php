@php
    $minYear = 1900;
    $maxYear = 2300;

    // Selected year από query, αλλιώς τρέχον έτος
    $selectedYear = (int) request()->query('year', now()->year);

    // Clamp για να μην ξεφεύγει
    if ($selectedYear < $minYear) $selectedYear = $minYear;
    if ($selectedYear > $maxYear) $selectedYear = $maxYear;

    // Βοηθητικό: κρατάμε όλα τα query params εκτός από page (για να μη σπάει pagination)
    $query = request()->query();
    unset($query['page']);
@endphp

<form method="GET" action="{{ url()->current() }}" style="text-align:center; margin: 10px 0 18px;">
    {{-- Διατηρούμε τυχόν άλλα query params (π.χ. from/to κλπ) --}}
    @foreach($query as $k => $v)
        @if($k !== 'year')
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
    @endforeach

    <label style="font-weight:600; margin-right:8px;">
        Έτος:
    </label>

    <input
        type="number"
        name="year"
        value="{{ $selectedYear }}"
        min="{{ $minYear }}"
        max="{{ $maxYear }}"
        inputmode="numeric"
        style="width:110px; padding:6px 10px; border:1px solid #000; border-radius:8px; text-align:center;"
        required
    >

    <button
        type="submit"
        style="margin-left:10px; padding:6px 14px; border:1px solid #000; border-radius:8px; background:#fff; cursor:pointer;"
    >
        Προβολή
    </button>

    <div style="margin-top:10px; font-weight:700;">
        Έτος προβολής: {{ $selectedYear }}
    </div>
</form>

