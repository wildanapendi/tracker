<?php

namespace App\Models;

use App\Enums\GuidanceStatus;
use App\Policies\GuidancePolicy;
use Database\Factories\GuidanceFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jadwal bimbingan dengan dosen pembimbing.
 * Mencatat agenda, hasil, action items, dan status bimbingan.
 * Ref: SRS 3.6
 */
#[UsePolicy(GuidancePolicy::class)]
class Guidance extends Model
{
    /** @use HasFactory<GuidanceFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'scheduled_at',
        'status',
        'agenda',
        'result',
        'action_items',
        'location',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => GuidanceStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }

    /**
     * Pemilik jadwal bimbingan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
