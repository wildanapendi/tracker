<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Route API didaftarkan manual via callback agar bisa pakai middleware 'web'
        // (bukan 'api' yang stateless) — ini memungkinkan session cookie Postman bekerja.
        then: function () {
            Route::middleware(['web'])   // Pakai middleware 'web' agar session aktif
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Kecualikan semua route /api/* dari verifikasi CSRF token
        // agar Postman/cURL tidak perlu mengirim X-XSRF-TOKEN
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Paksa semua error pada route /api/* selalu dikembalikan dalam format JSON
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

