<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            // FK ke tagihan — pembayaran ini untuk tagihan yang mana
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();

            // FK ke users — siapa yang bayar
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // order_id untuk Midtrans — harus unik per transaksi
            // nullable karena pembayaran tunai tidak punya order_id
            $table->string('order_id', 50)->unique()->nullable();

            // Metode pembayaran: qris, va (virtual account), tunai
            $table->enum('metode', ['qris', 'va', 'tunai']);

            $table->decimal('nominal', 12, 2);

            // Status: pending=menunggu konfirmasi, lunas=berhasil, gagal=transaksi gagal
            $table->enum('status', ['pending', 'lunas', 'gagal'])->default('pending');

            // Simpan response JSON dari Midtrans untuk keperluan audit
            // nullable karena pembayaran tunai tidak ada response Midtrans
            $table->json('midtrans_response')->nullable();

            // Tanggal & jam pembayaran dikonfirmasi
            $table->timestamp('tgl_bayar')->nullable();

            // Catatan tambahan dari bendahara (opsional)
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};