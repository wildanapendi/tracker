<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Calendar extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|\UnitEnum|null $navigationGroup = 'Perencanaan';
    protected static ?string $title = 'Kalender Akademik';

    // Pastikan property non-static di Filament 5 untuk $view
    protected string $view = 'filament.pages.calendar';
}
