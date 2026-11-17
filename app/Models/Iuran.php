<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iuran extends Model
{

    protected $table = 'iuran';

    // Kolom yang boleh diisi via mass assignment
    protected $fillable = [
        'dibuat_oleh',
        'nama_iuran',
        'nominal',
        'keterangan',
        'deadline',
        'status',
    ];

    // Cast otomatis — deadline jadi Carbon object, bisa pakai ->format('d/m/Y')
    protected $casts = [
        'deadline' => 'date',
        'nominal'  => 'decimal:2',
    ];

    // Relasi: iuran dibuat oleh 1 user (bendahara)
    // hasMany = 1 iuran punya banyak tagihan
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    // Semua pembayaran dari seluruh tagihan pada iuran ini
    public function pembayaran()
    {
        return $this->hasManyThrough(Pembayaran::class, Tagihan::class);
    }

    // Mengembalikan alasan kalau tidak boleh dihapus, null kalau boleh
    public function alasanTidakBisaDihapus(): ?string
    {
        if ($this->pembayaran()->exists()) {
            return 'Iuran ini sudah memiliki catatan pembayaran sehingga tidak dapat dihapus. '
                . 'Ubah statusnya menjadi ditutup agar riwayat keuangan tetap utuh.';
        }

        return null;
    }

    // belongsTo = iuran ini milik 1 user (pembuatnya)
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // Helper: hitung berapa tagihan yang sudah lunas
    public function jumlahLunas(): int
    {
        return $this->tagihan()->where('status', 'lunas')->count();
    }

    // Helper: hitung total warga yang dapat tagihan
    public function totalWarga(): int
    {
        return $this->tagihan()->count();
    }

    // Helper: hitung persentase lunas
    public function persentaseLunas(): int
    {
        if ($this->totalWarga() === 0) return 0;
        return (int) round(($this->jumlahLunas() / $this->totalWarga()) * 100);
    }
}