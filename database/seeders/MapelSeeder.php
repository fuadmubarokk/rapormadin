<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapel = [
            ['nama_mapel' => 'Nahwu', 'nama_mapel_ar' => 'القرآن والحديث', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Fiqh', 'nama_mapel_ar' => 'عقيدة أخلاق', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Shorof', 'nama_mapel_ar' => 'فقه', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Qowaidul Ilal', 'nama_mapel_ar' => 'تاريخ الحضارة الإسلامية', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Akhlaq', 'nama_mapel_ar' => 'اللغة العربية', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Tauhid', 'nama_mapel_ar' => 'اللغة العربية', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Tajwid', 'nama_mapel_ar' => 'اللغة العربية', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Imla', 'nama_mapel_ar' => 'اللغة العربية', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Fiqh Wanita', 'nama_mapel_ar' => 'اللغة العربية', 'kategori' => 'tulis', 'kkm' => 75],
            ['nama_mapel' => 'Muhafadzhoh', 'nama_mapel_ar' => 'محفوظات', 'kategori' => 'non-tulis', 'kkm' => 75],
            ['nama_mapel' => 'Qiroatul Kutub', 'nama_mapel_ar' => 'قراءة الكتب', 'kategori' => 'non-tulis', 'kkm' => 75],
            ['nama_mapel' => 'Taftisyul Kutub', 'nama_mapel_ar' => 'تفقيش الكتب', 'kategori' => 'non-tulis', 'kkm' => 75],
        ];

        foreach ($mapel as $m) {
            Mapel::create($m);
        }
    }
}