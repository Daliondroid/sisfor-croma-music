<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table      = 'notifikasis';
    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_user', 'jenis_notifikasi', 'pesan', 'status_baca', 'id_referensi',
    ];

    public function user() { return $this->belongsTo(User::class, 'id_user'); }

    public function markAsRead(): void
    {
        $this->update(['status_baca' => 'sudah_dibaca']);
    }
}