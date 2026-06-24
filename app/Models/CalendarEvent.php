<?php

namespace App\Models;

use Database\Factories\CalendarEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Event kalender custom — event yang tidak terikat entitas lain.
 * Event dari bimbingan, task, dan milestone dibaca langsung dari
 * tabel masing-masing (BR-08), bukan diduplikasi ke sini.
 * Ref: SRS 3.9
 */
class CalendarEvent extends Model
{
    /** @use HasFactory<CalendarEventFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'event_date',
        'color',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }

    /**
     * Pemilik event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
