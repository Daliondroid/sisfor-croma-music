<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresMurid extends Model
{
    protected $table = 'progres_murids';
    protected $primaryKey = 'id_progres';

    protected $fillable = ['id_jadwal', 'url_foto', 'materi_diajarkan', 'catatan_perkembangan'];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal');
    }
}