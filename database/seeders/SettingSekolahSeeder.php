<?php

namespace Database\Seeders;

use App\Models\SettingSekolah;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class SettingSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingSekolah::create([
            'nama_madrasah' => 'MADRASAH DINIYAH AL-AMIN CINTAMULYA',
            'nama_madrasah_ar' => 'المدرسة الدينية الأمين جينتا موليا',
            'desa' => 'Cintamulya',
            'kecamatan' => 'Candipuro',
            'kabupaten' => 'Lampung Selatan',
            'sekretariat' => 'Jl. KH. Hasyim Asyari No. 09, Desa Cintamulya',
            'tempat_ttd' => 'Cintamulya',
            'tanggal_rapor' => '2025-12-19',
            'npsn' => '123456789',
            'kepala_madrasah' => 'NUR HAMID',
            'tahun_ajaran_id' => TahunAjaran::where('status', 1)->first()->id,
            'semester' => 'Ganjil',
        ]);
    }
}