<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswa';
    protected $guarded = [];
    protected $casts = [
        'tanggal_lahir' => 'date',
        'diterima_tanggal' => 'date',
    ];

    /**
     * Relasi ke tabel kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi ke tabel nilai
     */
    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Relasi ke tabel nilai_non_tulis
     */
    public function nilaiNonTulis()
    {
        return $this->hasMany(NilaiNonTulis::class);
    }

    /**
     * Relasi ke tabel penilaian_karakter
     */
    public function penilaianKarakter()
    {
        return $this->hasMany(PenilaianKarakter::class);
    }

    /**
     * Relasi ke tabel absensi
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Relasi ke tabel catatan_rapor
     */
    public function catatanRapor()
    {
        return $this->hasMany(CatatanRapor::class);
    }

    public function getJenisKelaminTextAttribute()
    {
        return match ($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }
}