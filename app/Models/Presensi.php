<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $primaryKey = 'id_presensi';

    protected $fillable = [
        'id_jadwal', 'id_guru', 'id_murid',
        'tanggal', 'sesi_ke', 'status_murid', 'input_by',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function jadwal()      { return $this->belongsTo(Jadwal::class, 'id_jadwal'); }
    public function guru()        { return $this->belongsTo(Guru::class, 'id_guru'); }
    public function murid()       { return $this->belongsTo(Murid::class, 'id_murid'); }
    public function inputOleh()   { return $this->belongsTo(User::class, 'input_by'); }
    public function materiKbm()   { return $this->hasOne(MateriKbm::class, 'id_presensi'); }
    public function videoProgres(){ return $this->hasOne(VideoProgres::class, 'id_presensi'); }
}