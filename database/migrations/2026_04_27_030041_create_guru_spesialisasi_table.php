<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_spesialisasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_guru');
            $table->unsignedBigInteger('id_spesialisasi');
            
            // Membuat foreign key
            $table->foreign('id_guru')->references('id_guru')->on('gurus')->onDelete('cascade');
            $table->foreign('id_spesialisasi')->references('id_spesialisasi')->on('spesialisasi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_spesialisasi');
    }
};