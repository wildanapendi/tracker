<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel guidances — jadwal bimbingan dengan dosen pembimbing.
     * Ref: SRS 3.6
     */
    public function up(): void
    {
        Schema::create('guidances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('scheduled');  // GuidanceStatus enum values
            $table->text('agenda')->nullable();
            $table->text('result')->nullable();
            $table->text('action_items')->nullable();
            $table->string('location', 255)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guidances');
    }
};
