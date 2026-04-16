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
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('jenis_notifikasi'); // e.g. 'spp_jatuh_tempo', 'absensi_belum_diisi'
            $table->text('pesan');
            $table->enum('status_baca', ['belum_dibaca', 'sudah_dibaca'])->default('belum_dibaca');
            $table->unsignedBigInteger('id_referensi')->nullable(); // ID entitas terkait
            $table->timestamps(); // tanggal_dibuat = created_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
