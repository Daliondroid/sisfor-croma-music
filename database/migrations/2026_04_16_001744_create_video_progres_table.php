<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_progres', function (Blueprint $table) {
            $table->id('id_video');
            $table->foreignId('id_presensi')->constrained('presensis', 'id_presensi')->cascadeOnDelete();
            $table->string('url_video');
            $table->enum('platform', ['google_drive', 'youtube_private', 'lainnya'])->default('google_drive');
            $table->string('deskripsi_video')->nullable();
            $table->timestamps(); // uploaded_at = created_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_progres');
    }
};
