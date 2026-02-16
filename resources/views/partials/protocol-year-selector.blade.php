@php
    $y = (int) ($selectedYear ?? now()->year);
    $minY = 1900;
    $maxY = 2300;
@endphp

<form method="GET" action="{{ url()->current() }}" style="text-align:center; margin: 10px 0 18px;">
    <label style="font-weight:700; margin-right:8px;">Έτος:</label>

    <input
        type="number"
        name="year"
        value="{{ $y }}"
        min="{{ $minY }}"
        max="{{ $maxY }}"
        inputmode="numeric"
        style="width:110px; text-align:center; padding:6px 10px; border:1px solid #111; border-radius:8px;"
        onkeydown="if(event.key==='Enter'){ this.form.submit(); }"
    />

    <button
        type="submit"
        style="margin-left:10px; padding:6px 12px; border-radius:8px; background:#111; color:#fff; border:1px solid #111; cursor:pointer;"
    >
        OK
    </button>

    <div style="margin-top:8px; font-weight:700;">
        Επιλεγμένο έτος: <span style="text-decoration:underline;">{{ $y }}</span>
    </div>

</form>
