<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;

// Catatan: Rute /login, /register, dll dikelola langsung oleh Filament Panel
// melalui AppPanelProvider. Jangan duplikasi di sini karena akan menyebabkan
// RouteNotFoundException circular saat route cache belum ada.

// Redirect root non-Filament ke dashboard Filament menggunakan URL statis
// agar tidak bergantung pada route cache.
Route::get('dashboard', function () {
    return redirect('/');
})->name('dashboard');

Route::get('home', function () {
    return redirect('/');
})->name('home');

// Endpoint agregasi khusus untuk kalender JSON feed.
Route::middleware('auth')->get('calendar/events', [CalendarController::class, 'events'])
    ->name('calendar.events');

require __DIR__.'/settings.php';
