<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Spp extends Model
{
    protected $table = 'spps';

    protected $primaryKey = 'id_spp';

    protected $fillable = [
        'id_murid', 'id_program', 'periode_tagihan', 'nominal_tagihan',
        'tanggal_jatuh_tempo', 'tipe_les', 'status_bayar',
    ];

    protected $casts = [
        'periode_tagihan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'nominal_tagihan' => 'decimal:2',
    ];

    // ── Nilai enum status_bayar (sesuai ERD v12 & migration) ──
    const STATUS_LUNAS = 'Lunas';

    const STATUS_BELUM_LUNAS = 'Belum Lunas';

    // ── Helper method ──────────────────────────────────────────

    /**
     * Cek apakah SPP ini sudah lunas.
     * Dipakai di views: $spp->sudahBayar()
     */
    public function sudahBayar(): bool
    {
        return $this->status_bayar === self::STATUS_LUNAS;
    }

    // ── Query Scopes ───────────────────────────────────────────

    /**
     * Filter SPP yang sudah lunas.
     * Pakai: Spp::sudahBayar()->get()
     */
    public function scopeSudahBayar($query)
    {
        return $query->where('status_bayar', self::STATUS_LUNAS);
    }

    /**
     * Filter SPP yang belum lunas.
     * Pakai: Spp::belumLunas()->get()
     */
    public function scopeBelumLunas($query)
    {
        return $query->where('status_bayar', self::STATUS_BELUM_LUNAS);
    }

    /**
     * Filter berdasarkan periode (format: 'Y-m' atau date).
     * Pakai: Spp::periodeTagihan(now()->format('Y-m'))->get()
     */
    public function scopePeriodeTagihan($query, string $bulan)
    {
        // $bulan format 'Y-m', cocokkan ke kolom date periode_tagihan
        return $query->whereYear('periode_tagihan', substr($bulan, 0, 4))
            ->whereMonth('periode_tagihan', substr($bulan, 5, 2));
    }

    // ── Relasi ─────────────────────────────────────────────────

    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }

    public function programKursus(): BelongsTo
    {
        return $this->belongsTo(ProgramKursus::class, 'id_program');
    }

    /**
     * SPP hanya punya satu transaksi aktif (sesuai ERD v12: SPP ||--o| TRANSAKSI).
     * Gunakan hasOne agar $spp->transaksi mengembalikan single object, bukan collection.
     */
    public function transaksi(): HasOne
    {
        return $this->hasOne(Transaksi::class, 'id_spp')->latestOfMany('id_transaksi');
    }

    /**
     * Untuk kasus butuh semua history transaksi (re-upload):
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_spp');
    }

    public function monthlyReports(): HasMany
    {
        return $this->hasMany(MonthlyReport::class, 'id_spp');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'id_spp');
    }
}
