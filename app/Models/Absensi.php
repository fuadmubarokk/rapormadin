<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    
    // BAGIAN INI YANG PENTING:
    protected $table = 'absensi';
    
    protected $guarded = [];

    /**
     * Relasi ke tabel siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}