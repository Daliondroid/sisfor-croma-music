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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id('id_presensi');
            $table->foreignId('id_jadwal')->constrained('jadwals', 'id_jadwal');
            $table->foreignId('id_guru')->constrained('gurus', 'id_guru');
            $table->foreignId('id_murid')->constrained('murids', 'id_murid');
            $table->date('tanggal');
            $table->integer('sesi_ke');
            $table->enum('status_murid', ['hadir', 'alpa', 'izin'])->default('hadir');
            $table->foreignId('input_by')->nullable()->constrained('users', 'id_user'); // siapa yang input (guru/murid)
            $table->timestamps(); // created_at sebagai timestamp presensi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
