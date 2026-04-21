<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $primaryKey = 'id_admin';

    protected $fillable = ['id_user', 'nama_admin'];

    public function user()      { return $this->belongsTo(User::class, 'id_user'); }
    public function jadwals()   { return $this->hasMany(Jadwal::class, 'id_admin'); }
    public function transaksis(){ return $this->hasMany(Transaksi::class, 'id_admin'); }
}
