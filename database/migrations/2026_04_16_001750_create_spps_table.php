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
            $table->string('bulan_tagihan', 7); // format: 2024-01
            $table->decimal('nominal_tagihan', 12, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status_bayar', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar');
            $table->timestamps();

            $table->unique(['id_murid', 'bulan_tagihan']); // satu tagihan per murid per bulan
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
