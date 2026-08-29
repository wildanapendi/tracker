<?php

namespace App\Models;

use App\Policies\ChapterPolicy;
use Database\Factories\ChapterFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Bab skripsi — memiliki banyak sub-task (ChapterTask).
 * Urutan dan bobot dapat dikustomisasi per bab.
 * Ref: SRS 3.4
 */
#[UsePolicy(ChapterPolicy::class)]
class Chapter extends Model
{
    /** @use HasFactory<ChapterFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'order',
        'weight',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'weight' => 'decimal:2',
        ];
    }

    /**
     * Boot: default ordering by `order` column.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('order');
        });
    }

    /**
     * Pemilik bab.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sub-task dalam bab ini.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(ChapterTask::class)->orderBy('order');
    }
}
