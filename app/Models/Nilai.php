<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;
    protected $table = 'nilai';
    protected $guarded = [];

    /**
     * Relasi ke tabel siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke tabel guru_mapel_kelas
     */
    public function guruMapelKelas()
    {
        return $this->belongsTo(GuruMapelKelas::class);
    }

    /**
     * Mendapatkan nilai huruf berdasarkan nilai angka
     */
    public function getNilaiHurufAttribute()
    {
        $nilai = $this->nilai_uas;
        
        if ($nilai >= 90) {
            return 'A';
        } elseif ($nilai >= 80) {
            return 'B';
        } elseif ($nilai >= 70) {
            return 'C';
        } elseif ($nilai >= 60) {
            return 'D';
        } else {
            return 'E';
        }
    }

    /**
     * Mendapatkan terbilang dari nilai angka
     */
    public function getTerbilangAttribute()
    {
        return $this->terbilang($this->nilai_uas);
    }

    /**
     * Fungsi untuk mengubah angka menjadi terbilang
     */
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = array('', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');
        $terbilang = '';

        if ($angka < 12) {
            $terbilang = ' ' . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10) . ' puluh' . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = ' seratus' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100) . ' ratus' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = ' seribu' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000) . ' ribu' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000) . ' juta' . $this->terbilang($angka % 1000000);
        }

        return $terbilang;
    }
}