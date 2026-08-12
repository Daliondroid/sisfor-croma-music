<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_spesialisasis', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel gurus
            $table->foreignId('id_guru')->constrained('gurus', 'id_guru')->cascadeOnDelete();
            // Langsung simpan nama spesialisasi sesuai ERD v12
            $table->string('nama_spesialisasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_spesialisasis');
    }
};
