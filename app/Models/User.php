<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username', 'name', 'email', 'password', 'role', 'is_active', 'foto_profil',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // Relasi
    public function murid()       { return $this->hasOne(Murid::class, 'id_user'); }
    public function guru()        { return $this->hasOne(Guru::class, 'id_user'); }
    public function admin()       { return $this->hasOne(Admin::class, 'id_user'); }
    public function notifikasis() { return $this->hasMany(Notifikasi::class, 'id_user'); }
}