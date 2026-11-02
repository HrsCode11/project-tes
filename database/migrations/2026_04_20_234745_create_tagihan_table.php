<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            // FK ke iuran — tagihan ini milik iuran yang mana
            $table->foreignId('iuran_id')->constrained('iuran')->cascadeOnDelete();
            // FK ke users — tagihan ini untuk warga siapa
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('nominal', 12, 2);
            // 3 status: belum_bayar, menunggu=sudah bayar digital tapi belum konfirmasi, lunas
            $table->enum('status', ['belum_bayar', 'menunggu', 'lunas'])->default('belum_bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};