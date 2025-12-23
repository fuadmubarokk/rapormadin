<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruMapelKelas;
use App\Models\User;
use App\Models\Mapel;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruMapelKelasController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Tampilkan data penugasan guru mapel kelas
     */
    public function index(Request $request)
    {
        $query = GuruMapelKelas::with('guru', 'mapel', 'kelas');
        
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('guru', function($subQ) use ($searchTerm) {
                    $subQ->where('name', 'like', $searchTerm);
                })
                ->orWhereHas('mapel', function($subQ) use ($searchTerm) {
                    $subQ->where('nama_mapel', 'like', $searchTerm);
                })
                ->orWhereHas('kelas', function($subQ) use ($searchTerm) {
                    $subQ->where('nama_kelas', 'like', $searchTerm);
                });
            });
        }
        
        $perPage = $request->get('showEntries', 10);
        if ($perPage == -1) {
            $perPage = $query->count();
        }
    
        $guruMapelKelas = $query->paginate($perPage);
        
        $guru = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['guru', 'wali']);
        })->get();
        
        $mapel = Mapel::where('kategori', 'tulis')->get();        
        $kelas = Kelas::all();
        
        return view('admin.guru_mapel_kelas.index', compact('guruMapelKelas', 'guru', 'mapel', 'kelas'));
    }

    /**
     * Simpan data penugasan guru mapel kelas
     */
    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);
    
        $exists = GuruMapelKelas::where('guru_id', $request->guru_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('kelas_id', $request->kelas_id)
            ->exists();
    
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi guru, mapel, dan kelas sudah ada'
            ]);
        }
    
        $guruMapelKelas = GuruMapelKelas::create($request->all());
    
        return response()->json([
            'success' => true,
            'message' => 'Data guru mapel kelas berhasil ditambahkan',
            'data' => $guruMapelKelas
        ]);
    }

    /**
     * Tampilkan form edit untuk penugasan guru (untuk AJAX)
     */
    public function edit($id)
    {
        try {
            $gmk = GuruMapelKelas::findOrFail($id);
            $guru = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['guru', 'wali']);
            })->get();
            
            $mapel = Mapel::where('kategori', 'tulis')->get();        
            $kelas = Kelas::all();
            
            return response()->json([
                'success' => true,
                'form' => view('admin.guru_mapel_kelas._edit_form', compact('gmk', 'guru', 'mapel', 'kelas'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update penugasan Guru Mapel Kelas
     */
    public function update(Request $request, $id)
    {
        $gmk = GuruMapelKelas::findOrFail($id);
    
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);
    
        $exists = GuruMapelKelas::where('guru_id', $request->guru_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('id', '!=', $id)
            ->exists();
    
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi guru, mapel, dan kelas sudah ada untuk penugasan lain.'
            ]);
        }
    
        $gmk->update($request->all());
    
        return response()->json([
            'success' => true,
            'message' => 'Penugasan berhasil diperbarui.',
            'data' => $gmk
        ]);
    }

    /**
     * Hapus data penugasan guru mapel kelas
     */
    public function destroy($id)
    {
        if (DB::table('nilai')->where('guru_mapel_kelas_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus penugasan ini karena masih terdapat data Nilai yang terkait.'
            ]);
        }
    
        $guruMapelKelas = GuruMapelKelas::findOrFail($id);
        $guruMapelKelas->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Data Guru Mapel Kelas berhasil dihapus.'
        ]);
    }
    
    /**
     * Hapus semua data penugasan guru
     */
    public function deleteAll()
    {
        if (DB::table('nilai')->whereIn('guru_mapel_kelas_id', 
            function($query) {
                $query->select('id')->from('guru_mapel_kelas');
            }
        )->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus semua data karena masih terdapat data Nilai yang terkait.'
            ]);
        }
    
        GuruMapelKelas::truncate();
    
        return response()->json([
            'success' => true,
            'message' => 'Semua data penugasan guru berhasil dihapus.'
        ]);
    }
    
    /**
     * Export data guru mapel kelas
     */
    public function export()
    {
        // Implementasi export sesuai kebutuhan
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }
}