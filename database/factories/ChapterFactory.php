<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chapter>
 */
class ChapterFactory extends Factory
{
    protected $model = Chapter::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => 'Bab ' . fake()->numberBetween(1, 5) . ' - ' . fake()->sentence(3),
            'order' => fake()->numberBetween(1, 10),
            'weight' => fake()->randomFloat(2, 0.5, 3.0),
            'notes' => fake()->optional(0.3)->paragraph(),
        ];
    }
}
