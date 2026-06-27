<?php

namespace App\Models;

use App\Enums\MilestoneStatus;
use Database\Factories\MilestoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use App\Policies\MilestonePolicy;

/**
 * Milestone — tahapan besar proses skripsi (Sempro, Sidang, dll).
 * Memiliki banyak MilestoneDocument sebagai checklist berkas.
 * Ref: SRS 3.7
 *
 * BR-10: Hapus milestone → cascade hapus documents + cleanup file fisik.
 */
#[UsePolicy(MilestonePolicy::class)]
class Milestone extends Model
{
    /** @use HasFactory<MilestoneFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'order',
        'status',
        'target_date',
        'completed_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MilestoneStatus::class,
            'order' => 'integer',
            'target_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Boot: cleanup file fisik sebelum milestone dihapus (BR-10).
     *
     * Database cascade menghapus records milestone_documents,
     * tapi file fisik di storage harus dihapus manual di sini.
     */
    protected static function booted(): void
    {
        // BR-04: auto-manage completed_at berdasarkan status change (sama seperti ChapterTask)
        static::saving(function (Milestone $milestone) {
            if ($milestone->isDirty('status')) {
                if ($milestone->status === MilestoneStatus::Completed) {
                    $milestone->completed_at = $milestone->completed_at ?? now();
                } else {
                    $milestone->completed_at = null;
                }
            }
        });

        // BR-10: cleanup file fisik sebelum milestone dihapus
        static::deleting(function (Milestone $milestone) {
            $milestone->documents->each(function (MilestoneDocument $doc) {
                if ($doc->file_path) {
                    Storage::disk('local')->delete($doc->file_path);
                }
            });
        });
    }

    /**
     * Pemilik milestone.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Daftar persyaratan berkas milestone ini.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(MilestoneDocument::class)->orderBy('order');
    }
}
