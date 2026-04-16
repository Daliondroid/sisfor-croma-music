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
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id('id_report');
            $table->foreignId('id_murid')->constrained('murids', 'id_murid')->cascadeOnDelete();
            $table->string('bulan', 7); // format: 2024-01
            $table->integer('total_hadir')->default(0);
            $table->integer('total_alpa')->default(0);
            $table->integer('total_izin')->default(0);
            $table->float('persentase_kehadiran')->default(0);
            $table->text('catatan_guru')->nullable();
            $table->timestamps(); // generated_at = created_at

            $table->unique(['id_murid', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
