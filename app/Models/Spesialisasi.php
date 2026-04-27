<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spesialisasi extends Model
{
    // Beri tahu Laravel untuk menggunakan tabel 'spesialisasi', bukan 'spesialisasis'
    protected $table = 'spesialisasi';
    
    // Sesuaikan primary key
    protected $primaryKey = 'id_spesialisasi';

    protected $fillable = [
        'nama_spesialisasi'
    ];

    // Relasi balik (Many-to-Many) ke tabel gurus
    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_spesialisasi', 'id_spesialisasi', 'id_guru');
    }
}