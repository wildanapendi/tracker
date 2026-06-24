<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\MilestoneDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MilestoneDocument>
 */
class MilestoneDocumentFactory extends Factory
{
    protected $model = MilestoneDocument::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'milestone_id' => Milestone::factory(),
            'title' => fake()->randomElement([
                'Transkrip Nilai',
                'Kartu Rencana Studi',
                'Surat Persetujuan Pembimbing',
                'Fotokopi KTM',
                'Draft Proposal',
                'Bukti Pembayaran SPP',
                'Surat Keterangan Bebas Pustaka',
                'Lembar Pengesahan',
                'Foto 3x4',
                'Formulir Pendaftaran Sidang',
                'Draft Skripsi Final',
                'Bukti Turnitin',
            ]),
            'is_completed' => fake()->boolean(30),
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'order' => fake()->numberBetween(1, 10),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * State: dokumen yang sudah dilengkapi (checklist).
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }

    /**
     * State: dokumen yang sudah di-upload file-nya.
     */
    public function withFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'file_path' => 'private/documents/' . fake()->uuid() . '.pdf',
            'file_name' => fake()->word() . '.pdf',
            'file_size' => fake()->numberBetween(50_000, 10_000_000),
        ]);
    }
}
