<?php

namespace App\Listeners;

use App\Enums\MilestoneStatus;
use App\Models\Milestone;
use App\Models\MilestoneDocument;
use App\Models\ThesisProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class InitializeUserDataOnRegistration
{
    /**
     * Milestone default untuk setiap user baru (BR-05).
     *
     * @var array<int, array{title: string, order: int, documents: list<string>}>
     */
    private array $defaultMilestones = [
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

    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        // Otomatis tandai email verified jika di mode non-production / development
        if (! app()->isProduction() && ! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Buat ThesisProfile kosong agar user tidak error di halaman Profil Skripsi
        ThesisProfile::firstOrCreate(['user_id' => $user->id]);

        // Hanya seed jika belum punya milestone (idempotent)
        if ($user->milestones()->exists()) {
            return;
        }

        foreach ($this->defaultMilestones as $milestoneData) {
            $milestone = Milestone::create([
                'user_id' => $user->id,
                'title'   => $milestoneData['title'],
                'order'   => $milestoneData['order'],
                'status'  => MilestoneStatus::NotStarted,
            ]);

            foreach ($milestoneData['documents'] as $index => $docTitle) {
                MilestoneDocument::create([
                    'milestone_id' => $milestone->id,
                    'title'        => $docTitle,
                    'order'        => $index + 1,
                    'is_completed' => false,
                ]);
            }
        }
    }
}
