<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Madin Al Amin Cintamulya',
            'email' => 'admin@madrasah.com',
            'password' => Hash::make('password'), // Ganti dengan password yang aman
            'nip' => '1234567890',
            'no_hp' => '081234567890',
        ]);
        $admin->roles()->attach(Role::where('name', 'admin')->first());

        // Buat beberapa guru dan wali kelas
        User::factory()->count(5)->guru()->create()->each(function ($user) {
            $user->roles()->attach(Role::where('name', 'guru')->first());
        });

        User::factory()->count(3)->wali()->create()->each(function ($user) {
            $user->roles()->attach(Role::where('name', 'wali')->first());
        });
    }
}