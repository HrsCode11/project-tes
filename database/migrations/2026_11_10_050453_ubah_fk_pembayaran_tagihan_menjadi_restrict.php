<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['tagihan_id']);

            // Sebelumnya CASCADE. Tagihan yang punya catatan pembayaran
            // tidak lagi bisa terhapus, termasuk lewat penghapusan iuran
            $table->foreign('tagihan_id')
                ->references('id')->on('tagihan')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['tagihan_id']);

            $table->foreign('tagihan_id')
                ->references('id')->on('tagihan')
                ->cascadeOnDelete();
        });
    }
};