<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriKbm extends Model
{
    protected $table      = 'materi_kbms';
    protected $primaryKey = 'id_materi';

    protected $fillable = [
        'id_presensi', 'materi_diajarkan', 'catatan_perkembangan', 'tingkat_progres',
    ];

    public function presensi() { return $this->belongsTo(Presensi::class, 'id_presensi'); }
}