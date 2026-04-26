<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Kelas extends Model
{
    protected $table      = 'kelas';
    protected $primaryKey = 'id_kelas';
 
    protected $fillable = [
        'id_program',
        'nama_kelas',
        'kapasitas',
        'tipe_les',
    ];
 
    public function program()
    {
        return $this->belongsTo(ProgramKursus::class, 'id_program', 'id_program');
    }
 
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'id_kelas', 'id_kelas');
    }
}