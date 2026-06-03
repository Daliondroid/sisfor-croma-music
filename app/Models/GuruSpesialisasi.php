<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruSpesialisasi extends Model
{
    protected $table = 'guru_spesialisasis';

    protected $fillable = ['id_guru', 'nama_spesialisasi'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}