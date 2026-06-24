<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel thesis_profiles — profil skripsi (one-to-one dengan users).
     * Ref: SRS 3.3
     */
    public function up(): void
    {
        Schema::create('thesis_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title', 500)->nullable();
            $table->string('study_program', 255)->nullable();
            $table->string('faculty', 255)->nullable();
            $table->string('supervisor_name', 255)->nullable();
            $table->string('co_supervisor_name', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->date('target_completion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_profiles');
    }
};
