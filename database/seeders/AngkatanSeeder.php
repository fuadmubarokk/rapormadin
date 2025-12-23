<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use Illuminate\Database\Seeder;

class AngkatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $angkatans = [
            ['nama_angkatan' => '2020/2021'],
            ['nama_angkatan' => '2021/2022'],
            ['nama_angkatan' => '2022/2023'],
            ['nama_angkatan' => '2023/2024'],
            ['nama_angkatan' => '2024/2025'],
        ];

        foreach ($angkatans as $angkatan) {
            Angkatan::create($angkatan);
        }
    }
}