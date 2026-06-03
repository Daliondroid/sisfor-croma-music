<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Murid extends Model
{
    protected $primaryKey = 'id_murid';

    protected $fillable = [
        'id_user', 'nama_murid', 'tanggal_lahir', 'alamat', 'nomor_hp', 'nama_orang_tua', 'status_aktif'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function spps(): HasMany
    {
        return $this->hasMany(Spp::class, 'id_murid');
    }
}