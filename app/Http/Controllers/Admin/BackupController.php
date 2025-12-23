<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Backup database
     */
    public function create()
    {
        try {
            $filename = "backup_" . date('Y-m-d_H-i-s') . ".sql";
            $path = storage_path('app/backups/');
            
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg(env('DB_USERNAME')),
                escapeshellarg(env('DB_PASSWORD')),
                escapeshellarg(env('DB_HOST')),
                escapeshellarg(env('DB_DATABASE')),
                escapeshellarg($path . $filename)
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Gagal membuat backup database');
            }
            
            return redirect()->route('admin.backup.index')
                ->with('success', 'Database berhasil di-backup. File tersimpan di ' . $path . $filename);
        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Gagal melakukan backup database: ' . $e->getMessage());
        }
    }
    
    /**
     * Download backup database
     */
    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (!File::exists($path)) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'File backup tidak ditemukan');
        }
        
        return response()->download($path);
    }
    
    /**
     * Hapus backup database
     */
    public function delete($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (File::exists($path)) {
            File::delete($path);
            
            return redirect()->route('admin.backup.index')
                ->with('success', 'File backup berhasil dihapus');
        }
        
        return redirect()->route('admin.backup.index')
            ->with('error', 'File backup tidak ditemukan');
    }
    
    /**
     * Daftar backup database
     */
    public function index()
    {
        $path = storage_path('app/backups/');
        $files = [];
        
        if (File::exists($path)) {
            $files = File::allFiles($path);
            usort($files, function ($a, $b) {
                return $b->getMTime() - $a->getMTime();
            });
        }
        
        return view('admin.backup.index', compact('files'));
    }
}