<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjarans = [
            ['nama_tahun' => '2023/2024', 'semester' => 'Ganjil', 'status' => 0],
            ['nama_tahun' => '2023/2024', 'semester' => 'Genap', 'status' => 0],
            ['nama_tahun' => '2024/2025', 'semester' => 'Ganjil', 'status' => 1], // Aktif
            ['nama_tahun' => '2024/2025', 'semester' => 'Genap', 'status' => 0],
        ];

        foreach ($tahunAjarans as $tahunAjaran) {
            TahunAjaran::create($tahunAjaran);
        }
    }
}