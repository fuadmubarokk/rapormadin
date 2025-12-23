<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SettingSekolah;

class SettingSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingSekolah::create([
            'nama_madrasah' => 'Madrasah Diniyah Al Amin Cintamulya',
            'nama_madrasah_ar' => 'المدرسة الدينية الأمين جينتا موليا',
            'alamat' => 'Jl. KH. Hasyim Asyari No. 09, Desa Cintamulya',
            'npsn' => '12345678',
            'kepala_madrasah' => 'Nur Hamid',
            'nip_kepala' => '-',
        ]);
    }
}