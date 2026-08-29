<?php

namespace App\Enums;

/**
 * Status enum untuk jadwal bimbingan dengan dosen pembimbing.
 *
 * Backed string values disimpan di database column `status`.
 * Method label() mengembalikan teks Bahasa Indonesia untuk UI.
 * Method color() mengembalikan nama warna Filament untuk badge/icon.
 */
enum GuidanceStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rescheduled = 'rescheduled';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Dijadwalkan',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
            self::Rescheduled => 'Dijadwalkan Ulang',
        };
    }

    /**
     * Nama warna Filament untuk badge dan icon.
     */
    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'info',
            self::Completed => 'success',
            self::Cancelled => 'danger',
            self::Rescheduled => 'warning',
        };
    }

    /**
     * Icon Heroicon untuk visual indicator.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Scheduled => 'heroicon-o-clock',
            self::Completed => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Rescheduled => 'heroicon-o-arrow-path',
        };
    }
}
