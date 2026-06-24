<?php

namespace Database\Factories;

use App\Enums\GuidanceStatus;
use App\Models\Guidance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guidance>
 */
class GuidanceFactory extends Factory
{
    protected $model = Guidance::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'scheduled_at' => fake()->dateTimeBetween('-1 month', '+2 months'),
            'status' => fake()->randomElement(GuidanceStatus::cases()),
            'agenda' => fake()->randomElement([
                'Review Bab 1 - Pendahuluan',
                'Diskusi metodologi penelitian',
                'Konsultasi analisis data',
                'Review draft Bab 3',
                'Pembahasan revisi dari penguji',
                'Konsultasi judul penelitian',
                'Review keseluruhan draft',
                'Persiapan seminar proposal',
                'Diskusi hasil penelitian',
            ]),
            'result' => fake()->optional(0.5)->paragraph(),
            'action_items' => fake()->optional(0.5)->paragraph(),
            'location' => fake()->optional(0.7)->randomElement([
                'Ruang Dosen Lt. 3',
                'Lab Komputer',
                'Google Meet',
                'Zoom Meeting',
                'Ruang Rapat Fakultas',
            ]),
        ];
    }

    /**
     * State: bimbingan yang sudah dijadwalkan (upcoming).
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => GuidanceStatus::Scheduled,
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'result' => null,
            'action_items' => null,
        ]);
    }

    /**
     * State: bimbingan yang sudah selesai.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => GuidanceStatus::Completed,
            'scheduled_at' => fake()->dateTimeBetween('-2 months', '-1 day'),
            'result' => fake()->paragraph(),
            'action_items' => fake()->paragraph(),
        ]);
    }
}
