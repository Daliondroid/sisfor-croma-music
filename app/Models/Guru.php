<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $primaryKey = 'id_guru';

    protected $fillable = [
        'id_user', 'nama_guru', 'nomor_hp', 'status_aktif'
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    // ── Relasi ─────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Spesialisasi guru — HasMany ke guru_spesialisasis.
     * ERD v12: GURU ||--o{ GURU_SPESIALISASI (nama_spesialisasi disimpan langsung sebagai string)
     * BUKAN many-to-many pivot — tidak ada tabel master spesialisasi di ERD v12.
     */
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