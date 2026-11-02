<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';

    protected $fillable = [
        'dicatat_oleh',
        'keterangan',
        'kategori',
        'penerima',
        'jumlah',
        'tgl_keluar',
        'bukti_path',
    ];

    protected $casts = [
        'jumlah'     => 'decimal:2',
        'tgl_keluar' => 'date',
    ];

    // Relasi: pengeluaran ini dicatat oleh 1 user (bendahara)
    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}