<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiNonTulis extends Model
{
    use HasFactory;
    protected $table = 'nilai_non_tulis';
    protected $guarded = [];

    /**
     * Relasi ke tabel siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}