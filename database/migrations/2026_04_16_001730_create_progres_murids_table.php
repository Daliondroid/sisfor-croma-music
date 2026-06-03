<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progres_murids', function (Blueprint $table) {
            $table->id('id_progres');
            $table->foreignId('id_jadwal')->constrained('jadwals', 'id_jadwal')->cascadeOnDelete();
            $table->string('url_foto')->nullable();
            $table->text('materi_diajarkan');
            $table->text('catatan_perkembangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_murids');
    }
};