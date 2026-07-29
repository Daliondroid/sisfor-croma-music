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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_admin')->constrained('admins', 'id_admin');
            $table->foreignId('id_guru')->constrained('gurus', 'id_guru');
            $table->foreignId('id_spp')->constrained('spps', 'id_spp')->cascadeOnDelete();
            $table->foreignId('id_honor')->nullable()->constrained('honor_gurus', 'id_honor'); // Bisa diisi nanti saat payout
            $table->foreignId('id_report')->nullable()->constrained('monthly_reports', 'id_report'); // Bisa diisi saat akhir bulan
            
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('sesi_ke');
            $table->enum('status_jadwal', ['Sesuai Jadwal', 'Reschedule'])->default('Sesuai Jadwal');
            $table->enum('status_kehadiran_murid', ['Hadir', 'Tidak Hadir'])->nullable();
            $table->enum('status_kehadiran_guru', ['Hadir', 'Tidak Hadir'])->nullable();
            $table->timestamp('waktu_presensi_diisi')->nullable();
            $table->enum('presensi_diisi_oleh', ['Guru', 'Murid'])->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users', 'id_user');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
