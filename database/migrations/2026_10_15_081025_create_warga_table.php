<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap', 100);
            // Tidak unik: satu nomor HP bisa dipakai beberapa anggota keluarga
            $table->string('no_hp', 16)->nullable();
            $table->string('no_rt', 10);
            $table->string('alamat', 255)->nullable();
            $table->enum('status', ['aktif', 'pindah', 'meninggal'])->default('aktif');
            $table->timestamps();
        });

        // Satu warga maksimal punya satu akun; akun boleh tidak terkait warga
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('warga_id')
                ->nullable()
                ->unique()
                ->after('id')
                ->constrained('warga')
                ->nullOnDelete();
        });

        // Salin data user yang sudah ada menjadi data warga
        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            $wargaId = DB::table('warga')->insertGetId([
                'nik'          => $user->nik,
                'nama_lengkap' => $user->nama_lengkap,
                'no_hp'        => $user->no_hp,
                'no_rt'        => $user->no_rt,
                // User nonaktif dianggap sudah tidak aktif sebagai warga
                'status'       => $user->status === 'aktif' ? 'aktif' : 'pindah',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            DB::table('users')->where('id', $user->id)->update(['warga_id' => $wargaId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warga_id');
        });

        Schema::dropIfExists('warga');
    }
};
