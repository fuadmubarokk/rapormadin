<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SiswaController extends Controller
{
    use HasFileUploads;
    
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Siswa::with('kelas');
        
        // Pencarian
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                ->orWhere('nisn', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        
        // Debug: Log parameter sorting
        \Log::info('Sorting parameters:', [
            'sort' => $request->get('sort'),
            'dir' => $request->get('dir')
        ]);
        
        // Sorting
        $sortColumn = $request->get('sort', 'id');
        $sortDirection = $request->get('dir', 'asc');
        
        // Mapping kolom dari frontend ke database
        $sortMapping = [
            'nisn' => 'nisn',
            'nama' => 'nama', 
            'kelas' => 'kelas_id',
            'kelas_id' => 'kelas_id'
        ];
        
        // Gunakan mapping atau default ke id
        $dbSortColumn = $sortMapping[$sortColumn] ?? 'id';
        
        // Validasi direction
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) 
            ? strtolower($sortDirection) 
            : 'asc';
        
        // Apply sorting
        if ($dbSortColumn === 'kelas_id') {
            // Sorting by kelas nama
            $query->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->orderBy('kelas.nama_kelas', $sortDirection)
                ->select('siswa.*');
        } else {
            $query->orderBy($dbSortColumn, $sortDirection);
        }
        
        // Jumlah data per halaman
        $perPage = $request->get('per_page', 20);
        $siswa = $query->paginate($perPage)->withQueryString();
        $kelas = Kelas::all();
        
        // Jika request AJAX, kembalikan JSON
        if ($request->ajax() || $request->wantsJson()) {
            $tableHtml = view('admin.siswa._table_partial', compact('siswa'))->render();
            
            return response()->json([
                'table' => $tableHtml,
                'sort' => $sortColumn,
                'dir' => $sortDirection
            ]);
        }
        
        return view('admin.siswa.index', compact('siswa', 'kelas'));
    }

    public function store(StoreSiswaRequest $request)
    {
        $data = $request->all();
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_siswa');
        }
    
        Siswa::create($data);
    
        // Jika request AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil ditambahkan'
            ]);
        }
    
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $siswa = Siswa::with('kelas')->findOrFail($id);
        
        // Jika request AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'siswa' => $siswa
            ]);
        }
        
        // Fallback untuk non-AJAX request
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(UpdateSiswaRequest $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $data = $request->all();
            
        if ($request->hasFile('foto')) {
            // Hapus file lama jika ada file baru
            $oldFoto = $siswa->foto;
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_siswa', $oldFoto);
        }
            
        $siswa->update($data);
    
        // Jika request AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diperbarui'
            ]);
        }
    
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        if ($siswa->foto) {
            File::delete(public_path('img/foto_siswa/' . $siswa->foto));
        }

        $siswa->delete();

        // Jika request AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil dihapus'
            ]);
        }

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus');
    }

    public function destroyAll(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|in:HAPUS SEMUA SISWA'
        ]);
    
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
        try {
            Siswa::truncate();
            
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    
        $path = public_path('img/foto_siswa/'); 
        
        if (File::exists($path)) {
            File::deleteDirectory($path);
            File::makeDirectory($path, 0777, true);
        }
    
        return redirect()->route('admin.siswa.index')
                        ->with('success', 'Semua data siswa berhasil dihapus secara permanen.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);
    
        $import = new SiswaImport();
        
        try {
            Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            // Jika request AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat membaca file: ' . $e->getMessage()
                ], 400);
            }
            return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    
        $allErrors = $import->customErrors;
    
        if (!empty($allErrors)) {
            // Jika request AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan validasi',
                    'errors' => $allErrors
                ], 422);
            }
            return back()->with('import_errors', $allErrors);
        }
    
        // Jika request AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Import data siswa berhasil!'
            ]);
        }
    
        return back()->with('success', 'Import data siswa berhasil!');
    }
    
    public function template()
    {
        $path = public_path('template/template_siswa.xlsx');

        if (!file_exists($path)) {
            abort(404, 'File template tidak ditemukan.');
        }

        return response()->download($path, 'template_import_siswa.xlsx');
    }
}