<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianKarakter extends Model
{
    use HasFactory;
    protected $table = 'penilaian_karakter';
    protected $guarded = [];

    /**
     * Relasi ke tabel siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}