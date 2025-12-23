<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Relasi ke tabel siswa
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    /**
     * Relasi ke tabel users sebagai wali kelas
     */
    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_id');
    }

    /**
     * Relasi ke tabel guru_mapel_kelas
     */
    public function guruMapelKelas()
    {
        return $this->hasMany(GuruMapelKelas::class);
    }
    
    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class);
    }
}