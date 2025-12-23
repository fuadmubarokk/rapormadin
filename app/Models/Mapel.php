<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;
    protected $table = 'mapel';
    protected $guarded = [];

    /**
     * Relasi ke tabel guru_mapel_kelas
     */
    public function guruMapelKelas()
    {
        return $this->hasMany(GuruMapelKelas::class);
    }
    
    public function angkatans() // <-- Diubah menjadi jamak
    {
        return $this->belongsToMany(Angkatan::class, 'mapel_angkatan')
                    ->using(MapelAngkatan::class)
                    ->withPivot('urutan');
    }
}