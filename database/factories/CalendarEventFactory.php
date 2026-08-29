<?php

namespace Database\Factories;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CalendarEvent>
 */
class CalendarEventFactory extends Factory
{
    protected $model = CalendarEvent::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'Deadline pengumpulan revisi',
                'Workshop penulisan ilmiah',
                'Batas akhir pendaftaran sidang',
                'Libur kampus',
                'Pengumuman jadwal sempro',
                'Kelas metodologi penelitian',
            ]),
            'description' => fake()->optional(0.4)->sentence(),
            'event_date' => fake()->dateTimeBetween('-1 month', '+3 months'),
            'color' => fake()->optional(0.5)->hexColor(),
        ];
    }
}
