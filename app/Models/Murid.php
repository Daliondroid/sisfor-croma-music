<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    protected $primaryKey = 'id_murid';

    protected $fillable = [
        'id_user', 'nama_murid', 'tanggal_lahir', 'alamat',
        'nomor_hp', 'nama_orang_tua', 'status_aktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_aktif'  => 'boolean',
    ];

    public function user()          { return $this->belongsTo(User::class, 'id_user'); }
    public function jadwals()       { return $this->hasMany(Jadwal::class, 'id_murid'); }
    public function presensis()     { return $this->hasMany(Presensi::class, 'id_murid'); }
    public function spps()          { return $this->hasMany(Spp::class, 'id_murid'); }
    public function transaksis()    { return $this->hasMany(Transaksi::class, 'id_murid'); }
    public function monthlyReports(){ return $this->hasMany(MonthlyReport::class, 'id_murid'); }

    // Helper: SPP bulan berjalan
    public function sppBulanIni()
    {
        return $this->spps()->where('bulan_tagihan', now()->format('Y-m'))->first();
    }
}
