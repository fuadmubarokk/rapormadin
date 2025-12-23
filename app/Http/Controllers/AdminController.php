<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Angkatan;
use App\Models\GuruMapelKelas;
use App\Models\TahunAjaran;
use App\Models\SettingSekolah;
use App\Models\Nilai;
use App\Models\Role;
use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest; 
use App\Imports\GuruImport;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $totalGuru = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['guru', 'wali']);
        })->count();
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalMapel = Mapel::count();
        
        $tahunAjaran = TahunAjaran::where('status', true)->first();
        $semester = $tahunAjaran ? $tahunAjaran->semester : 'Ganjil';

        $whereTahun = $tahunAjaran ? $tahunAjaran->nama_tahun : '';

        $grafikNilai = DB::table('nilai')
            ->select('mapel.nama_mapel', DB::raw('AVG(nilai.nilai_uas) as rata_rata'))
            ->join('guru_mapel_kelas', 'nilai.guru_mapel_kelas_id', '=', 'guru_mapel_kelas.id')
            ->join('mapel', 'guru_mapel_kelas.mapel_id', '=', 'mapel.id')
            ->where('nilai.semester', $semester)
            ->where('nilai.tahun_ajaran', $whereTahun)
            ->groupBy('mapel.nama_mapel')
            ->orderBy('mapel.nama_mapel')
            ->get();

        $chartLabels = $grafikNilai->pluck('nama_mapel');
        $chartData = $grafikNilai->pluck('rata_rata');
        
        $grafikSiswaPerKelas = DB::table('siswa')
            ->select('kelas.nama_kelas', DB::raw('COUNT(siswa.id) as jumlah_siswa'))
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->groupBy('kelas.nama_kelas')
            ->orderBy('kelas.nama_kelas')
            ->get();
            
        $kelasLabels = $grafikSiswaPerKelas->pluck('nama_kelas');
        $kelasData = $grafikSiswaPerKelas->pluck('jumlah_siswa');

        return view('admin.dashboard', compact(
            'totalGuru', 
            'totalSiswa', 
            'totalKelas', 
            'totalMapel',
            'chartLabels',
            'chartData',
            'kelasLabels',
            'kelasData'
        ));
    }

    public function guruIndex()
    {
        $guru = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['guru', 'wali']);
            })
            ->orderBy('name', 'asc')
            ->get();
    
        return view('admin.guru.index', compact('guru'));
    }


    public function guruCreate()
    {
        return view('admin.guru.create');
    }

    public function guruStore(StoreGuruRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
            
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_guru');
        }

        $user = User::create($data);
        $roleNames = $request->input('roles', []);
        $roleIds = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $user->roles()->attach($roleIds);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan');
    }

    public function guruEdit($id)
    {
        $guru = User::findOrFail($id);
        $guru->load('roles');
        return view('admin.guru.edit', compact('guru'));
    }

    public function guruUpdate(UpdateGuruRequest $request, $id)
    {
        $guru = User::findOrFail($id);
        $data = $request->all();
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_guru', $guru->foto);
        }
        
        if ($request->hasFile('ttd_wali_kelas')) {
            $data['ttd_wali_kelas'] = $this->handleTtdUpload($request->file('ttd_wali_kelas'), $guru->ttd_wali_kelas, $guru->id);
        }
    
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        
        unset($data['roles']);
        
        $guru->update($data);
        
        $roleNames = $request->input('roles', []);
        $roleIds = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $guru->roles()->sync($roleIds);
        
        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui');
    }

    public function guruDestroy($id)
    {
        if (DB::table('guru_mapel_kelas')->where('guru_id', $id)->exists()) {
            return redirect()->route('admin.guru.index')
                            ->with('error', 'Tidak dapat menghapus Guru ini karena masih memiliki penugasan mengajar.');
        }

        $guru = User::findOrFail($id);
        $guru->delete();

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data Guru berhasil dihapus.');
    }
    
    /**
     * Import data guru dari Excel
     */
    public function guruImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file'));
            
            return redirect()->route('admin.guru.index')
                ->with('success', 'Data guru berhasil diimpor');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            $errorMessages = '';
            foreach ($failures as $failure) {
                $errorMessages .= 'Baris ke-' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
            }

            return redirect()->route('admin.guru.index')
                ->with('error', 'Import gagal. Periksa kembali file Anda.<br>' . $errorMessages);
        } catch (\Exception $e) {
            return redirect()->route('admin.guru.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
    
    /**
     * Download template untuk import guru
     */
    public function guruTemplate()
    {
        $fileName = 'template_import_guru.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        
        return response()->download(public_path('templates/' . $fileName), $fileName, $headers);
    }

    public function kelasIndex()
    {
        $kelas = Kelas::with('waliKelas')->get();
        $wali = User::whereHas('roles', function($query) {
            $query->where('name', 'wali');
        })->get();
        return view('admin.kelas.index', compact('kelas', 'wali'));
    }

    public function kelasStore(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'wali_id' => 'nullable|exists:users,id',
            'tingkat' => 'required|string|max:20',
        ]);

        Kelas::create($request->all());

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil ditambahkan');
    }

    public function kelasUpdate(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'wali_id' => 'nullable|exists:users,id',
            'tingkat' => 'required|string|max:20',
        ]);

        $kelas->update($request->all());

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui');
    }

    public function kelasDestroy($id)
    {
        if (DB::table('guru_mapel_kelas')->where('kelas_id', $id)->exists()) {
            return redirect()->route('admin.kelas.index')
                            ->with('error', 'Tidak dapat menghapus kelas ini karena masih terdapat data Guru Mapel yang terkait.');
        }

        if (DB::table('siswa')->where('kelas_id', $id)->exists()) {
            return redirect()->route('admin.kelas.index')
                            ->with('error', 'Tidak dapat menghapus kelas ini karena masih terdapat Siswa di dalamnya.');
        }

        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Data kelas berhasil dihapus.');
    }

    public function siswaIndex(Request $request)
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

    public function siswaStore(StoreSiswaRequest $request)
    {
        $data = $request->all();
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_siswa');
        }
    
        Siswa::create($data);
    
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function siswaEdit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function siswaUpdate(UpdateSiswaRequest $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $data = $request->all();
            
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_siswa', $siswa->foto);
        }
            
        $siswa->update($data);
    
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui');
    }

    public function siswaDestroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        if ($siswa->foto) {
            File::delete(public_path('img/foto_siswa/' . $siswa->foto));
        }

        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus');
    }

    public function siswaDestroyAll(Request $request)
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
    public function siswaImport(Request $request)
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
    
    
    private function cleanImportError($message)
    {
        if (preg_match("/Column '(.*?)' cannot be null/", $message, $m)) {
            return "Kolom '" . str_replace('_', ' ', $m[1]) . "' tidak boleh kosong. Periksa file Excel Anda.";
        }
    
        if (preg_match("/Duplicate entry '(.*?)'/", $message, $m)) {
            return "Data duplikat ditemukan: " . $m[1];
        }
    
        return "Terjadi kesalahan saat mengimpor data. Pastikan format Excel sudah benar.";
    }


    /**
     * Download template untuk import siswa
     */
    public function siswaTemplate()
    {
        $path = public_path('template/template_siswa.xlsx');

        if (!file_exists($path)) {
            abort(404, 'File template tidak ditemukan.');
        }

        return response()->download($path, 'template_import_siswa.xlsx');
    }


    public function mapelIndex()
    {
        $mapel = Mapel::all();
        
        // TAMBAHKAN BARIS INI
        $angkatans = \App\Models\Angkatan::orderBy('nama_angkatan', 'asc')->get();
    
        // KIRIM VARIABEL $angkatans KE VIEW
        return view('admin.mapel.index', compact('mapel', 'angkatans')); 
    }

    public function mapelStore(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'nama_mapel_ar' => 'required|string|max:100',
            'kategori' => 'required|in:tulis,non-tulis',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        Mapel::create($request->all());

        return redirect()->route('admin.mapel.index')->with('success', 'Data mata pelajaran berhasil ditambahkan');
    }

    public function mapelUpdate(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'nama_mapel_ar' => 'required|string|max:100',
            'kategori' => 'required|in:tulis,non-tulis',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $mapel->update($request->all());

        return redirect()->route('admin.mapel.index')->with('success', 'Data mata pelajaran berhasil diperbarui');
    }

    public function mapelDestroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $mapel->delete();

        return redirect()->route('admin.mapel.index')->with('success', 'Data mata pelajaran berhasil dihapus');
    }
    
    /**
     * Menampilkan halaman pengaturan urutan mapel untuk angkatan tertentu.
     */
    public function showMapelUrutan($angkatanId)
    {
        $angkatan = Angkatan::findOrFail($angkatanId);
        
        $mapelDiAngkatan = $angkatan->mapel;
        
        $mapelIdDiAngkatan = $mapelDiAngkatan->pluck('id')->toArray();
        $mapelTersedia = Mapel::whereNotIn('id', $mapelIdDiAngkatan)->get();

        return view('admin.mapel.urutan', compact('angkatan', 'mapelDiAngkatan', 'mapelTersedia'));
    }

    /**
     * Memperbarui urutan mapel atau menambahkan mapel baru ke angkatan.
     */
    public function updateMapelUrutan(Request $request)
    {
        $angkatanId = $request->input('angkatan_id');
        $urutanData = $request->input('urutan', []);

        DB::transaction(function () use ($angkatanId, $urutanData) {
            foreach ($urutanData as $index => $mapelId) {
                DB::table('mapel_angkatan')
                    ->updateOrInsert(
                        ['mapel_id' => $mapelId, 'angkatan_id' => $angkatanId],
                        ['urutan' => $index + 1]
                    );
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Urutan mapel berhasil diperbarui!']);
    }

    /**
     * Tampilkan data penugasan guru mapel kelas
     */
    public function guruMapelKelasIndex(Request $request)
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
    public function guruMapelKelasStore(Request $request)
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
    public function guruMapelKelasEdit($id)
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
    public function guruMapelKelasUpdate(Request $request, $id)
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
    public function guruMapelKelasDestroy($id)
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
    public function guruMapelKelasDeleteAll()
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

    public function tahunAjaranIndex()
    {
        $setting = \App\Models\SettingSekolah::first();

        $tahunAjaranList = \App\Models\TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        foreach ($tahunAjaranList as $ta) {
            $ta->is_active = ($setting && $setting->tahun_ajaran_id == $ta->id);
        }

        return view('admin.tahun_ajaran.index', compact('tahunAjaranList'));
    }

    public function tahunAjaranStore(Request $request)
    {
        $request->validate([
            'nama_tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        TahunAjaran::where('status', true)->update(['status' => false]);

        $tahunAjaranBaru = TahunAjaran::create([
            'nama_tahun' => $request->nama_tahun,
            'semester' => $request->semester,
            'status' => true,
        ]);

        $setting = \App\Models\SettingSekolah::first();
        if ($setting) {
            $setting->update([
                'tahun_ajaran_id' => $tahunAjaranBaru->id,
            ]);
        } else {
            \App\Models\SettingSekolah::create([
                'tahun_ajaran_id' => $tahunAjaranBaru->id,
            ]);
        }

        return redirect()->route('admin.tahun_ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan dan diaktifkan.');
    }

    public function tahunAjaranAktifkan(Request $request, $id)
    {
        TahunAjaran::where('status', true)->update(['status' => false]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update([
            'status' => true,
            'semester' => $request->semester,
        ]);

        $setting = \App\Models\SettingSekolah::first();
        if ($setting) {
            $setting->update([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester' => $request->semester,
            ]);
        } else {
            \App\Models\SettingSekolah::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester' => $request->semester,
            ]);
        }

        return redirect()->route('admin.tahun_ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diaktifkan.');
    }

    public function tahunAjaranUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_tahun' => 'required|string|max:255',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update([
            'nama_tahun' => $request->nama_tahun,
            'semester' => $request->semester,
        ]);

        return redirect()->route('admin.tahun_ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function tahunAjaranDestroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        
        if ($tahunAjaran->status) {
            return redirect()->route('admin.tahun_ajaran.index')
                ->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun_ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus');
    }

    public function settingSekolahIndex()
    {
        $setting = SettingSekolah::first();
        return view('admin.setting.index', compact('setting'));
    }

    public function settingSekolahUpdate(Request $request)
    {
        $setting = SettingSekolah::first();
    
        $request->validate([
            'nama_madrasah' => 'required|string|max:255',
            'nama_madrasah_ar' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'sekretariat' => 'nullable|string|max:255',
            'tempat_ttd' => 'nullable|string|max:255',
            'tanggal_rapor' => 'nullable|date',
            'npsn' => 'string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_kepala_madrasah' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kepala_madrasah' => 'required|string|max:255',
        ]);

         $data = $request->all();
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->handleFileUpload($request->file('logo'), 'img/logo', $setting ? $setting->logo : null);
        }
    
        if ($request->hasFile('ttd_kepala_madrasah')) {
            if ($setting && $setting->ttd_kepala_madrasah) {
                File::delete(public_path('img/ttd/' . $setting->ttd_kepala_madrasah));
            }
            
            $ttd = $request->file('ttd_kepala_madrasah');
            $filename = 'ttd_kepala_madrasah_' . time() . '.' . $ttd->getClientOriginalExtension();
            
            $path = public_path('img/ttd');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            
            $ttd->move($path, $filename);
            $data['ttd_kepala_madrasah'] = $filename;
        }
    
        if ($setting) {
            $setting->update($data);
        } else {
            SettingSekolah::create($data);
        }
    
        return redirect()->route('admin.setting.index')
            ->with('success', 'Pengaturan sekolah berhasil diperbarui');
    }
    
    /**
     * Upload tanda tangan kepala madrasah.
     */
    public function uploadTtdKepalaMadrasah(Request $request)
    {
        $request->validate([
            'ttd_kepala_madrasah' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        try {
            $setting = SettingSekolah::first();
    
            if ($request->hasFile('ttd_kepala_madrasah')) {
                $ttd = $request->file('ttd_kepala_madrasah');
                $filename = 'ttd_kepala_madrasah_' . time() . '.' . $ttd->getClientOriginalExtension();
                
                $path = public_path('img/ttd');
                
                \Log::info('Path tujuan upload TTD: ' . $path);
                
                if (!File::exists($path)) {
                    \Log::info('Folder tidak ditemukan, mencoba membuat: ' . $path);
                    if (!File::makeDirectory($path, 0755, true)) {
                        \Log::error('GAGAL membuat folder: ' . $path);
                        throw new \Exception('Gagal membuat folder tujuan.');
                    }
                    \Log::info('Folder berhasil dibuat.');
                }
    
                if ($setting && $setting->ttd_kepala_madrasah) {
                    $oldFilePath = $path . '/' . $setting->ttd_kepala_madrasah;
                    \Log::info('Menghapus file lama: ' . $oldFilePath);
                    File::delete($oldFilePath);
                }
                
                \Log::info('Memindahkan file ke: ' . $path . '/' . $filename);
                if (!$ttd->move($path, $filename)) {
                    \Log::error('GAGAL memindahkan file.');
                    throw new \Exception('Gagal memindahkan file tanda tangan.');
                }
                
                \Log::info('File berhasil dipindahkan. Mengupdate database...');
                
                if ($setting) {
                    $setting->update(['ttd_kepala_madrasah' => $filename]);
                } else {
                    SettingSekolah::create(['ttd_kepala_madrasah' => $filename]);
                }
                
                \Log::info('Database berhasil diupdate.');
            }
    
            return redirect()->route('admin.setting.index')
                ->with('success', 'Tanda tangan kepala madrasah berhasil diupload.');
    
        } catch (\Exception $e) {
            \Log::error('Error saat upload TTD Kepala Madrasah: ' . $e->getMessage());
            return redirect()->route('admin.setting.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function uploadTtdWaliKelas(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ttd_wali_kelas' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($request->hasFile('ttd_wali_kelas')) {
            if ($user->ttd_wali_kelas) {
                File::delete(public_path('img/ttd/' . $user->ttd_wali_kelas));
            }
            
            $ttd = $request->file('ttd_wali_kelas');
            $filename = 'ttd_wali_kelas_' . $user->id . '_' . time() . '.' . $ttd->getClientOriginalExtension();
            
            $path = public_path('img/ttd');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            
            $ttd->move($path, $filename);
            
            $user->update(['ttd_wali_kelas' => $filename]);
        }

        return redirect()->route('admin.guru.index')
            ->with('success', 'Tanda tangan wali kelas berhasil diupload.');
    }
    
    /**
     * Backup database
     */
    public function backupDatabase()
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
            
            return redirect()->route('admin.setting.index')
                ->with('success', 'Database berhasil di-backup. File tersimpan di ' . $path . $filename);
        } catch (\Exception $e) {
            return redirect()->route('admin.setting.index')
                ->with('error', 'Gagal melakukan backup database: ' . $e->getMessage());
        }
    }
    
    /**
     * Download backup database
     */
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (!File::exists($path)) {
            return redirect()->route('admin.setting.index')
                ->with('error', 'File backup tidak ditemukan');
        }
        
        return response()->download($path);
    }
    
    /**
     * Hapus backup database
     */
    public function deleteBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (File::exists($path)) {
            File::delete($path);
            
            return redirect()->route('admin.setting.index')
                ->with('success', 'File backup berhasil dihapus');
        }
        
        return redirect()->route('admin.setting.index')
            ->with('error', 'File backup tidak ditemukan');
    }
    
    /**
     * Daftar backup database
     */
    public function listBackup()
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

    /**
     * Menangani upload file: hapus file lama, pindahkan file baru, dan kembalikan nama file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string|null $oldFilename
     * @return string|null Nama file baru atau null jika tidak ada file.
     */
    private function handleFileUpload($file, $directory, $oldFilename = null)
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
    private function handleTtdUpload($file, $oldFilename, $userId)
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