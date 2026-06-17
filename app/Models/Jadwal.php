<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Jadwal extends Model
{
    protected $table = 'jadwals';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_admin', 'id_guru', 'id_spp', 'id_honor', 'id_report',
        'tanggal', 'jam_mulai', 'jam_selesai', 'sesi_ke',
        'status_jadwal', 'status_kehadiran_murid', 'status_kehadiran_guru',
        'waktu_presensi_diisi', 'presensi_diisi_oleh', 'is_active',
    ];

    protected $casts = [
        'tanggal'              => 'date',
        'waktu_presensi_diisi' => 'datetime',
        'is_active'            => 'boolean',
    ];

    // ── Relasi ─────────────────────────────────────────────────

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    /**
     * SPP yang mencakup sesi ini.
     * Lewat SPP inilah bisa diakses murid: $jadwal->spp->murid
     */
    public function spp(): BelongsTo
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    public function honorGuru(): BelongsTo
    {
        return $this->belongsTo(HonorGuru::class, 'id_honor');
    }

    public function monthlyReport(): BelongsTo
    {
        return $this->belongsTo(MonthlyReport::class, 'id_report');
    }

    public function progresMurid(): HasOne
    {
        return $this->hasOne(ProgresMurid::class, 'id_jadwal');
    }

    // ── Helper accessor ────────────────────────────────────────

    /**
     * Akses murid dari jadwal lewat SPP.
     * Contoh: $jadwal->murid  (menggantikan relasi langsung yang tidak ada di ERD v12)
     */
    public function getMuridAttribute(): ?Murid
    {
        return $this->spp?->murid;
    }
}