<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MapelAngkatan extends Pivot
{
    protected $table = 'mapel_angkatan';
    protected $guarded = ['id'];
}