<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    // Nama tabel manual karena Laravel akan tebak 'pembayarans'
    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'warga_id',
        'order_id',
        'metode',
        'nominal',
        'status',
        'midtrans_response',
        'tgl_bayar',
        'catatan',
    ];

    protected $casts = [
        // JSON otomatis di-decode jadi array saat diakses
        'midtrans_response' => 'array',
        'tgl_bayar'         => 'datetime',
        'nominal'           => 'decimal:2',
    ];

    // Relasi: pembayaran ini milik 1 tagihan
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    // Relasi: pembayaran ini dilakukan oleh 1 user
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}