<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class ProgramKursus extends Model
{
    protected $table      = 'program_kursus';
    protected $primaryKey = 'id_program';
 
    protected $fillable = [
        'nama_program',
        'deskripsi',
        'tipe_les',
        'is_active',
    ];
 
    protected $casts = [
        'is_active' => 'boolean',
    ];
 
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_program', 'id_program');
    }
}