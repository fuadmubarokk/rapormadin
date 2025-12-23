<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanRapor extends Model
{
    use HasFactory;
    protected $table = 'catatan_rapor';
    protected $guarded = [];

    /**
     * Relasi ke tabel siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}