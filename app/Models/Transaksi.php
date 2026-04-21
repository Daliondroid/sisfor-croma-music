<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table      = 'transaksis';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_spp', 'id_murid', 'id_admin', 'file_bukti_transfer',
        'nominal_bayar', 'tanggal_bayar', 'tanggal_konfirmasi', 'catatan_admin',
    ];

    protected $casts = [
        'tanggal_bayar'      => 'date',
        'tanggal_konfirmasi' => 'date',
    ];

    public function spp()   { return $this->belongsTo(Spp::class, 'id_spp'); }
    public function murid() { return $this->belongsTo(Murid::class, 'id_murid'); }
    public function admin() { return $this->belongsTo(Admin::class, 'id_admin'); }
}

