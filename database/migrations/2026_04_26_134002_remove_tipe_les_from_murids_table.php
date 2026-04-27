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
        Schema::table('murids', function (Blueprint $table) {
            // Menghapus kolom tipe_les
            $table->dropColumn('tipe_les');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('murids', function (Blueprint $table) {
            // Mengembalikan kolom tipe_les jika sewaktu-waktu migration di-rollback
            // Sesuaikan isi enum dengan yang sebelumnya Anda buat
            $table->enum('tipe_les', ['opsi1', 'opsi2'])->nullable(); 
        });
    }
};