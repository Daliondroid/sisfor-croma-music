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
            $programs = [
                ['nama_program' => 'Piano', 'deskripsi' => 'Program les piano klasik, pop, dan jazz untuk semua usia', 'tipe_les' => 'keduanya', 'biaya_kursus' => 600000],
                ['nama_program' => 'Vokal', 'deskripsi' => 'Program vokal, teknik pernapasan, dan performance panggung', 'tipe_les' => 'keduanya', 'biaya_kursus' => 600000],
                ['nama_program' => 'Gitar', 'deskripsi' => 'Program les gitar akustik dan elektrik berbagai genre', 'tipe_les' => 'keduanya', 'biaya_kursus' => 600000],
                ['nama_program' => 'Keyboard', 'deskripsi' => 'Program synthesizer, arranger, dan keyboard modern', 'tipe_les' => 'keduanya', 'biaya_kursus' => 600000],
                ['nama_program' => 'Drum', 'deskripsi' => 'Program rhythm, perkusi, dan drum modern', 'tipe_les' => 'keduanya', 'biaya_kursus' => 650000],
                ['nama_program' => 'Bass', 'deskripsi' => 'Program groove, slap technique, dan bassline foundation', 'tipe_les' => 'keduanya', 'biaya_kursus' => 650000],
                ['nama_program' => 'Saxophone', 'deskripsi' => 'Program saxophone jazz, pop, dan teknik tiup brass', 'tipe_les' => 'keduanya', 'biaya_kursus' => 650000],
                ['nama_program' => 'Flute', 'deskripsi' => 'Program flute klasik, teknik tiup, dan repertoar orkestra', 'tipe_les' => 'keduanya', 'biaya_kursus' => 700000],
                ['nama_program' => 'Trumpet', 'deskripsi' => 'Program trumpet jazz, klasik, dan brass ensemble', 'tipe_les' => 'keduanya', 'biaya_kursus' => 700000],
                ['nama_program' => 'Instrumen Lainnya', 'deskripsi' => 'Request instrumen khusus: Biola, Cello, Ukulele, dll.', 'tipe_les' => 'keduanya', 'biaya_kursus' => 700000],
            ];

            foreach ($programs as $progData) {
                ProgramKursus::create(array_merge($progData, ['is_active' => true]));
            }

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
