<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel milestone_documents — persyaratan berkas per milestone.
     * Ref: SRS 3.8
     * Cascade: hapus milestone → hapus semua documents-nya (BR-10)
     * File fisik di-cleanup via model boot event, bukan di migration.
     */
    public function up(): void
    {
        Schema::create('milestone_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();  // BR-10
            $table->string('title', 255);
            $table->boolean('is_completed')->default(false);
            $table->string('file_path', 500)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->integer('order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('milestone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_documents');
    }
};
