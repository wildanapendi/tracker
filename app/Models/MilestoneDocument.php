<?php

namespace App\Models;

use Database\Factories\MilestoneDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Persyaratan berkas per milestone — checklist document item.
 * Mendukung file upload ke storage/app/private/documents.
 * Ref: SRS 3.8
 */
class MilestoneDocument extends Model
{
    /** @use HasFactory<MilestoneDocumentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'milestone_id',
        'title',
        'is_completed',
        'file_path',
        'file_name',
        'file_size',
        'order',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'file_size' => 'integer',
            'order' => 'integer',
        ];
    }

    /**
     * Boot: cleanup file fisik sebelum document dihapus (BR-10).
     */
    protected static function booted(): void
    {
        static::deleting(function (MilestoneDocument $doc) {
            if ($doc->file_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($doc->file_path);
            }
        });
    }

    /**
     * Milestone parent dari document ini.
     */
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }
}
