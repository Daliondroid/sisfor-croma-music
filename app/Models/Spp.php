<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $table      = 'spps';
    protected $primaryKey = 'id_spp';

    protected $fillable = [
        'id_murid', 'bulan_tagihan', 'nominal_tagihan',
        'tanggal_jatuh_tempo', 'status_bayar',
    ];

    protected $casts = ['tanggal_jatuh_tempo' => 'date'];

    public function murid()     { return $this->belongsTo(Murid::class, 'id_murid'); }
    public function transaksi() { return $this->hasOne(Transaksi::class, 'id_spp'); }

    public function sudahBayar(): bool
    {
        return $this->status_bayar === 'sudah_bayar';
    }
}
