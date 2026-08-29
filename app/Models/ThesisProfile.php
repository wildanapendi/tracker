<?php

namespace App\Models;

use Database\Factories\ThesisProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Profil skripsi — one-to-one dengan User.
 * Menyimpan metadata skripsi: judul, prodi, pembimbing, dll.
 * Ref: SRS 3.3
 */
class ThesisProfile extends Model
{
    /** @use HasFactory<ThesisProfileFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'study_program',
        'faculty',
        'supervisor_name',
        'co_supervisor_name',
        'start_date',
        'target_completion',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'target_completion' => 'date',
        ];
    }

    /**
     * Pemilik profil skripsi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
