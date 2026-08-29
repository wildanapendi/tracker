<?php

namespace App\Enums;

/**
 * Status enum untuk sub-task dalam setiap bab skripsi.
 *
 * Backed string values disimpan di database column `status`.
 * Method label() mengembalikan teks Bahasa Indonesia untuk UI.
 * Method color() mengembalikan nama warna Filament untuk badge/icon.
 */
enum TaskStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Belum Dimulai',
            self::InProgress => 'Dalam Proses',
            self::Completed => 'Selesai',
        };
    }

    /**
     * Nama warna Filament untuk badge dan icon.
     */
    public function color(): string
    {
        return match ($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'warning',
            self::Completed => 'success',
        };
    }

    /**
     * Icon Heroicon untuk visual indicator.
     */
    public function icon(): string
    {
        return match ($this) {
            self::NotStarted => 'heroicon-o-minus-circle',
            self::InProgress => 'heroicon-o-arrow-path',
            self::Completed => 'heroicon-o-check-circle',
        };
    }
}
