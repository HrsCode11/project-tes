<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    // Nama tabel manual karena Laravel akan tebak 'pemasuans'
    protected $table = 'pemasukan';

    protected $fillable = [
        'dicatat_oleh',
        'keterangan',
        'kategori',
        'sumber',
        'jumlah',
        'tgl_masuk',
    ];

    protected $casts = [
        'jumlah'    => 'decimal:2',
        // tgl_masuk jadi Carbon object — bisa pakai ->format('d/m/Y')
        'tgl_masuk' => 'date',
    ];

    // Relasi: pemasukan ini dicatat oleh 1 user (bendahara)
    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}