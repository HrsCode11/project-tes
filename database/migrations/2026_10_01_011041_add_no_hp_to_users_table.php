<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Format tersimpan: 628xxxxxxxxxx (tanpa +, tanpa 0 di depan)
            // nullable agar user lama tidak error, bisa dilengkapi belakangan
            $table->string('no_hp', 16)->nullable()->unique()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['no_hp']);
            $table->dropColumn('no_hp');
        });
    }
};
