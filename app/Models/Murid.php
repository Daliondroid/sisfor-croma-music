<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Murid extends Model
{
    protected $primaryKey = 'id_murid';

    protected $fillable = [
        'id_user', 'nama_murid', 'tanggal_lahir', 'alamat',
        'nomor_hp', 'nama_orang_tua', 'status_aktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_aktif' => 'boolean',
    ];

    // ── Relasi langsung ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * SPP milik murid ini.
     * MURID ||--o{ SPP (ERD v12)
     */
    public function spps(): HasMany
    {
        return $this->hasMany(Spp::class, 'id_murid');
    }

    // ── Relasi melalui SPP ─────────────────────────────────────

    /**
     * Jadwal murid ini — ditempuh lewat tabel SPP.
     * MURID → SPP → JADWAL (ERD v12: tidak ada id_murid langsung di JADWAL)
     */
    public function jadwals(): HasManyThrough
    {
        return $this->hasManyThrough(
            Jadwal::class,
            Spp::class,
            'id_murid',  // FK di tabel spps
            'id_spp',    // FK di tabel jadwals
            'id_murid',  // PK murid
            'id_spp'     // PK spp
        );
    }

    /**
     * Monthly report murid ini — ditempuh lewat tabel SPP.
     * MURID → SPP → MONTHLY_REPORT (ERD v12)
     */
    public function monthlyReports(): HasManyThrough
    {
        return $this->hasManyThrough(
            MonthlyReport::class,
            Spp::class,
            'id_murid',
            'id_spp',
            'id_murid',
            'id_spp'
        );
    }

    // ── Helper methods ─────────────────────────────────────────

    /**
     * Ambil SPP pada bulan/periode tertentu (atau bulan ini jika tidak diisi).
     * Dipakai di dashboard: $murid->sppBulanIni()
     *
     * @param  string|null  $bulan  format 'Y-m', default bulan ini
     */
    public function sppBulanIni(?string $bulan = null): ?Spp
    {
        $bulan = $bulan ?? now()->format('Y-m');
        $startDate = Carbon::parse($bulan.'-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($bulan.'-01')->endOfMonth()->toDateString();

        return $this->spps()
            ->whereBetween('periode_tagihan', [$startDate, $endDate])
            ->first();
    }
}
