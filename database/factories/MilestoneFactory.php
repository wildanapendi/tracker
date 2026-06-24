<?php

namespace Database\Factories;

use App\Enums\MilestoneStatus;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Milestone>
 */
class MilestoneFactory extends Factory
{
    protected $model = Milestone::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'Pengajuan Proposal',
                'Seminar Proposal',
                'Sidang Tugas Akhir',
                'Yudisium',
                'Ujian Komprehensif',
            ]),
            'order' => fake()->numberBetween(1, 5),
            'status' => fake()->randomElement(MilestoneStatus::cases()),
            'target_date' => fake()->optional(0.8)->dateTimeBetween('+1 month', '+12 months'),
            'completed_at' => null,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * State: milestone yang sudah selesai.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MilestoneStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
