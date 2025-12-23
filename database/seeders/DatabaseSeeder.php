<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            AngkatanSeeder::class,
            TahunAjaranSeeder::class,
            MapelSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            GuruMapelKelasSeeder::class,
            SettingSekolahSeeder::class,
        ]);
    }
}