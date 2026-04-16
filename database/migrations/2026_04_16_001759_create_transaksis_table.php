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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->foreignId('id_spp')->constrained('spps', 'id_spp');
            $table->foreignId('id_murid')->constrained('murids', 'id_murid');
            $table->foreignId('id_admin')->constrained('admins', 'id_admin');
            $table->string('file_bukti_transfer'); // path file
            $table->decimal('nominal_bayar', 12, 2);
            $table->date('tanggal_bayar');
            $table->date('tanggal_konfirmasi')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
