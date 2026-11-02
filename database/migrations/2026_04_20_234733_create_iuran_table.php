<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iuran', function (Blueprint $table) {
            $table->id();
            // FK ke users — siapa bendahara yang buat iuran ini
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnDelete();
            $table->string('nama_iuran', 100);
            // DECIMAL(12,2) artinya max 12 digit, 2 desimal. cth: 9999999999.99
            $table->decimal('nominal', 12, 2);
            $table->text('keterangan')->nullable();
            $table->date('deadline');
            // 3 status: aktif=masih bisa bayar, selesai=semua lunas, ditutup=paksa tutup
            $table->enum('status', ['aktif', 'selesai', 'ditutup'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran');
    }
};