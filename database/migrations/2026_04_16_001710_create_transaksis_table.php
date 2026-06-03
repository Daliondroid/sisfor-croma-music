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
            $table->foreignId('id_spp')->constrained('spps', 'id_spp')->cascadeOnDelete();
            $table->foreignId('id_admin')->nullable()->constrained('admins', 'id_admin');
            $table->string('file_bukti_transfer');
            $table->decimal('nominal_bayar', 15, 2);
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
