<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithFile;

class SiswaTemplateExport implements WithFile
{
    use Exportable;

    public function file()
    {
        // Lokasi file template yang kamu simpan
        return storage_path('template/siswa_template.xlsx');
    }
}
