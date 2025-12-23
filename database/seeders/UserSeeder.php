<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Buat user admin
        $user = User::create([
            'name' => 'Admin Madrasah',
            'email' => 'admin@madrasah.com',
            'password' => Hash::make('password'),
            'nip' => '1234567890',
            'no_hp' => '081234567890',
        ]);

        // 2️⃣ Ambil id role 'admin' dari tabel roles
        $adminRole = DB::table('roles')->where('name', 'admin')->first();

        // 3️⃣ Tambahkan ke tabel user_roles
        if ($adminRole) {
            DB::table('user_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $adminRole->id,
            ]);
        }
    }
}
