<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengaman: akun tanpa data warga akan kehilangan nama-nya setelah kolom dihapus
        $tanpaWarga = DB::table('users')->whereNull('warga_id')->count();

        if ($tanpaWarga > 0) {
            throw new \RuntimeException(
                "Ada {$tanpaWarga} akun yang belum terhubung ke data warga (users.warga_id kosong). "
                . 'Hubungkan dulu sebelum menghapus kolom lama.'
            );
        }

        // Index unik dilepas lebih dulu, baru kolomnya dihapus
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nik']);
            $table->dropUnique(['no_hp']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_lengkap', 'nik', 'no_rt', 'no_hp']);
        });
    }

    public function down(): void
    {
        throw new \RuntimeException('Migration ini tidak dapat di-rollback. Pulihkan dari backup database.');
    }
};