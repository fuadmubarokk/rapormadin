<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'angkatan';

    public function mapel()
    {
        return $this->belongsToMany(Mapel::class, 'mapel_angkatan')
                    ->withPivot('urutan')
                    ->orderBy('mapel_angkatan.urutan', 'asc');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}