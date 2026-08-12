<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\GuruSpesialisasi;
use App\Models\Murid;
use App\Models\ProgramKursus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Create Default Program Kursus
            $piano = ProgramKursus::create([
                'nama_program' => 'Piano Classic',
                'deskripsi' => 'Program les piano klasik untuk anak dan remaja',
                'biaya_kursus' => 600000,
                'is_active' => true,
            ]);

            $biola = ProgramKursus::create([
                'nama_program' => 'Biola Starter',
                'deskripsi' => 'Program les biola dasar',
                'biaya_kursus' => 650000,
                'is_active' => true,
            ]);

            $vokal = ProgramKursus::create([
                'nama_program' => 'Vokal Pop',
                'deskripsi' => 'Program vokal dan teknik pernapasan',
                'biaya_kursus' => 550000,
                'is_active' => true,
            ]);

            // 2. Create Admin Account
            $userAdmin = User::create([
                'username' => 'admin',
                'name' => 'Croma Administrator',
                'email' => 'admin@croma.id',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]);

            $admin = Admin::create([
                'id_user' => $userAdmin->id_user,
                'nama_admin' => 'Croma Administrator',
            ]);

            // 3. Create Guru Account
            $userGuru = User::create([
                'username' => 'guru',
                'name' => 'Budi Harmono, S.Sn.',
                'email' => 'guru@croma.id',
                'email_verified_at' => now(),
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'is_active' => true,
            ]);

            $guru = Guru::create([
                'id_user' => $userGuru->id_user,
                'nama_guru' => 'Budi Harmono, S.Sn.',
                'nomor_hp' => '081234567890',
                'status_aktif' => true,
            ]);

            GuruSpesialisasi::create([
                'id_guru' => $guru->id_guru,
                'nama_spesialisasi' => 'Piano Classic',
            ]);

            GuruSpesialisasi::create([
                'id_guru' => $guru->id_guru,
                'nama_spesialisasi' => 'Biola',
            ]);

            // 4. Create Murid Account
            $userMurid = User::create([
                'username' => 'murid',
                'name' => 'Anisa Rahmawati',
                'email' => 'murid@croma.id',
                'email_verified_at' => now(),
                'password' => Hash::make('murid123'),
                'role' => 'murid',
                'is_active' => true,
            ]);

            $murid = Murid::create([
                'id_user' => $userMurid->id_user,
                'nama_murid' => 'Anisa Rahmawati',
                'tanggal_lahir' => '2012-05-15',
                'alamat' => 'Jl. Musik Harmoni No. 12, Bandung',
                'nomor_hp' => '089876543210',
                'nama_orang_tua' => 'Siti Aminah',
                'status_aktif' => true,
            ]);
        });
    }
}
