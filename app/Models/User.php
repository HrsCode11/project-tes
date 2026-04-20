<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'nik',
        'email',
        'password',
        'no_rt',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Helper role
    public function isKetuaRT(): bool
    {
        return $this->role === 'ketua_rt';
    }

    public function isBendahara(): bool
    {
        return $this->role === 'bendahara';
    }

    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}