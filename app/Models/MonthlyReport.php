<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyReport extends Model
{
    protected $table = 'monthly_reports';
    protected $primaryKey = 'id_report';

    protected $fillable = ['id_spp', 'periode_bulan', 'url_video', 'skor', 'evaluasi_bulanan'];

    public function spp(): BelongsTo
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'id_report');
    }
}