<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel chapter_tasks — sub-task dalam setiap bab skripsi.
     * Ref: SRS 3.5
     * Cascade: hapus chapter → hapus semua task-nya (BR-10)
     */
    public function up(): void
    {
        Schema::create('chapter_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();  // BR-10
            $table->string('title', 255);
            $table->string('status')->default('not_started');  // TaskStatus enum values
            $table->integer('order')->default(0);
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();  // BR-02, BR-03: auto-managed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('chapter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_tasks');
    }
};
