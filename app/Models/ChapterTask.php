<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Database\Factories\ChapterTaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sub-task dalam setiap bab skripsi.
 * Status dikelola via TaskStatus enum.
 * completed_at di-auto-manage saat status berubah (BR-02, BR-03).
 * Ref: SRS 3.5
 */
class ChapterTask extends Model
{
    /** @use HasFactory<ChapterTaskFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'title',
        'status',
        'order',
        'due_date',
        'completed_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'order' => 'integer',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Boot: auto-manage completed_at berdasarkan status change.
     *
     * BR-02: Saat status → completed, completed_at diisi timestamp saat ini.
     * BR-03: Saat status berubah dari completed, completed_at di-null-kan.
     */
    protected static function booted(): void
    {
        static::saving(function (ChapterTask $task) {
            // Hanya proses jika status berubah (bukan saat create pertama kali tanpa status change)
            if ($task->isDirty('status')) {
                if ($task->status === TaskStatus::Completed) {
                    // BR-02: auto-fill completed_at
                    $task->completed_at = $task->completed_at ?? now();
                } else {
                    // BR-03: null-kan completed_at jika bukan completed
                    $task->completed_at = null;
                }
            }
        });
    }

    /**
     * Bab parent dari task ini.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
