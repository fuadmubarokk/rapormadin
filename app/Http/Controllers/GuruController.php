<?php

namespace App\Http\Controllers;

use App\Models\GuruMapelKelas;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Exports\NilaiExport;
use App\Imports\NilaiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GuruController extends Controller
{
    public function __construct()
    {
        $this->middleware('guru');
    }

    public function dashboard()
    {
        $guru = auth()->user();
        $guruMapelKelas = $guru->guruMapelKelas()->with('mapel', 'kelas')->get();
        $tahunAjaran = TahunAjaran::where('status', true)->first();

        return view('guru.dashboard', compact('guruMapelKelas', 'tahunAjaran'));
    }

    public function nilaiIndex($guruMapelKelasId)
    {
        $guruMapelKelas = GuruMapelKelas::with('mapel', 'kelas')->findOrFail($guruMapelKelasId);
            
        // Cek akses guru
        if ($guruMapelKelas->guru_id != auth()->id()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke mapel dan kelas ini');
        }
    
        $tahunAjaran = TahunAjaran::where('status', true)->first();
        $semester = 'Ganjil';
    
        // ✅ URUT BERDASARKAN NAMA SISWA
        $siswa = Siswa::where('kelas_id', $guruMapelKelas->kelas_id)
            ->orderBy('nama', 'asc')   // ganti sesuai nama kolom
            ->get();
            
        $nilai = Nilai::where('guru_mapel_kelas_id', $guruMapelKelasId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran->nama_tahun)
            ->get()
            ->keyBy('siswa_id');
    
        return view('guru.nilai.index', compact(
            'guruMapelKelas', 'siswa', 'nilai', 'tahunAjaran', 'semester'
        ));
    }


    /**
     * Menyimpan atau memperbarui nilai untuk satu siswa (via AJAX).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function nilaiStore(Request $request)
    {
        try {
            $request->validate([
                'siswa_id' => 'required|exists:siswa,id',
                'guru_mapel_kelas_id' => 'required|exists:guru_mapel_kelas,id',
                'semester' => 'required|string',
                'tahun_ajaran' => 'required|string',
                'nilai_uas' => 'required|integer|min:0|max:100',
                'predikat' => 'nullable|string|in:A,B,C,D,E',
                'deskripsi' => 'nullable|string|max:255',
            ]);

            // Cek apakah guru memiliki akses ke mapel dan kelas ini
            $guruMapelKelas = GuruMapelKelas::findOrFail($request->guru_mapel_kelas_id);
            if ($guruMapelKelas->guru_id != auth()->id()) {
                $message = 'Akses ditolak.';
                return $request->ajax() 
                    ? response()->json(['success' => false, 'message' => $message], 403)
                    : redirect()->route('guru.dashboard')->with('error', $message);
            }

            $nilai = Nilai::updateOrCreate(
                [
                    'siswa_id' => $request->siswa_id,
                    'guru_mapel_kelas_id' => $request->guru_mapel_kelas_id,
                    'semester' => $request->semester,
                    'tahun_ajaran' => $request->tahun_ajaran,
                ],
                $request->only('nilai_uas', 'predikat', 'deskripsi')
            );

            $message = $nilai->wasRecentlyCreated ? 'Nilai berhasil ditambahkan.' : 'Nilai berhasil diperbarui.';
            
            // KUNCI: Cek apakah request ini AJAX
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->route('guru.nilai.index', $request->guru_mapel_kelas_id)
                ->with('success', $message);

        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Validasi gagal. ' . implode(', ', $e->errors()->all())
                ], 422);
            }
            
            return redirect()->route('guru.nilai.index', $request->guru_mapel_kelas_id)
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            $message = 'Terjadi kesalahan. Silakan coba lagi.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            
            return redirect()->route('guru.nilai.index', $request->guru_mapel_kelas_id)
                ->with('error', $message);
        }
    }

    /**
     * Menyimpan nilai untuk semua siswa sekaligus (via AJAX).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function nilaiStoreAll(Request $request)
    {
        try {
            $request->validate([
                'guru_mapel_kelas_id' => 'required|exists:guru_mapel_kelas,id',
                'semester' => 'required|string',
                'tahun_ajaran' => 'required|string',
            ]);

            $gmkId = $request->guru_mapel_kelas_id;
            $semester = $request->semester;
            $tahunAjaran = $request->tahun_ajaran;

            // Pastikan guru yang sedang login memiliki akses ke guru_mapel_kelas ini
            $guruMapelKelas = GuruMapelKelas::findOrFail($gmkId);
            if ($guruMapelKelas->guru_id != auth()->id()) {
                $message = 'Akses ditolak.';
                return $request->ajax() 
                    ? response()->json(['success' => false, 'message' => $message], 403)
                    : redirect()->back()->with('error', $message);
            }

            $siswaList = Siswa::where('kelas_id', $guruMapelKelas->kelas_id)->get();
            $savedCount = 0;

            foreach ($siswaList as $siswa) {
                $nilaiUas = $request->input("nilai_uas.{$siswa->id}");

                // Hanya simpan jika ada data yang diisi
                if ($nilaiUas !== '' && $nilaiUas !== null) {
                    Nilai::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'guru_mapel_kelas_id' => $gmkId,
                            'semester' => $semester,
                            'tahun_ajaran' => $tahunAjaran,
                        ],
                        [
                            'nilai_uas' => $nilaiUas,
                            // Predikat dan deskripsi bisa di-generate otomatis di sini jika perlu
                        ]
                    );
                    $savedCount++;
                }
            }

            $message = "Berhasil menyimpan {$savedCount} data nilai.";
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->route('guru.nilai.index', $gmkId)->with('success', $message);

        } catch (\Exception $e) {
            $message = 'Terjadi kesalahan saat menyimpan semua data. Silakan coba lagi.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            
            return redirect()->route('guru.nilai.index', $request->guru_mapel_kelas_id)
                ->with('error', $message);
        }
    }
    
    /**
     * Menghapus data nilai untuk satu siswa.
     *
     * @param Nilai $nilai
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function nilaiDestroy(Nilai $nilai, Request $request)
    {
        try {
            // Keamanan: Pastikan nilai yang akan dihapus milik guru yang login
            if ($nilai->guruMapelKelas->guru_id != auth()->id()) {
                $message = 'Anda tidak memiliki akses untuk menghapus data ini.';
                return $request->ajax() 
                    ? response()->json(['success' => false, 'message' => $message], 403)
                    : redirect()->back()->with('error', $message);
            }
    
            $nilai->delete();
            $message = 'Data nilai berhasil dihapus.';
    
            return $request->ajax() 
                ? response()->json(['success' => true, 'message' => $message])
                : redirect()->back()->with('success', $message);
    
        } catch (\Exception $e) {
            $message = 'Gagal menghapus data. Silakan coba lagi.';
            return $request->ajax() 
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->back()->with('error', $message);
        }
    }

    /**
     * Memperbarui nilai (dari modal lama, dipertahankan untuk kompatibilitas).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nilaiUpdate(Request $request, $id)
    {
        $request->validate([
            'nilai_uas' => 'required|integer|min:0|max:100',
            'predikat' => 'nullable|string|in:A,B,C,D,E',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $nilai = Nilai::findOrFail($id);
        
        // Cek apakah guru memiliki akses ke nilai ini
        if ($nilai->guruMapelKelas->guru_id != auth()->id()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke nilai ini');
        }

        $nilai->update($request->all());

        return redirect()->route('guru.nilai.index', $nilai->guru_mapel_kelas_id)
            ->with('success', 'Nilai berhasil diperbarui');
    }

    /**
     * Export nilai ke Excel
     */
    public function nilaiExport($guruMapelKelasId)
    {
        $guruMapelKelas = GuruMapelKelas::with('mapel', 'kelas')->findOrFail($guruMapelKelasId);
        
        if ($guruMapelKelas->guru_id != auth()->id()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke mapel dan kelas ini');
        }

        $tahunAjaran = TahunAjaran::where('status', true)->first();
        $semester = 'Ganjil'; // Bisa dinamis sesuai kebutuhan
        
        $fileName = 'nilai_' . str_replace(' ', '_', $guruMapelKelas->mapel->nama_mapel) . '_' . 
                   str_replace(' ', '_', $guruMapelKelas->kelas->nama_kelas) . '.xlsx';
        
        return Excel::download(new NilaiExport($guruMapelKelasId, $semester, $tahunAjaran->nama_tahun), $fileName);
    }

    /**
     * Import nilai dari Excel
     */
    public function nilaiImport(Request $request)
    {
        $request->validate([
            'guru_mapel_kelas_id' => 'required|exists:guru_mapel_kelas,id',
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $guruMapelKelas = GuruMapelKelas::findOrFail($request->guru_mapel_kelas_id);
        if ($guruMapelKelas->guru_id != auth()->id()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke mapel dan kelas ini');
        }

        try {
            Excel::import(new NilaiImport(
                $request->guru_mapel_kelas_id,
                $request->semester,
                $request->tahun_ajaran
            ), $request->file('file'));
            
            return redirect()->route('guru.nilai.index', $request->guru_mapel_kelas_id)
                ->with('success', 'Nilai berhasil diimpor');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            $errorMessages = '';
            foreach ($failures as $failure) {
                $errorMessages .= 'Baris ke-' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
            }

            return redirect()->route('guru.nilai.index', $request->guru_mapel_kelas_id)
                ->with('error', 'Import gagal. Periksa kembali file Anda.<br>' . $errorMessages);
        } catch (\Exception $e) {
            return redirect()->route('guru.nilai.index', $request->guru_mapel_kelas_id)
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
    
    /**
     * Download template untuk import nilai
     */
    public function nilaiTemplate($guruMapelKelasId)
    {
        $guruMapelKelas = GuruMapelKelas::with('mapel', 'kelas')->findOrFail($guruMapelKelasId);
        
        if ($guruMapelKelas->guru_id != auth()->id()) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke mapel dan kelas ini');
        }

        $tahunAjaran = TahunAjaran::where('status', true)->first();
        $semester = 'Ganjil'; // Bisa dinamis sesuai kebutuhan
        
        $fileName = 'template_nilai_' . str_replace(' ', '_', $guruMapelKelas->mapel->nama_mapel) . '_' . 
                   str_replace(' ', '_', $guruMapelKelas->kelas->nama_kelas) . '.xlsx';
        
        return Excel::download(new NilaiExport($guruMapelKelasId, $semester, $tahunAjaran->nama_tahun, true), $fileName);
    }
}