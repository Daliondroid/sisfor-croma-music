<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id('id_report');
            $table->foreignId('id_spp')->constrained('spps', 'id_spp')->cascadeOnDelete();
            $table->date('periode_bulan');
            $table->string('url_video')->nullable();
            $table->enum('skor', ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-'])->nullable();
            $table->text('evaluasi_bulanan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
