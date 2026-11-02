<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'iuran_id',
        'warga_id',
        'nominal',
        'status',
        'pengingat_dikirim_at',
    ];

    protected $casts = [
        'nominal'              => 'decimal:2',
        'pengingat_dikirim_at' => 'datetime',
    ];

    public function iuran()
    {
        return $this->belongsTo(Iuran::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    // Satu tagihan punya satu pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}