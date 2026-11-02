<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class WargaSeeder extends Seeder
{
    public function run(): void
    {
        $warga = [
            ['Slamet Riyadi',        '3520061203750001', 'slamet.riyadi@example.com',    '081234560001', '04', 'aktif'],
            ['Sri Wahyuni',          '3520066107800002', 'sri.wahyuni@example.com',      '081234560002', '04', 'aktif'],
            ['Agus Prasetyo',        '3520060511880003', 'agus.prasetyo@example.com',    '081234560003', '04', 'aktif'],
            ['Dewi Lestari',         '3520065402920004', 'dewi.lestari@example.com',     '081234560004', '04', 'aktif'],
            ['Hendra Wijaya',        '3520063009700005', 'hendra.wijaya@example.com',    '081234560005', '03', 'aktif'],
            ['Siti Nurhaliza',       '3520064805850006', 'siti.nurhaliza@example.com',   '081234560006', '03', 'aktif'],
            ['Wahyu Setiawan',       '3520061708900007', 'wahyu.setiawan@example.com',   '081234560007', '04', 'aktif'],
            ['Endang Susilowati',    '3520066512680008', 'endang.susilowati@example.com','081234560008', '03', 'aktif'],
            ['Rizky Pratama',        '3520060304990009', 'rizky.pratama@example.com',    null,           '04', 'aktif'],
            ['Mulyono Hadi Santoso', '3520061906600010', 'mulyono.hadi@example.com',     '081234560010', '04', 'nonaktif'],
        ];

        foreach ($warga as [$nama, $nik, $email, $noHp, $noRt, $status]) {
            User::updateOrCreate(
                ['nik' => $nik],
                [
                    'nama_lengkap' => $nama,
                    'email'        => $email,
                    'no_hp'        => $noHp,
                    'no_rt'        => $noRt,
                    'password'     => 'password123', 
                    'role'         => 'warga',
                    'status'       => $status,
                ]
            );
        }
    }
}