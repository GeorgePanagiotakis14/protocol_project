<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a "protocol year" selection in session and shares it with all views.
 *
 * Rules:
 * - Default year = current year (server time) and auto-updates when the year changes.
 * - If the user explicitly selects a year (via ?year=YYYY), we keep that fixed.
 */
class SetProtocolYear
{
    public function handle(Request $request, Closure $next): Response
    {
        
        $currentYear = (int) now()->year;

        // If user passes ?year=YYYY, treat it as a manual selection
        if ($request->filled('year')) {
            $year = (int) $request->query('year');
            if ($year >= 1900 && $year <= 2300) {
                session([
                    'protocol_year' => $year,
                    'protocol_year_manual' => true,
                ]);
            }
        }

        // Auto mode: follow current year
        $manual = filter_var(session('protocol_year_manual', false), FILTER_VALIDATE_BOOLEAN);
        if (!$manual) {
            session(['protocol_year' => $currentYear]);
        }

        $selectedYear = (int) session('protocol_year', $currentYear);
        view()->share('selectedYear', $selectedYear);

        return $next($request);
    }
}
