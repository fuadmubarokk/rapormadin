<?php

namespace App\Http\Controllers;

use App\Models\NilaiNonTulis;
use App\Models\PenilaianKarakter;
use App\Models\Absensi;
use App\Models\CatatanRapor;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WaliController extends Controller
{
    /**
     * Constructor untuk menerapkan middleware 'wali'.
     */
    public function __construct()
    {
        $this->middleware('wali');
    }

    /**
     * Mengambil data dasar wali kelas (kelas, tahun ajaran, semester).
     * Method ini digunakan untuk menghindari duplikasi kode.
     *
     * @param Request $request
     * @return array|null
     */
    private function getWaliData(Request $request)
    {
        $wali = auth()->user();
        $kelas = $wali->kelasWali;
        $tahunAjaran = TahunAjaran::where('status', true)->first();

        if (!$kelas) {
            return null;
        }

        // Prioritaskan semester dari request, lalu session, jika tidak ada default ke 'Ganjil'
        $semester = $request->get('semester', session('semester', 'Ganjil'));
        session(['semester' => $semester]); // Simpan semester ke session

        return compact('wali', 'kelas', 'tahunAjaran', 'semester');
    }

    /**
     * Menampilkan halaman dashboard wali kelas.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dashboard(Request $request)
    {
        $data = $this->getWaliData($request);

        if (!$data) {
            return view('wali.dashboard', ['kelas' => null, 'tahunAjaran' => null]);
        }

        $siswaCount = Siswa::where('kelas_id', $data['kelas']->id)->count();

        return view('wali.dashboard', array_merge($data, ['siswa' => $siswaCount]));
    }

    /**
     * Menampilkan halaman input nilai non tulis.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function nilaiNonTulisIndex(Request $request)
    {
        $data = $this->getWaliData($request);
    
        if (!$data) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }
    
        $siswa = Siswa::where('kelas_id', $data['kelas']->id)
            ->orderBy('nama', 'asc')
            ->get();
    
        $nilaiNonTulis = NilaiNonTulis::where('semester', $data['semester'])
            ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->keyBy('siswa_id');
    
        foreach ($nilaiNonTulis as $nilai) {
            if ($nilai->muhafadzhoh) {
                $nilai->deskripsi = konversiMuhafadzhoh($nilai->muhafadzhoh);
                $nilai->angka = konversiMuhafadzhohKeAngka($nilai->muhafadzhoh);
            }
        }
    
        return view('wali.nilai_non_tulis.index', array_merge(
            $data,
            compact('siswa', 'nilaiNonTulis')
        ));
    }


    /**
     * Menyimpan atau memperbarui nilai non tulis untuk satu siswa.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function nilaiNonTulisStore(Request $request)
    {
        try {
            $request->validate([
                'siswa_id' => 'required|exists:siswa,id',
                'muhafadzhoh' => 'nullable|in:ممتاز,جيد,متوسط,رادئ',
                'qiroatul_kutub' => 'nullable|integer|min:0|max:100',
                'taftisyul_kutub' => 'nullable|integer|min:0|max:100',
            ]);

            if (!$request->filled('muhafadzhoh') && !$request->filled('qiroatul_kutub') && !$request->filled('taftisyul_kutub')) {
                throw ValidationException::withMessages([
                    'nilai' => 'Setidaknya satu nilai harus diisi untuk disimpan.',
                ]);
            }

            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }

            // PERBAIKAN: Cek apakah tahun ajaran ada
            if (!$data['tahunAjaran']) {
                $message = 'Tahun ajaran aktif tidak ditemukan. Silakan hubungi administrator.';
                return $this->handleResponse($request, false, $message, 'dashboard', 400);
            }

            $siswa = Siswa::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $data['kelas']->id) {
                $message = 'Anda tidak memiliki akses ke siswa ini.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }

            $nilaiData = [];
            if ($request->filled('muhafadzhoh')) {
                $nilaiData['muhafadzhoh'] = $request->muhafadzhoh;
            }
            if ($request->filled('qiroatul_kutub')) {
                $nilaiData['qiroatul_kutub'] = $request->qiroatul_kutub;
            }
            if ($request->filled('taftisyul_kutub')) {
                $nilaiData['taftisyul_kutub'] = $request->taftisyul_kutub;
            }

            $nilaiNonTulis = NilaiNonTulis::where('siswa_id', $request->siswa_id)
                ->where('semester', $data['semester'])
                ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
                ->first();

            if ($nilaiNonTulis) {
                $nilaiNonTulis->update($nilaiData);
                $message = 'Nilai Non Tulis berhasil diperbarui.';
            } else {
                NilaiNonTulis::create(array_merge([
                    'siswa_id' => $request->siswa_id,
                    'semester' => $data['semester'],
                    'tahun_ajaran' => $data['tahunAjaran']->nama_tahun,
                ], $nilaiData));
                $message = 'Nilai Non Tulis berhasil ditambahkan.';
            }

            return $this->handleResponse($request, true, $message, 'nilai_non_tulis.index');

        } catch (ValidationException $e) {
            return $this->handleValidationException($request, $e, 'nilai_non_tulis.index');
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'nilai_non_tulis.index', $e);
        }
    }

    /**
     * Menyimpan nilai non tulis untuk semua siswa sekaligus.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function nilaiNonTulisStoreAll(Request $request)
    {
        try {
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }

            // PERBAIKAN: Cek apakah tahun ajaran ada
            if (!$data['tahunAjaran']) {
                $message = 'Tahun ajaran aktif tidak ditemukan. Silakan hubungi administrator.';
                return $this->handleResponse($request, false, $message, 'dashboard', 400);
            }

            $siswaList = Siswa::where('kelas_id', $data['kelas']->id)->get();
            $savedCount = 0;

            foreach ($siswaList as $siswa) {
                $nilaiData = [];
                if ($request->filled("muhafadzhoh.{$siswa->id}")) {
                    $nilaiData['muhafadzhoh'] = $request->input("muhafadzhoh.{$siswa->id}");
                }
                if ($request->filled("qiroatul_kutub.{$siswa->id}")) {
                    $nilaiData['qiroatul_kutub'] = $request->input("qiroatul_kutub.{$siswa->id}");
                }
                if ($request->filled("taftisyul_kutub.{$siswa->id}")) {
                    $nilaiData['taftisyul_kutub'] = $request->input("taftisyul_kutub.{$siswa->id}");
                }

                if (!empty($nilaiData)) {
                    $existingNilai = NilaiNonTulis::where('siswa_id', $siswa->id)
                        ->where('semester', $data['semester'])
                        ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
                        ->first();

                    if ($existingNilai) {
                        $existingNilai->update($nilaiData);
                    } else {
                        NilaiNonTulis::create(array_merge([
                            'siswa_id' => $siswa->id,
                            'semester' => $data['semester'],
                            'tahun_ajaran' => $data['tahunAjaran']->nama_tahun,
                        ], $nilaiData));
                    }
                    $savedCount++;
                }
            }

            $message = "Berhasil menyimpan {$savedCount} data nilai non tulis.";
            return $this->handleResponse($request, true, $message, 'nilai_non_tulis.index');

        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'nilai_non_tulis.index', $e);
        }
    }

    /**
     * Menghapus data nilai non tulis untuk satu siswa.
     *
     * @param NilaiNonTulis $nilaiNonTulis
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function nilaiNonTulisDestroy(NilaiNonTulis $nilaiNonTulis, Request $request)
    {
        try {
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // Pastikan nilai yang akan dihapus milik siswa di kelas wali yang bersangkutan
            if ($nilaiNonTulis->siswa->kelas_id != $data['kelas']->id) {
                $message = 'Anda tidak memiliki akses untuk menghapus data ini.';
                return $this->handleResponse($request, false, $message, 'nilai_non_tulis.index', 403);
            }
    
            $nilaiNonTulis->delete();
            $message = 'Data nilai non tulis berhasil dihapus.';
    
            return $this->handleResponse($request, true, $message, 'nilai_non_tulis.index');
    
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'nilai_non_tulis.index', $e);
        }
    }

    /**
     * Menampilkan halaman input penilaian karakter.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function karakterIndex(Request $request)
    {
        $data = $this->getWaliData($request);

        if (!$data) {
            return redirect()->route('wali.dashboard')->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }
        
        $siswa = Siswa::where('kelas_id', $data['kelas']->id)
            ->orderBy('nama', 'asc')
            ->get();

        $penilaianKarakter = PenilaianKarakter::where('semester', $data['semester'])
            ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        return view('wali.karakter.index', array_merge($data, compact('siswa', 'penilaianKarakter')));
    }

    /**
     * Menyimpan atau memperbarui penilaian karakter untuk satu siswa.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function karakterStore(Request $request)
    {
        try {
            // PERUBIKAN 1: Validasi field menjadi nullable (boleh kosong)
            $request->validate([
                'siswa_id' => 'required|exists:siswa,id',
                'kelakuan' => 'nullable|in:A,B,C,D,E',
                'kerajinan' => 'nullable|in:A,B,C,D,E',
                'kerapihan' => 'nullable|in:A,B,C,D,E',
            ]);
    
            // PERUBIKAN 2: Validasi kustom, pastikan setidaknya satu field diisi
            if (!$request->filled('kelakuan') && !$request->filled('kerajinan') && !$request->filled('kerapihan')) {
                throw ValidationException::withMessages([
                    'nilai' => 'Setidaknya satu nilai harus diisi untuk disimpan.',
                ]);
            }
    
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // PERBAIKAN: Cek apakah tahun ajaran ada
            if (!$data['tahunAjaran']) {
                $message = 'Tahun ajaran aktif tidak ditemukan. Silakan hubungi administrator.';
                return $this->handleResponse($request, false, $message, 'dashboard', 400);
            }
    
            $siswa = Siswa::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $data['kelas']->id) {
                $message = 'Anda tidak memiliki akses ke siswa ini.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // PERUBIKAN 3: Siapkan data hanya untuk field yang diisi
            $karakterData = [];
            if ($request->filled('kelakuan')) {
                $karakterData['kelakuan'] = $request->kelakuan;
            }
            if ($request->filled('kerajinan')) {
                $karakterData['kerajinan'] = $request->kerajinan;
            }
            if ($request->filled('kerapihan')) {
                $karakterData['kerapihan'] = $request->kerapihan;
            }
    
            // PERUBIKAN 4: Cek keberadaan data lalu update atau create
            $penilaianKarakter = PenilaianKarakter::where('siswa_id', $request->siswa_id)
                ->where('semester', $data['semester'])
                ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
                ->first();
    
            if ($penilaianKarakter) {
                // Jika data sudah ada, update hanya field yang dikirim
                $penilaianKarakter->update($karakterData);
                $message = 'Penilaian Karakter berhasil diperbarui.';
            } else {
                // Jika data belum ada, buat baru
                PenilaianKarakter::create(array_merge([
                    'siswa_id' => $request->siswa_id,
                    'semester' => $data['semester'],
                    'tahun_ajaran' => $data['tahunAjaran']->nama_tahun,
                ], $karakterData));
                $message = 'Penilaian Karakter berhasil ditambahkan.';
            }
    
            return $this->handleResponse($request, true, $message, 'karakter.index');
    
        } catch (ValidationException $e) {
            return $this->handleValidationException($request, $e, 'karakter.index');
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'karakter.index', $e);
        }
    }

    /**
     * Menyimpan penilaian karakter untuk semua siswa sekaligus.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function karakterStoreAll(Request $request)
    {
        try {
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // PERBAIKAN: Cek apakah tahun ajaran ada
            if (!$data['tahunAjaran']) {
                $message = 'Tahun ajaran aktif tidak ditemukan. Silakan hubungi administrator.';
                return $this->handleResponse($request, false, $message, 'dashboard', 400);
            }
    
            $siswaList = Siswa::where('kelas_id', $data['kelas']->id)->get();
            $savedCount = 0;
    
            foreach ($siswaList as $siswa) {
                // PERUBIKAN 1: Siapkan data hanya untuk field yang diisi per siswa
                $karakterData = [];
                if ($request->filled("kelakuan.{$siswa->id}")) {
                    $karakterData['kelakuan'] = $request->input("kelakuan.{$siswa->id}");
                }
                if ($request->filled("kerajinan.{$siswa->id}")) {
                    $karakterData['kerajinan'] = $request->input("kerajinan.{$siswa->id}");
                }
                if ($request->filled("kerapihan.{$siswa->id}")) {
                    $karakterData['kerapihan'] = $request->input("kerapihan.{$siswa->id}");
                }
    
                // PERUBIKAN 2: Hanya proses jika ada data yang diisi untuk siswa ini
                if (!empty($karakterData)) {
                    // Cek keberadaan data
                    $existingKarakter = PenilaianKarakter::where('siswa_id', $siswa->id)
                        ->where('semester', $data['semester'])
                        ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
                        ->first();
    
                    if ($existingKarakter) {
                        // Jika ada, update
                        $existingKarakter->update($karakterData);
                    } else {
                        // Jika belum, buat baru
                        PenilaianKarakter::create(array_merge([
                            'siswa_id' => $siswa->id,
                            'semester' => $data['semester'],
                            'tahun_ajaran' => $data['tahunAjaran']->nama_tahun,
                        ], $karakterData));
                    }
                    $savedCount++;
                }
            }
    
            $message = "Berhasil menyimpan {$savedCount} data penilaian karakter.";
            return $this->handleResponse($request, true, $message, 'karakter.index');
    
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'karakter.index', $e);
        }
    }
    
    /**
     * Menghapus data penilaian karakter untuk satu siswa.
     *
     * @param PenilaianKarakter $penilaianKarakter
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function karakterDestroy(PenilaianKarakter $penilaianKarakter, Request $request)
    {
        try {
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // Pastikan nilai yang akan dihapus milik siswa di kelas wali yang bersangkutan
            if ($penilaianKarakter->siswa->kelas_id != $data['kelas']->id) {
                $message = 'Anda tidak memiliki akses untuk menghapus data ini.';
                return $this->handleResponse($request, false, $message, 'karakter.index', 403);
            }
    
            $penilaianKarakter->delete();
            $message = 'Data penilaian karakter berhasil dihapus.';
    
            return $this->handleResponse($request, true, $message, 'karakter.index');
    
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'karakter.index', $e);
        }
    }

    /**
     * Menampilkan halaman input absensi.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function absensiIndex(Request $request)
    {
        $data = $this->getWaliData($request);
    
        if (!$data) {
            return redirect()->route('wali.dashboard')->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }
    
        $siswa = Siswa::where('kelas_id', $data['kelas']->id)
            ->orderBy('nama', 'asc')
            ->get();
            
        $absensi = Absensi::where('semester', $data['semester'])
            ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->keyBy('siswa_id');
    
        return view('wali.absensi.index', array_merge($data, compact('siswa', 'absensi')));
    }
    
    /**
     * Menyimpan atau memperbarui data absensi.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function absensiStore(Request $request)
    {
        try {
            // PERUBAHAN 1: Validasi field menjadi nullable (boleh kosong)
            $request->validate([
                'siswa_id' => 'required|exists:siswa,id',
                'sakit' => 'nullable|integer|min:0',
                'izin' => 'nullable|integer|min:0',
                'alpa' => 'nullable|integer|min:0',
            ]);
    
            // PERUBAHAN 2: Validasi kustom, pastikan setidaknya satu field diisi
            if (!$request->filled('sakit') && !$request->filled('izin') && !$request->filled('alpa')) {
                throw ValidationException::withMessages([
                    'nilai' => 'Setidaknya satu nilai (Sakit, Izin, atau Alpa) harus diisi.',
                ]);
            }
    
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // PERBAIKAN: Cek apakah tahun ajaran ada
            if (!$data['tahunAjaran']) {
                $message = 'Tahun ajaran aktif tidak ditemukan. Silakan hubungi administrator.';
                return $this->handleResponse($request, false, $message, 'dashboard', 400);
            }
    
            $siswa = Siswa::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $data['kelas']->id) {
                $message = 'Anda tidak memiliki akses ke siswa ini.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // PERUBAHAN 3: Siapkan data hanya untuk field yang diisi
            $absensiData = [];
            if ($request->filled('sakit')) {
                $absensiData['sakit'] = $request->sakit;
            }
            if ($request->filled('izin')) {
                $absensiData['izin'] = $request->izin;
            }
            if ($request->filled('alpa')) {
                $absensiData['alpa'] = $request->alpa;
            }
    
            // PERUBAHAN 4: Cek keberadaan data lalu update atau create
            $absensi = Absensi::where('siswa_id', $request->siswa_id)
                ->where('semester', $data['semester'])
                ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
                ->first();
    
            if ($absensi) {
                // Jika data sudah ada, update hanya field yang dikirim
                $absensi->update($absensiData);
                $message = 'Data absensi berhasil diperbarui.';
            } else {
                // Jika data belum ada, buat baru
                Absensi::create(array_merge([
                    'siswa_id' => $request->siswa_id,
                    'semester' => $data['semester'],
                    'tahun_ajaran' => $data['tahunAjaran']->nama_tahun,
                ], $absensiData));
                $message = 'Data absensi berhasil ditambahkan.';
            }
    
            return $this->handleResponse($request, true, $message, 'absensi.index');
    
        } catch (ValidationException $e) {
            return $this->handleValidationException($request, $e, 'absensi.index');
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'absensi.index', $e);
        }
    }
    
    /**
     * Menyimpan data absensi untuk semua siswa sekaligus.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function absensiStoreAll(Request $request)
    {
        try {
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // PERBAIKAN: Cek apakah tahun ajaran ada
            if (!$data['tahunAjaran']) {
                $message = 'Tahun ajaran aktif tidak ditemukan. Silakan hubungi administrator.';
                return $this->handleResponse($request, false, $message, 'dashboard', 400);
            }
    
            $siswaList = Siswa::where('kelas_id', $data['kelas']->id)->get();
            $savedCount = 0;
    
            foreach ($siswaList as $siswa) {
                // PERUBAHAN 1: Siapkan data hanya untuk field yang diisi per siswa
                $absensiData = [
                    'sakit' => $request->input("sakit.{$siswa->id}"),
                    'izin' => $request->input("izin.{$siswa->id}"),
                    'alpa' => $request->input("alpa.{$siswa->id}"),
                ];
    
                // PERUBAHAN 2: Hanya proses jika ada data yang diisi untuk siswa ini (logika OR)
                if ($absensiData['sakit'] !== '' || $absensiData['izin'] !== '' || $absensiData['alpa'] !== '') {
                    // Cek keberadaan data
                    $existingAbsensi = Absensi::where('siswa_id', $siswa->id)
                        ->where('semester', $data['semester'])
                        ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
                        ->first();
    
                    if ($existingAbsensi) {
                        // Jika ada, update
                        $existingAbsensi->update($absensiData);
                    } else {
                        // Jika belum, buat baru
                        Absensi::create(array_merge([
                            'siswa_id' => $siswa->id,
                            'semester' => $data['semester'],
                            'tahun_ajaran' => $data['tahunAjaran']->nama_tahun,
                        ], $absensiData));
                    }
                    $savedCount++;
                }
            }
    
            $message = "Berhasil menyimpan {$savedCount} data absensi.";
            return $this->handleResponse($request, true, $message, 'absensi.index');
    
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'absensi.index', $e);
        }
    }
    
    /**
     * Menghapus data absensi untuk satu siswa.
     *
     * @param Absensi $absensi
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function absensiDestroy(Absensi $absensi, Request $request)
    {
        try {
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // Pastikan nilai yang akan dihapus milik siswa di kelas wali yang bersangkutan
            if ($absensi->siswa->kelas_id != $data['kelas']->id) {
                $message = 'Anda tidak memiliki akses untuk menghapus data ini.';
                return $this->handleResponse($request, false, $message, 'absensi.index', 403);
            }
    
            $absensi->delete();
            $message = 'Data absensi berhasil dihapus.';
    
            return $this->handleResponse($request, true, $message, 'absensi.index');
    
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'absensi.index', $e);
        }
    }

    /**
     * Menampilkan halaman input catatan rapor.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function catatanIndex(Request $request)
    {
        $data = $this->getWaliData($request);

        if (!$data) {
            return redirect()->route('wali.dashboard')->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }

        $siswa = Siswa::where('kelas_id', $data['kelas']->id)
            ->orderBy('nama', 'asc')
            ->get();
            
        $catatanRapor = CatatanRapor::where('semester', $data['semester'])
            ->where('tahun_ajaran', $data['tahunAjaran']->nama_tahun)
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        return view('wali.catatan.index', array_merge($data, compact('siswa', 'catatanRapor')));
    }

    /**
     * Menyimpan atau memperbarui catatan rapor.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function catatanStore(Request $request)
    {
        try {
            $request->validate([
                'siswa_id' => 'required|exists:siswa,id',
                'catatan' => 'required|string|max:500',
            ]);

            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }

            if (!$data['tahunAjaran']) {
                $message = 'Tahun ajaran aktif tidak ditemukan. Silakan hubungi administrator.';
                return $this->handleResponse($request, false, $message, 'dashboard', 400);
            }

            $siswa = Siswa::findOrFail($request->siswa_id);
            if ($siswa->kelas_id != $data['kelas']->id) {
                $message = 'Anda tidak memiliki akses ke siswa ini.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }

            $catatanRapor = CatatanRapor::updateOrCreate(
                [
                    'siswa_id' => $request->siswa_id,
                    'semester' => $data['semester'],
                    'tahun_ajaran' => $data['tahunAjaran']->nama_tahun,
                ],
                $request->only('catatan')
            );

            $message = $catatanRapor->wasRecentlyCreated ? 'Catatan berhasil ditambahkan.' : 'Catatan berhasil diperbarui.';
            return $this->handleResponse($request, true, $message, 'catatan.index');

        } catch (ValidationException $e) {
            return $this->handleValidationException($request, $e, 'catatan.index');
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'catatan.index', $e);
        }
    }

    /**
     * Menyimpan catatan rapor untuk semua siswa sekaligus.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function catatanStoreAll(Request $request)
    {
        try {
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }

            if (!$data['tahunAjaran']) {
                $message = 'Tahun ajaran aktif tidak ditemukan. Silakan hubungi administrator.';
                return $this->handleResponse($request, false, $message, 'dashboard', 400);
            }

            $siswaList = Siswa::where('kelas_id', $data['kelas']->id)->get();
            $savedCount = 0;

            foreach ($siswaList as $siswa) {
                $catatanData = [
                    'catatan' => $request->input("catatan.{$siswa->id}"),
                ];

                if (!is_null($catatanData['catatan']) && trim($catatanData['catatan']) !== '') {
                    CatatanRapor::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'semester' => $data['semester'],
                            'tahun_ajaran' => $data['tahunAjaran']->nama_tahun,
                        ],
                        $catatanData
                    );
                    $savedCount++;
                }
            }

            $message = "Berhasil menyimpan {$savedCount} data catatan rapor.";
            return $this->handleResponse($request, true, $message, 'catatan.index');

        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'catatan.index', $e);
        }
    }
    
    /**
     * Menghapus data catatan rapor untuk satu siswa.
     *
     * @param CatatanRapor $catatanRapor
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function catatanDestroy(CatatanRapor $catatanRapor, Request $request)
    {
        try {
            $data = $this->getWaliData($request);
            if (!$data) {
                $message = 'Akses ditolak.';
                return $this->handleResponse($request, false, $message, 'dashboard', 403);
            }
    
            // Pastikan catatan yang akan dihapus milik siswa di kelas wali yang bersangkutan
            if ($catatanRapor->siswa->kelas_id != $data['kelas']->id) {
                $message = 'Anda tidak memiliki akses untuk menghapus data ini.';
                return $this->handleResponse($request, false, $message, 'catatan.index', 403);
            }
    
            $catatanRapor->delete();
            $message = 'Data catatan berhasil dihapus.';
    
            return $this->handleResponse($request, true, $message, 'catatan.index');
    
        } catch (\Exception $e) {
            return $this->handleGeneralException($request, 'catatan.index', $e);
        }
    }

    // --- HELPER METHODS ---

    /**
     * Menangani respons sukses/error untuk AJAX dan request biasa.
     *
     * @param Request $request
     * @param bool $success
     * @param string $message
     * @param string $route
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function handleResponse(Request $request, bool $success, string $message, string $route, int $statusCode = 200)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $message
            ], $success ? 200 : $statusCode);
        }

        $type = $success ? 'success' : 'error';
        return redirect()->route("wali.{$route}")->with($type, $message);
    }

    /**
     * Menangani exception validasi.
     *
     * @param Request $request
     * @param ValidationException $e
     * @param string $route
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function handleValidationException(Request $request, ValidationException $e, string $route)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal. ' . implode(', ', $e->errors()->all())
            ], 422);
        }

        return redirect()->route("wali.{$route}")
            ->withErrors($e->validator)
            ->withInput();
    }

    /**
     * Menangani exception umum.
     *
     * @param Request $request
     * @param string $route
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function handleGeneralException(Request $request, string $route, \Exception $e)
    {
        // Log error untuk debugging
        \Log::error('General Exception in WaliController: ' . $e->getMessage());
        
        $message = 'Terjadi kesalahan. Silakan coba lagi.';
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 500);
        }

        return redirect()->route("wali.{$route}")->with('error', $message);
    }
}