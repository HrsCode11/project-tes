<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // tabel => kolom yang menunjuk ke users
    private array $relasi = [
        'iuran'       => 'dibuat_oleh',
        'pemasukan'   => 'dicatat_oleh',
        'pengeluaran' => 'dicatat_oleh',
    ];

    public function up(): void
    {
        foreach ($this->relasi as $tabel => $kolom) {
            Schema::table($tabel, function (Blueprint $t) use ($kolom) {
                $t->dropForeign([$kolom]);
                // User yang pernah mencatat data tidak bisa terhapus
                $t->foreign($kolom)->references('id')->on('users')->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->relasi as $tabel => $kolom) {
            Schema::table($tabel, function (Blueprint $t) use ($kolom) {
                $t->dropForeign([$kolom]);
                $t->foreign($kolom)->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};