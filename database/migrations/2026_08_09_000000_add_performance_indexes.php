<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add performance indexes to high-traffic tables.
     *
     * Indexes target columns frequently used in:
     *  – Dashboard aggregate counts
     *  – SPP billing filters (status + period)
     *  – Schedule lookups (date + active flag)
     *  – Honor/salary payout queries
     */
    public function up(): void
    {
        // ── spps ───────────────────────────────────────────────────
        Schema::table('spps', function (Blueprint $table) {
            // Dashboard: count Belum Lunas per month → composite covers both filters
            $table->index(['status_bayar', 'periode_tagihan'], 'idx_spps_status_periode');

            // SppController index filter by murid + status
            $table->index(['id_murid', 'status_bayar'], 'idx_spps_murid_status');

            // Order by due date in billing list
            $table->index('tanggal_jatuh_tempo', 'idx_spps_jatuh_tempo');
        });

        // ── jadwals ────────────────────────────────────────────────
        Schema::table('jadwals', function (Blueprint $table) {
            // Dashboard: today's schedule — tanggal + is_active + status_jadwal
            $table->index(['tanggal', 'is_active', 'status_jadwal'], 'idx_jadwals_tanggal_active_status');

            // Guru-specific schedule lookups
            $table->index(['id_guru', 'tanggal'], 'idx_jadwals_guru_tanggal');

            // SPP-based joins from reports/absensi
            $table->index(['id_spp', 'is_active'], 'idx_jadwals_spp_active');
        });

        // ── transaksis ─────────────────────────────────────────────
        Schema::table('transaksis', function (Blueprint $table) {
            // Dashboard: pending confirmations — id_spp + konfirmasi null check
            $table->index(['id_spp', 'tanggal_konfirmasi'], 'idx_transaksis_spp_konfirmasi');
        });

        // ── honor_gurus ────────────────────────────────────────────
        Schema::table('honor_gurus', function (Blueprint $table) {
            // Dashboard: ready-to-pay honor per guru
            $table->index(['id_guru', 'status_bayar'], 'idx_honor_gurus_guru_status');
        });

        // ── murids ─────────────────────────────────────────────────
        Schema::table('murids', function (Blueprint $table) {
            // Dashboard: totalMurid active count
            $table->index('status_aktif', 'idx_murids_status_aktif');
        });

        // ── gurus ──────────────────────────────────────────────────
        Schema::table('gurus', function (Blueprint $table) {
            // Dashboard: totalGuru active count
            $table->index('status_aktif', 'idx_gurus_status_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spps', function (Blueprint $table) {
            $table->dropIndex('idx_spps_status_periode');
            $table->dropIndex('idx_spps_murid_status');
            $table->dropIndex('idx_spps_jatuh_tempo');
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropIndex('idx_jadwals_tanggal_active_status');
            $table->dropIndex('idx_jadwals_guru_tanggal');
            $table->dropIndex('idx_jadwals_spp_active');
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex('idx_transaksis_spp_konfirmasi');
        });

        Schema::table('honor_gurus', function (Blueprint $table) {
            $table->dropIndex('idx_honor_gurus_guru_status');
        });

        Schema::table('murids', function (Blueprint $table) {
            $table->dropIndex('idx_murids_status_aktif');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropIndex('idx_gurus_status_aktif');
        });
    }
};
