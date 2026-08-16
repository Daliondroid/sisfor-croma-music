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
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->unique(['id_spp', 'periode_bulan'], 'uniq_monthly_reports_spp_periode');
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->index('id_honor', 'idx_jadwals_id_honor');
            $table->index('id_report', 'idx_jadwals_id_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->dropUnique('uniq_monthly_reports_spp_periode');
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropIndex('idx_jadwals_id_honor');
            $table->dropIndex('idx_jadwals_id_report');
        });
    }
};
