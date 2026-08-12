<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HonorGuru extends Model
{
    protected $table = 'honor_gurus';

    protected $primaryKey = 'id_honor';

    protected $fillable = [
        'id_guru', 'id_admin', 'tanggal_pencairan', 'jumlah_pertemuan',
        'jumlah_honor', 'file_bukti_transfer', 'status_bayar', 'catatan',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'id_honor');
    }
}
