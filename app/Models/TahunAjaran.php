<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;
    protected $table = 'tahun_ajaran';

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $fillable = [
        'nama_tahun',
        'semester',
        'status'
    ];
}