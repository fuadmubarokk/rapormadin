<?php

namespace Database\Seeders;

use App\Models\GuruMapelKelas;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Database\Seeder;

class GuruMapelKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gurus = User::whereHas('roles', function ($q) {
            $q->where('name', 'guru');
        })->get();
        $kelas = Kelas::all();
        $mapels = Mapel::all();

        // Pastikan ada data sebelum membuat relasi
        if ($gurus->isNotEmpty() && $kelas->isNotEmpty() && $mapels->isNotEmpty()) {
            foreach ($kelas as $k) {
                foreach ($mapels as $m) {
                    GuruMapelKelas::create([
                        'guru_id' => $gurus->random()->id,
                        'mapel_id' => $m->id,
                        'kelas_id' => $k->id,
                    ]);
                }
            }
        }
    }
}