<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            $userId = DB::table('users')->insertGetId([
                'username'          => 'Admin',
                'name'              => 'Croma Music',
                'email'             => 'admin@croma.id',
                'email_verified_at' => now(),
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'is_active'         => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ], 'id_user');

            DB::table('admins')->insert([
                'id_user'    => $userId,
                'nama_admin' => 'Croma Music',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        });
    }
}
