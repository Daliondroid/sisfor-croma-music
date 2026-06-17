<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyReport extends Model
{
    protected $table = 'monthly_reports';
    protected $primaryKey = 'id_report';

    protected $fillable = [
        'id_spp', 'periode_bulan', 'url_video', 'skor', 'evaluasi_bulanan'
    ];

    protected $casts = [
        'periode_bulan' => 'date',
    ];

    // ── Relasi ─────────────────────────────────────────────────

    /**
     * SPP yang dievaluasi oleh report ini.
     * ERD v12: SPP ||--o| MONTHLY_REPORT
     */
    public function spp(): BelongsTo
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    /**
     * Jadwal-jadwal yang terangkum dalam report ini.
     * ERD v12: MONTHLY_REPORT ||--o{ JADWAL (via id_report di jadwals)
     */
    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'id_report');
    }

    // ── Helper accessor ────────────────────────────────────────

    /**
     * Akses murid dari monthly report lewat SPP.
     * Contoh: $report->murid
     */
    public function getMuridAttribute(): ?Murid
    {
        return $this->spp?->murid;
    }
}