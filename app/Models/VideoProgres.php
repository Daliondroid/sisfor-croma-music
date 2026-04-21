<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoProgres extends Model
{
    protected $table      = 'video_progres';
    protected $primaryKey = 'id_video';

    protected $fillable = [
        'id_presensi', 'url_video', 'platform', 'deskripsi_video',
    ];

    public function presensi() { return $this->belongsTo(Presensi::class, 'id_presensi'); }
}
