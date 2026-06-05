<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    protected $table = 'transaksis';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_spp', 'id_admin', 'file_bukti_transfer', 'nominal_bayar',
        'tanggal_bayar', 'tanggal_konfirmasi', 'catatan_admin'
        // CATATAN: id_murid sudah dihapus sejak ERD v12.
        // Murid dapat diakses via: $transaksi->spp->murid
    ];

    protected $casts = [
        'tanggal_bayar'       => 'date',
        'tanggal_konfirmasi'  => 'date',
        'nominal_bayar'       => 'decimal:2',
    ];

    // ── Relasi ─────────────────────────────────────────────────

    public function spp(): BelongsTo
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    // ── Helper accessor ────────────────────────────────────────

    /**
     * Akses murid dari transaksi lewat SPP.
     * Menggantikan relasi id_murid yang sudah dihapus di ERD v12.
     */
    public function getMuridAttribute(): ?Murid
    {
        return $this->spp?->murid;
    }
}