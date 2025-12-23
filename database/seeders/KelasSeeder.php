<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $angkatan = Angkatan::first(); // Ambil angkatan pertama
        $waliKelas = User::role('wali')->get();

        $kelasData = [
            ['nama_kelas' => 'I A', 'tingkat' => 'Ula'],
            ['nama_kelas' => 'I B', 'tingkat' => 'Ula'],
            ['nama_kelas' => 'II A', 'tingkat' => 'Wustho'],
            ['nama_kelas' => 'II B', 'tingkat' => 'Wustho'],
            ['nama_kelas' => 'III A', 'tingkat' => 'Ulya'],
        ];

        foreach ($kelasData as $index => $data) {
            Kelas::create([
                'nama_kelas' => $data['nama_kelas'],
                'tingkat' => $data['tingkat'],
                'wali_id' => $waliKelas->random()->id ?? null,
                'angkatan_id' => $angkatan->id,
            ]);
        }
    }
}