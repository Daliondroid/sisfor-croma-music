<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramKursus extends Model
{
    protected $table = 'program_kursus';
    protected $primaryKey = 'id_program';

    protected $fillable = ['nama_program', 'deskripsi', 'tipe_les', 'biaya_kursus', 'is_active'];

    public function spps(): HasMany
    {
        return $this->hasMany(Spp::class, 'id_program');
    }
}