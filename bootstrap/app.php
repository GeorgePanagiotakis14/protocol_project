<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
        // Κράτα logs για εμάς
        logger()->error($e);

        // Σε debug mode άφησέ το default (debug page)
        if (config('app.debug')) {
            return null;
        }

        // ΜΗΝ πειράζεις "κανονικά" HTTP errors (404/403 κλπ)
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
            return null;
        }

        // Για όλα τα υπόλοιπα, δείξε φιλική σελίδα 500
        return response()->view('errors.generic', [], 500);
      });
    })
    ->create();
