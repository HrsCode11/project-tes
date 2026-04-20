<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ketua RT
        User::create([
            'nama_lengkap' => 'Haris Cahyana',
            'nik'          => '3502010101900001',
            'email'        => 'ketua@rtrw.com',
            'password'     => Hash::make('password123'),
            'no_rt'        => '04',
            'role'         => 'ketua_rt',
            'status'       => 'aktif',
        ]);

        // Bendahara
        User::create([
            'nama_lengkap' => 'Siti Aminah',
            'nik'          => '3502010201900002',
            'email'        => 'bendahara@rtrw.com',
            'password'     => Hash::make('password123'),
            'no_rt'        => '04',
            'role'         => 'bendahara',
            'status'       => 'aktif',
        ]);

    }
}