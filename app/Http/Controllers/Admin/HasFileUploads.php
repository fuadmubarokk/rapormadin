<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

trait HasFileUploads
{
    /**
     * Menangani upload file: hapus file lama, pindahkan file baru, dan kembalikan nama file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string|null $oldFilename
     * @return string|null Nama file baru atau null jika tidak ada file.
     */
    protected function handleFileUpload($file, $directory, $oldFilename = null)
    {
        if (!$file) {
            return null;
        }
    
        if ($oldFilename) {
            File::delete(public_path($directory . '/' . $oldFilename));
        }
        
        $path = public_path($directory);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        $filename = $file->hashName();
        $file->move($path, $filename);
        
        return $filename;
    }

    /**
     * Menangani upload file TTD dengan nama file unik.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $oldFilename
     * @param int $userId
     * @return string|null Nama file baru atau null jika tidak ada file.
     */
    protected function handleTtdUpload($file, $oldFilename, $userId)
    {
        if (!$file) {
            return null;
        }
    
        if ($oldFilename) {
            File::delete(public_path('img/ttd/' . $oldFilename));
        }
        
        $path = public_path('img/ttd');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        $filename = 'ttd_wali_kelas_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $filename);
        
        return $filename;
    }
}