<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruMapelKelas extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Relasi ke tabel users (guru)
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Relasi ke tabel mapel
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

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
}