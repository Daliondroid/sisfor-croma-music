<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Spp extends Model
{
    protected $table = 'spps';
    protected $primaryKey = 'id_spp';

    protected $fillable = [
        'id_murid', 'id_program', 'periode_tagihan', 'nominal_tagihan', 
        'tanggal_jatuh_tempo', 'tipe_les', 'status_bayar'
    ];

    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid');
    }

    public function programKursus(): BelongsTo
    {
        return $this->belongsTo(ProgramKursus::class, 'id_program');
    }

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