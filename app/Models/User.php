<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Data penduduk (nama, NIK, RT, No. HP) ada di tabel warga.
    protected $fillable = [
        'warga_id',
        'email',
        'password',
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

    // Data penduduk yang terkait dengan akun ini
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    // Nama untuk ditampilkan. Akun tanpa data warga memakai email sebagai cadangan.
    protected function namaLengkap(): Attribute
    {
        return Attribute::get(fn () => $this->warga?->nama_lengkap ?? $this->email);
    }

    // ── Helper role ──
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

    // Data yang pernah dicatat oleh akun ini
public function iuranDibuat()
{
    return $this->hasMany(Iuran::class, 'dibuat_oleh');
}

public function pemasukanDicatat()
{
    return $this->hasMany(Pemasukan::class, 'dicatat_oleh');
}

public function pengeluaranDicatat()
{
    return $this->hasMany(Pengeluaran::class, 'dicatat_oleh');
}

public function alasanTidakBisaDihapus(): ?string
{
    if ($this->iuranDibuat()->exists()
        || $this->pemasukanDicatat()->exists()
        || $this->pengeluaranDicatat()->exists()) {
        return 'Akun ini pernah mencatat iuran/pemasukan/pengeluaran. Nonaktifkan saja agar jejak pencatatnya tetap ada.';
    }

    return null;
}
}