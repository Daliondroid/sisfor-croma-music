<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_admin', 'id_guru', 'id_murid', 'id_kelas',
        'hari', 'jam_mulai', 'jam_selesai', 'lokasi',
        'tanggal_berlaku', 'is_active',
    ];

    protected $casts = [
        'tanggal_berlaku' => 'date',
        'is_active'       => 'boolean',
    ];

    public function admin()     { return $this->belongsTo(Admin::class, 'id_admin'); }
    public function guru()      { return $this->belongsTo(Guru::class, 'id_guru'); }
    public function murid()     { return $this->belongsTo(Murid::class, 'id_murid'); }
    public function kelas()     { return $this->belongsTo(Kelas::class, 'id_kelas'); }
    public function presensis() { return $this->hasMany(Presensi::class, 'id_jadwal'); }
}
