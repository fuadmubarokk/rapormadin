<?php

namespace Database\Seeders;

use App\Models\Mapel;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapels = [
            ['nama_mapel' => 'Nahwu', 'nama_mapel_ar' => 'النحو', 'kategori' => 'tulis', 'kkm' => 51],
            ['nama_mapel' => 'Fiqh', 'nama_mapel_ar' => 'الفقه', 'kategori' => 'tulis', 'kkm' => 51],
            ['nama_mapel' => 'Shorof', 'nama_mapel_ar' => 'الصرف', 'kategori' => 'tulis', 'kkm' => 51],
            ['nama_mapel' => 'Qowaidul Ilal', 'nama_mapel_ar' => 'قواعد الإعلال', 'kategori' => 'tulis', 'kkm' => 51],
            ['nama_mapel' => 'Muhafadzhoh', 'nama_mapel_ar' => 'الحفظ', 'kategori' => 'non-tulis', 'kkm' => 75],
            ['nama_mapel' => 'Qiroatul Kutub', 'nama_mapel_ar' => 'قراءة الكتب', 'kategori' => 'non-tulis', 'kkm' => 75],
            ['nama_mapel' => 'Taftisyul Kutub', 'nama_mapel_ar' => 'تفشيش الكتب', 'kategori' => 'non-tulis', 'kkm' => 75],
        ];

        foreach ($mapels as $mapel) {
            Mapel::create($mapel);
        }
    }
}