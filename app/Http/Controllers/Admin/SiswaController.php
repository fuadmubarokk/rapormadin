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
    
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nisn', 'like', '%' . $searchTerm . '%');
        }
    
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
    
        $siswa = $query->paginate(20);
        $kelas = Kelas::all();
    
        if ($request->ajax()) {
            $tableHtml = view('admin.siswa._table_partial', compact('siswa'))->render();
            $paginationHtml = $siswa->links('pagination::bootstrap-4')->toHtml();
            return response()->json(['table' => $tableHtml, 'pagination' => $paginationHtml]);
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
    
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(UpdateSiswaRequest $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $data = $request->all();
            
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_siswa', $siswa->foto);
        }
            
        $siswa->update($data);
    
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        if ($siswa->foto) {
            File::delete(public_path('img/foto_siswa/' . $siswa->foto));
        }

        $siswa->delete();

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

    /**
     * Import data siswa dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);
    
        $import = new SiswaImport();
        
        try {
            Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    
        $allErrors = $import->customErrors;
    
        if (!empty($allErrors)) {
            return back()->with('import_errors', $allErrors);
        }
    
        return back()->with('success', 'Import data siswa berhasil!');
    }
    
    /**
     * Download template untuk import siswa
     */
    public function template()
    {
        $path = public_path('template/template_siswa.xlsx');

        if (!file_exists($path)) {
            abort(404, 'File template tidak ditemukan.');
        }

        return response()->download($path, 'template_import_siswa.xlsx');
    }
}