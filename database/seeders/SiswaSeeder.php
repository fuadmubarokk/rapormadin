<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua kelas yang ada
        $kelas = Kelas::all();

        // Buat 10 siswa untuk setiap kelas
        $kelas->each(function ($k) {
            Siswa::factory()->count(10)->create(['kelas_id' => $k->id]);
        });
    }
}