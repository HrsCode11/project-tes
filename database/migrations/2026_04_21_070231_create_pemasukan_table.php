<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id();

            // Siapa bendahara yang input pemasukan ini
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnDelete();

            $table->string('keterangan', 255);

            // Kategori sumber pemasukan
            $table->enum('kategori', ['donasi', 'sewa', 'subsidi', 'lainnya']);

            // Dari siapa pemasukan ini (opsional)
            $table->string('sumber', 100)->nullable();

            $table->decimal('jumlah', 12, 2);

            // Tanggal pemasukan diterima
            $table->date('tgl_masuk');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};