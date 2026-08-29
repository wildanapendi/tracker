<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel calendar_events — event kalender custom (bukan dari entitas lain).
     * Ref: SRS 3.9
     * Event dari bimbingan, task, dan milestone dibaca langsung dari tabel
     * masing-masing (BR-08), bukan diduplikasi ke sini.
     */
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->string('color', 7)->nullable();  // Hex color: #RRGGBB
            $table->timestamps();

            $table->index('user_id');
            $table->index('event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
