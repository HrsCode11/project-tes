<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek di awal, sebelum ada perubahan struktur apa pun:
        // semua user yang punya tagihan/pembayaran harus sudah terhubung ke warga
        $adaYangBelumTerhubung = DB::table('users')
            ->whereNull('warga_id')
            ->where(function ($q) {
                $q->whereIn('id', DB::table('tagihan')->select('user_id'))
                  ->orWhereIn('id', DB::table('pembayaran')->select('user_id'));
            })->exists();

        if ($adaYangBelumTerhubung) {
            throw new \RuntimeException(
                'Ada user yang punya tagihan/pembayaran tapi belum terhubung ke warga (users.warga_id kosong).'
            );
        }

        foreach (['tagihan', 'pembayaran'] as $tabel) {
            // 1. Kolom baru, sementara boleh kosong
            Schema::table($tabel, function (Blueprint $t) {
                $t->unsignedBigInteger('warga_id')->nullable()->after('id');
            });

            // 2. Isi dari relasi user -> warga
            DB::statement("
                UPDATE {$tabel} t
                JOIN users u ON u.id = t.user_id
                SET t.warga_id = u.warga_id
            ");

            // 3. Setelah terisi semua, jadikan wajib diisi
            Schema::table($tabel, function (Blueprint $t) {
                $t->unsignedBigInteger('warga_id')->change();
            });

            // 4. Pasang relasi ke warga, lalu buang kolom lama.
            //    restrictOnDelete: warga yang punya riwayat tidak bisa terhapus
            Schema::table($tabel, function (Blueprint $t) {
                $t->foreign('warga_id')->references('id')->on('warga')->restrictOnDelete();
                $t->dropForeign(['user_id']);
                $t->dropColumn('user_id');
            });
        }
    }

    public function down(): void
    {
        throw new \RuntimeException('Migration ini tidak dapat di-rollback. Pulihkan dari backup database.');
    }
};