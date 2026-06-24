<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SkripsiTracker
|--------------------------------------------------------------------------
|
| Semua endpoint di sini diawali prefix /api/ dan TIDAK memerlukan CSRF token.
| Response selalu dalam format JSON. Cocok untuk Postman, Insomnia, atau cURL.
|
| Alur penggunaan:
|   1. POST /api/login    → Dapatkan session (cookie laravel_session)
|   2. GET  /api/me       → Verifikasi status login
|   3. GET  /api/calendar/events → Ambil data event kalender
|   4. POST /api/logout   → Akhiri session
|
*/

// ─── Auth Endpoints (Tidak Memerlukan Login) ─────────────────────────────────
Route::post('login', [AuthController::class, 'login'])->name('api.login');

// ─── Protected Endpoints (Memerlukan Session dari POST /api/login) ────────────
Route::middleware('auth:web')->group(function () {
    // Cek status autentikasi
    Route::get('me', [AuthController::class, 'me'])->name('api.me');

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');

    // Endpoint data kalender — JSON feed untuk FullCalendar / pengujian API
    Route::get('calendar/events', [CalendarController::class, 'events'])
        ->name('api.calendar.events');
});
