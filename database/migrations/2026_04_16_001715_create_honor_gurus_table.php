<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honor_gurus', function (Blueprint $table) {
            $table->id('id_honor');
            $table->foreignId('id_guru')->constrained('gurus', 'id_guru')->cascadeOnDelete();
            $table->foreignId('id_admin')->nullable()->constrained('admins', 'id_admin');
            $table->date('tanggal_pencairan')->nullable();
            $table->integer('jumlah_pertemuan');
            $table->decimal('jumlah_honor', 15, 2);
            $table->string('file_bukti_transfer')->nullable();
            $table->enum('status_bayar', ['Belum Lunas', 'Siap Dibayar', 'Lunas'])->default('Belum Lunas');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honor_gurus');
    }
};
