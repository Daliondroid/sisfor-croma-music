<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $primaryKey = 'id_guru';

    protected $fillable = [
        'id_user', 'nama_guru', 'nomor_hp', 'status_aktif',
    ];

    protected $casts = ['status_aktif' => 'boolean'];

    public function user()      { return $this->belongsTo(User::class, 'id_user'); }
    public function jadwals()   { return $this->hasMany(Jadwal::class, 'id_guru'); }
    public function presensis() { return $this->hasMany(Presensi::class, 'id_guru'); }

    public function spesialisasis()
    {
        return $this->belongsToMany(Spesialisasi::class, 'guru_spesialisasi', 'id_guru', 'id_spesialisasi');
    }
}
