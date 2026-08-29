<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Chapter;
use App\Models\ChapterTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChapterTask>
 */
class ChapterTaskFactory extends Factory
{
    protected $model = ChapterTask::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::cases());

        return [
            'chapter_id' => Chapter::factory(),
            'title' => fake()->randomElement([
                'Draft awal',
                'Review literatur',
                'Revisi dari pembimbing',
                'Finalisasi',
                'ACC pembimbing',
                'Pengumpulan data',
                'Analisis data',
                'Penulisan hasil',
                'Penyusunan kesimpulan',
            ]),
            'status' => $status,
            'order' => fake()->numberBetween(1, 10),
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+3 months'),
            // completed_at di-manage otomatis oleh model boot (BR-02, BR-03)
            'completed_at' => $status === TaskStatus::Completed ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * State: task yang sudah selesai.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    /**
     * State: task yang sedang dikerjakan.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::InProgress,
            'completed_at' => null,
        ]);
    }

    /**
     * State: task yang belum dimulai.
     */
    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::NotStarted,
            'completed_at' => null,
        ]);
    }
}
