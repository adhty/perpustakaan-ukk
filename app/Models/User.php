<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'role', 'nis', 'kelas', 'no_hp', 'alamat', 'foto', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // Relasi
    public function pinjams()
    {
        return $this->hasMany(Pinjam::class);
    }

    public function aktivePinjam()
    {
        return $this->pinjams()->where('status', 'dipinjam');
    }

    // Accessor
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function getIsSiswaAttribute(): bool
    {
        return $this->role === 'siswa';
    }
}
