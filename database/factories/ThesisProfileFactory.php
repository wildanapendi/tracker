<?php

namespace Database\Factories;

use App\Models\ThesisProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ThesisProfile>
 */
class ThesisProfileFactory extends Factory
{
    protected $model = ThesisProfile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(8),
            'study_program' => fake()->randomElement([
                'Teknik Informatika',
                'Sistem Informasi',
                'Ilmu Komputer',
                'Teknik Elektro',
                'Manajemen',
            ]),
            'faculty' => fake()->randomElement([
                'Fakultas Teknik',
                'Fakultas Ilmu Komputer',
                'Fakultas Ekonomi dan Bisnis',
                'Fakultas MIPA',
            ]),
            'supervisor_name' => 'Dr. ' . fake()->name(),
            'co_supervisor_name' => fake()->optional(0.6)->name(),
            'start_date' => fake()->dateTimeBetween('-6 months', '-1 month'),
            'target_completion' => fake()->dateTimeBetween('+1 month', '+6 months'),
        ];
    }
}
