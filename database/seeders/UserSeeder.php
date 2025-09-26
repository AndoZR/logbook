<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 Super Admin
        User::create([
            'username' => 'superadmin',
            'role' => '1',
            'password' => Hash::make('password123'), // ganti sesuai kebutuhan
        ]);

        // 3 Petugas Perpustakaan
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'username' => 'petugas' . $i,
                'role' => '2',
                'password' => Hash::make('password123'),
            ]);
        }

        // 50 Siswa
        $tingkatList = ['SD', 'SMP', 'SMA']; // opsi tingkat

        for ($i = 1; $i <= 50; $i++) {
            $user = User::create([
                'username' => 'siswa' . $i,
                'role' => '3',
                'password' => Hash::make('password123'),
            ]);
            
            // Buat data siswa dengan relasi user_id
            Siswa::create([
                'nama' => 'Siswa ke-' . $i,
                'tingkat' => $tingkatList[array_rand($tingkatList)], // acak SD/SMP/SMA
                'user_id' => $user->id,
            ]);
        }
    }
}
