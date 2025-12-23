<?php

if (!function_exists('konversiNilai')) {
    /**
     * Konversi nilai angka ke huruf.
     */
    function konversiNilai($nilaiAngka)
    {
        if (!$nilaiAngka || $nilaiAngka < 0) return '-';
        if ($nilaiAngka >= 90) return 'A';
        if ($nilaiAngka >= 80) return 'B';
        if ($nilaiAngka >= 70) return 'C';
        if ($nilaiAngka >= 60) return 'D';
        return 'E';
    }
}

if (!function_exists('terbilang')) {
    /**
     * Ubah angka menjadi huruf (contoh: 70 -> Tujuh Puluh)
     */
    function terbilang($angka)
    {
        $angka = abs($angka);
        $huruf = [
            "", "Satu", "Dua", "Tiga", "Empat", "Lima",
            "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"
        ];
        $temp = "";

        if ($angka < 12) {
            $temp = " " . $huruf[$angka];
        } else if ($angka < 20) {
            $temp = terbilang($angka - 10) . " Belas";
        } else if ($angka < 100) {
            $temp = terbilang($angka / 10) . " Puluh " . terbilang($angka % 10);
        } else if ($angka < 200) {
            $temp = " Seratus" . terbilang($angka - 100);
        } else if ($angka < 1000) {
            $temp = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
        } else if ($angka < 2000) {
            $temp = " Seribu" . terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $temp = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $temp = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
        }

        return trim(preg_replace('/\s+/', ' ', $temp));
    }
}

if (!function_exists('terbilangArab')) {
    /**
     * Konversi angka ke huruf Arab.
     *
     * @param int $angka
     * @return string
     */
    function terbilangArab($angka)
    {
        $daftar = [
            1 => 'واحد',
            2 => 'اثناني',
            3 => 'ثلاثة',
            4 => 'أربعة',
            5 => 'خمسة',
            6 => 'ستّة',
            7 => 'سبعة',
            8 => 'ثمانية',
            9 => 'تسعة',
            10 => 'عشرة',
            11 => 'أحد عشر',
            12 => 'إثنا عشر',
            13 => 'ثلاثة عشر',
            14 => 'أربعة عشر',
            15 => 'خمسة عشر',
            16 => 'ستّة عشر',
            17 => 'سبعة عشر',
            18 => 'ثمانية عشر',
            19 => 'تسعة عشر',
            20 => 'عشرون',
            30 => 'ثلاثون',
            40 => 'أربعون',
            50 => 'خمسون',
            60 => 'ستّون',
            70 => 'سبعون',
            80 => 'ثمانون',
            90 => 'تسعون',
            100 => 'مائة',
        ];

        if ($angka <= 0) return '-';
        if (isset($daftar[$angka])) return $daftar[$angka];

        if ($angka < 100) {
            $puluhan = floor($angka / 10) * 10;
            $satuan = $angka % 10;

            return $daftar[$satuan] . ' و ' . $daftar[$puluhan];
        }

        if ($angka == 100) return $daftar[100];
        if ($angka < 200) return 'مائة و ' . terbilangArab($angka - 100);
        if ($angka < 1000) {
            $ratusan = floor($angka / 100);
            $sisa = $angka % 100;
            return $daftar[$ratusan] . ' مائة' . ($sisa ? ' و ' . terbilangArab($sisa) : '');
        }

        return (string)$angka;
    }

    if (!function_exists('angkaArab')) {
        function angkaArab($number)
        {
            $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
            return str_replace(range(0,9), $arabic, strval($number));
        }
    }
    
    if (!function_exists('konversiMuhafadzhoh')) {
        /**
         * Konversi nilai muhafadzhoh dari teks Arab ke deskripsi Romanisasi
         *
         * @param string $nilaiArab
         * @return string
         */
        function konversiMuhafadzhoh($nilaiArab)
        {
            $konversi = [
                'ممتاز' => 'Mumtaz',
                'جيد'   => 'Jayyid',
                'متوسط' => 'Mutawwasith',
                'رادئ'   => 'Rodi\'',
            ];
            
            return $konversi[$nilaiArab] ?? $nilaiArab;
        }
    }
    
    if (!function_exists('konversiMuhafadzhohKeAngka')) {
        /**
         * Konversi nilai muhafadzhoh dari teks Arab ke angka
         *
         * @param string $nilaiArab
         * @return int
         */
        function konversiMuhafadzhohKeAngka($nilaiArab)
        {
            $konversi = [
                'ممتاز' => 4,
                'جيد' => 3,
                'متوسط' => 2,
                'رادئ' => 1,
            ];
            
            return $konversi[$nilaiArab] ?? 0;
        }
    }

    if (!function_exists('getGreenShadeColor')) {
        /**
         * Menghitung shade warna hijau berdasarkan persentase.
         * 0% -> Hijau Muda, 100% -> Hijau Tua.
         *
         * @param int|float $percentage Nilai persentase (0-100).
         * @return string Warna dalam format RGB.
         */
        function getGreenShadeColor($percentage)
        {
            $percentage = max(0, min(100, (float)$percentage));

            // Komponen RGB untuk shade hijau
            // Red dan Blue dijaga konstan rendah untuk menjaga nuansa hijau
            $red = 50;
            $blue = 50;

            // Green bervariasi dari 150 (hijau muda) ke 255 (hijau terang)
            $green = round(150 + (105 * $percentage / 100));

            return "rgb({$red}, {$green}, {$blue})";
        }
    }
}