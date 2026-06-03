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
        Schema::create('spps', function (Blueprint $table) {
            $table->id('id_spp');
            $table->foreignId('id_murid')->constrained('murids', 'id_murid')->cascadeOnDelete();
            $table->foreignId('id_program')->constrained('program_kursus', 'id_program');
            $table->date('periode_tagihan');
            $table->decimal('nominal_tagihan', 15, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->enum('tipe_les', ['Onsite', 'Home Private']);
            $table->enum('status_bayar', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spps');
    }
};
