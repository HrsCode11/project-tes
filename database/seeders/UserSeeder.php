<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Haris Cahyana', 'nik' => '3502010101900001', 'hp' => '081200000001',
             'email' => 'ketua@rtrw.com',     'role' => 'ketua_rt'],
            ['nama' => 'Siti Aminah',   'nik' => '3502010201900002', 'hp' => '081200000002',
             'email' => 'bendahara@rtrw.com', 'role' => 'bendahara'],
            ['nama' => 'Budi Santoso',  'nik' => '3502010301900003', 'hp' => '081200000003',
             'email' => 'warga@rtrw.com',     'role' => 'warga'],
        ];

        foreach ($data as $d) {
            // Data penduduk dulu, lalu akun login yang terhubung ke data itu
            $warga = Warga::updateOrCreate(
                ['nik' => $d['nik']],
                [
                    'nama_lengkap' => $d['nama'],
                    'no_hp'        => $d['hp'],
                    'no_rt'        => '04',
                    'status'       => 'aktif',
                ]
            );

            // Password sederhana khusus untuk data uji, jangan dipakai di produksi
            User::updateOrCreate(
                ['email' => $d['email']],
                [
                    'warga_id' => $warga->id,
                    'password' => Hash::make('password123'),
                    'role'     => $d['role'],
                    'status'   => 'aktif',
                ]
            );
        }
    }
}