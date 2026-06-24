<?php

namespace Database\Seeders;

use App\Enums\MilestoneStatus;
use App\Models\Milestone;
use App\Models\MilestoneDocument;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seed milestone default untuk user baru (BR-05).
 *
 * 4 milestone standar proses skripsi perguruan tinggi Indonesia:
 * 1. Pengajuan Proposal
 * 2. Seminar Proposal (Sempro)
 * 3. Sidang Tugas Akhir / Komprehensif
 * 4. Yudisium
 *
 * Setiap milestone disertakan contoh persyaratan berkas default
 * yang dapat diubah/dihapus oleh user.
 */
class DefaultMilestoneSeeder extends Seeder
{
    /**
     * Definisi milestone default beserta persyaratan berkas.
     *
     * @var array<int, array{title: string, order: int, documents: list<string>}>
     */
    private array $defaults = [
        [
            'title' => 'Pengajuan Proposal',
            'order' => 1,
            'documents' => [
                'Draft Proposal Penelitian',
                'Formulir Pengajuan Judul',
                'Surat Persetujuan Pembimbing',
                'Transkrip Nilai Sementara',
            ],
        ],
        [
            'title' => 'Seminar Proposal',
            'order' => 2,
            'documents' => [
                'Proposal Final (Revisi)',
                'Formulir Pendaftaran Sempro',
                'Bukti Pembayaran SPP',
                'Kartu Rencana Studi (KRS)',
                'Fotokopi KTM',
            ],
        ],
        [
            'title' => 'Sidang Tugas Akhir / Komprehensif',
            'order' => 3,
            'documents' => [
                'Draft Skripsi Final',
                'Formulir Pendaftaran Sidang',
                'Lembar Persetujuan Pembimbing',
                'Bukti Revisi Sempro',
                'Surat Keterangan Bebas Pustaka',
                'Bukti Pembayaran Wisuda',
                'Laporan Turnitin / Cek Plagiasi',
            ],
        ],
        [
            'title' => 'Yudisium',
            'order' => 4,
            'documents' => [
                'Skripsi Final (Hard Cover)',
                'Lembar Pengesahan Bertandatangan',
                'Bukti Penyerahan ke Perpustakaan',
                'Transkrip Nilai Final',
                'Pas Foto 3x4 (4 lembar)',
                'Formulir Pendaftaran Yudisium',
            ],
        ],
    ];

    /**
     * Seed default milestones untuk semua user yang belum memiliki milestone.
     */
    public function run(): void
    {
        $users = User::doesntHave('milestones')->get();

        if ($users->isEmpty()) {
            $message = 'DefaultMilestoneSeeder: Tidak ada user baru yang membutuhkan inisialisasi milestone.';
            if ($this->command) {
                $this->command->info($message);
            } else {
                echo $message . PHP_EOL;
            }
            return;
        }

        foreach ($users as $user) {
            $startMessage = "DefaultMilestoneSeeder: Mulai inisialisasi milestone default untuk {$user->name} ({$user->email})...";
            if ($this->command) {
                $this->command->info($startMessage);
            } else {
                echo $startMessage . PHP_EOL;
            }

            $this->seedForUser($user);

            $endMessage = "DefaultMilestoneSeeder: Selesai menginisialisasi milestone untuk {$user->name} ({$user->email}).";
            if ($this->command) {
                $this->command->info($endMessage);
            } else {
                echo $endMessage . PHP_EOL;
            }
        }
    }

    /**
     * Seed milestones dan documents untuk satu user.
     */
    private function seedForUser(User $user): void
    {
        foreach ($this->defaults as $milestoneData) {
            $milestone = Milestone::create([
                'user_id' => $user->id,
                'title' => $milestoneData['title'],
                'order' => $milestoneData['order'],
                'status' => MilestoneStatus::NotStarted,
            ]);

            foreach ($milestoneData['documents'] as $index => $docTitle) {
                MilestoneDocument::create([
                    'milestone_id' => $milestone->id,
                    'title' => $docTitle,
                    'order' => $index + 1,
                    'is_completed' => false,
                ]);
            }
        }
    }
}
