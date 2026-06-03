<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $primaryKey = 'id_guru';

    protected $fillable = ['id_user', 'nama_guru', 'nomor_hp', 'status_aktif'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function spesialisasis(): HasMany
    {
        return $this->hasMany(GuruSpesialisasi::class, 'id_guru');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'id_guru');
    }

    public function honors(): HasMany
    {
        return $this->hasMany(HonorGuru::class, 'id_guru');
    }
}