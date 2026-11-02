<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    protected $table = 'warga';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'no_hp',
        'no_rt',
        'alamat',
        'status',
    ];

    // Normalisasi ke format 628xxx.
    public static function normalisasiNoHp(?string $value): ?string
    {
        if (!$value) return null;

        $nomor = preg_replace('/\D/', '', $value);

        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        } elseif (str_starts_with($nomor, '8')) {
            $nomor = '62' . $nomor;
        }

        return $nomor;
    }

    protected function noHp(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => self::normalisasiNoHp($value),
        );
    }

    // Satu warga punya paling banyak satu akun login
    public function user()
    {
        return $this->hasOne(User::class);
    }

    // Hanya warga aktif yang ditagih: Warga::aktif()->get()
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function alasanTidakBisaDihapus(): ?string
    {
        if ($this->user()->exists()) {
            return 'Warga ini masih punya akun login. Hapus akunnya dulu di Kelola Akun.';
        }

        if ($this->tagihan()->exists() || $this->pembayaran()->exists()) {
            return 'Warga ini punya riwayat tagihan/pembayaran. Ubah statusnya menjadi pindah atau meninggal agar riwayatnya tetap utuh.';
        }

        return null;
}
}