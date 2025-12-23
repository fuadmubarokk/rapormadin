<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\NilaiNonTulis;
use App\Models\PenilaianKarakter;
use App\Models\Absensi;
use App\Models\CatatanRapor;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\SettingSekolah;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use PDF;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Intervention\Image\Facades\Image;

class RaporController extends Controller
{
    /**
     * Constructor untuk menerapkan middleware 'wali'.
     */
    public function __construct()
    {
        $this->middleware('wali.or.admin');
    }

    /**
     * Mengambil data dasar kelas (kelas, tahun ajaran, semester).
     * Method ini direvisi agar bisa diakses oleh Admin dan Wali Kelas.
     *
     * @param Request $request
     * @param int|null $kelasId
     * @return array|null
     */
    private function getWaliData(Request $request, $kelasId = null)
    {
        $user = auth()->user();
        $kelas = null;

        if ($user->isWaliKelas()) {
            $kelas = $user->kelasWali;
        } elseif ($user->isAdmin()) {
            if ($kelasId) {
                $kelas = Kelas::find($kelasId);
            }
        }

        if (!$kelas) {
            return null;
        }

        $tahunAjaran = TahunAjaran::where('status', true)->first();
        if (!$tahunAjaran) {
            return null;
        }
        $semester = $tahunAjaran->semester;

        return compact('user', 'kelas', 'tahunAjaran', 'semester');
    }

    /**
     * Mengambil data siswa untuk kelas tertentu (digunakan oleh AJAX dan Wali Kelas).
     *
     * @param int $kelasId
     * @param Request $request
     * @param bool $isAjax
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function getSiswaData($kelasId, Request $request, $isAjax = true)
    {
        $waliData = $this->getWaliData($request, $kelasId);

        if (!$waliData) {
            if ($isAjax) {
                return response()->json(['error' => 'Akses ditolak atau kelas tidak ditemukan.'], 403);
            }
            return redirect()->route('rapor.index')->with('error', 'Akses ditolak atau kelas tidak ditemukan.');
        }

        $siswaList = Siswa::where('kelas_id', $waliData['kelas']->id)->orderBy('nama')->get();
        $dataKelengkapan = [];
        $jumlahLengkap = 0;
        $jumlahBelumLengkap = 0;

        foreach ($siswaList as $siswa) {
            $persentase = $this->calculateProgressPercentage($siswa->id, $waliData);
            $isLengkap = ($persentase == 100.0);
            $dataKelengkapan[$siswa->id] = ['lengkap' => $isLengkap, 'persentase' => $persentase];
            if ($isLengkap) $jumlahLengkap++; else $jumlahBelumLengkap++;
        }

        $totalSiswa = $siswaList->count();
        $persentaseLengkap = $totalSiswa > 0 ? round(($jumlahLengkap / $totalSiswa) * 100, 1) : 0;
        $persentaseBelumLengkap = $totalSiswa > 0 ? round(($jumlahBelumLengkap / $totalSiswa) * 100, 1) : 0;

        $data = array_merge($waliData, compact(
            'siswaList', 'dataKelengkapan', 'jumlahLengkap', 'jumlahBelumLengkap', 'persentaseLengkap', 'persentaseBelumLengkap'
        ));

        if ($isAjax) {
            $html = view('rapor._siswa_list', $data)->render();
            return response()->json(['html' => $html]);
        }

        return view('rapor.index', $data);
    }

    /**
     * Menghitung ranking siswa di kelas berdasarkan total nilai.
     * Method ini dioptimasi untuk mengurangi jumlah query ke database.
     *
     * @param int $kelasId
     * @param string $semester
     * @param string $tahunAjaran
     * @return array
     */
    private function calculateRanking($kelasId, $semester, $tahunAjaran)
    {
        // Ambil semua nilai tulis siswa di kelas tersebut
        $nilaiTulisKelas = Nilai::with('siswa')
            ->whereHas('guruMapelKelas', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get()
            ->groupBy('siswa_id');

        // Ambil semua nilai non tulis siswa di kelas tersebut
        $nilaiNonTulisKelas = NilaiNonTulis::where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->whereIn('siswa_id', Siswa::where('kelas_id', $kelasId)->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        $rankingData = [];
        $allSiswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id');

        foreach ($allSiswaIds as $siswaId) {
            $total = 0;
            // Hitung total nilai tulis
            if (isset($nilaiTulisKelas[$siswaId])) {
                foreach ($nilaiTulisKelas[$siswaId] as $nilai) {
                    $total += $nilai->nilai_uas;
                }
            }
            // Tambahkan nilai non tulis jika ada
            if (isset($nilaiNonTulisKelas[$siswaId])) {
                $qiroatul = is_numeric($nilaiNonTulisKelas[$siswaId]->qiroatul_kutub)
                    ? (int)$nilaiNonTulisKelas[$siswaId]->qiroatul_kutub : 0;

                $taftisyul = is_numeric($nilaiNonTulisKelas[$siswaId]->taftisyul_kutub)
                    ? (int)$nilaiNonTulisKelas[$siswaId]->taftisyul_kutub : 0;

                $total += $qiroatul + $taftisyul;

            }
            $rankingData[$siswaId] = $total;
        }

        // Urutkan dari nilai tertinggi ke terendah
        arsort($rankingData);
        
        return $rankingData;
    }

/**
 * Mengambil semua data yang diperlukan untuk menampilkan satu rapor siswa.
 * Method ini digunakan oleh show() dan cetak() untuk menghindari duplikasi.
 */
private function getRaporData($siswaId, $waliData)
{
    try {
        // --- PERUBAHAN 1: Load siswa beserta relasi kelas dan angkatannya ---
        // Ini penting untuk mendapatkan angkatan_id siswa dengan efisien
        $siswa = Siswa::with('kelas.angkatan')->findOrFail($siswaId);

        // Pastikan siswa menempati kelas wali
        if ($siswa->kelas_id != $waliData['kelas']->id) {
            return null;
        }
        // --- PERUBAHAN 2: Query untuk mengambil nilai TELAH DIUBAH SEKALIAN ---
         $nilaiTulis = Nilai::with('guruMapelKelas.mapel')
            // ---> PERBAIKAN 1: GANTI 'nilais' MENJADI 'nilai' <---
            ->join('guru_mapel_kelas', 'nilai.guru_mapel_kelas_id', '=', 'guru_mapel_kelas.id')
            // ----------------------------------------------------
            ->join('mapel', 'guru_mapel_kelas.mapel_id', '=', 'mapel.id') // Sudah benar
            // === JOIN INI YANG KRUSIAL ===
            // Hubungkan tabel mapel dengan tabel mapel_angkatan
            ->join('mapel_angkatan', function ($join) use ($siswa) {
                $join->on('mapel.id', '=', 'mapel_angkatan.mapel_id')
                     // Filter hanya untuk angkatan siswa ini
                     ->where('mapel_angkatan.angkatan_id', $siswa->kelas->angkatan->id);
            })
            // ==============================
            ->where('nilai.siswa_id', $siswaId)
            ->where('nilai.semester', $waliData['semester'])
            ->where('nilai.tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
            // === URUTKAN BERDASARKAN KOLOM 'urutan' DI TABEL 'mapel_angkatan' ===
            ->orderBy('mapel_angkatan.urutan', 'asc')
            // ================================================================
            // Penting: select hanya kolom dari tabel utama untuk menghindari error
            ->select('nilai.*')
            ->get()
            ->map(function ($item) {
                // Tandai nilai yang di bawah KKM
                $item->is_below_kkm = $item->nilai_uas < $item->guruMapelKelas->mapel->kkm;
                return $item;
            });

        // --- Kode selanjutnya TIDAK PERLU DIUBAH ---
        // Ambil nilai non tulis (satu baris)
        $nilaiNonTulis = NilaiNonTulis::where('siswa_id', $siswaId)
            ->where('semester', $waliData['semester'])
            ->where('tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
            ->first();
                
        // ==================== TAMBAHKAN KODE INI ====================
        if ($nilaiNonTulis) {
            // Daftar nilai yang dianggap BAIK
            $nilaiBaik = ['جيد', 'ممتاز'];
        
            // Bersihkan dan ubah ke huruf kecil, lalu cek apakah ada di daftar nilai baik
            $nilaiMuhafadzhoh = trim(strtolower($nilaiNonTulis->muhafadzhoh ?? ''));
            $nilaiNonTulis->is_muhafadzhoh_baik = in_array($nilaiMuhafadzhoh, $nilaiBaik);
        
            // Ambil KKM untuk Qiroatul dari database
            $mapelQiroatul = \App\Models\Mapel::where('nama_mapel', 'Qiroatul Kutub')->first();
            $kkmQiroatul = $mapelQiroatul ? $mapelQiroatul->kkm : 70; // Fallback ke 70 jika tidak ditemukan
            $nilaiNonTulis->is_qiroatul_below_kkm = is_numeric($nilaiNonTulis->qiroatul_kutub) && $nilaiNonTulis->qiroatul_kutub < $kkmQiroatul;
        
            // Ambil KKM untuk Taftisyul dari database
            $mapelTaftisyul = \App\Models\Mapel::where('nama_mapel', 'Taftisyul Kutub')->first();
            $kkmTaftisyul = $mapelTaftisyul ? $mapelTaftisyul->kkm : 70; // Fallback ke 70 jika tidak ditemukan
            $nilaiNonTulis->is_taftisyul_below_kkm = is_numeric($nilaiNonTulis->taftisyul_kutub) && $nilaiNonTulis->taftisyul_kutub < $kkmTaftisyul;
        }
        // ==================== AKHIR KODE TAMBAHAN ====================

        // Data tambahan
        $penilaianKarakter = PenilaianKarakter::where('siswa_id', $siswaId)
            ->where('semester', $waliData['semester'])
            ->where('tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
            ->first();

        $absensi = Absensi::where('siswa_id', $siswaId)
            ->where('semester', $waliData['semester'])
            ->where('tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
            ->first();

        $catatanRapor = CatatanRapor::where('siswa_id', $siswaId)
            ->where('semester', $waliData['semester'])
            ->where('tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
            ->first();

        // Hitung total nilai untuk ranking
        $totalNilai = $nilaiTulis->sum('nilai_uas');
        $jumlahMapel = $nilaiTulis->count();

        if ($nilaiNonTulis) {
            $qiroatul = is_numeric($nilaiNonTulis->qiroatul_kutub) ? (int)$nilaiNonTulis->qiroatul_kutub : 0;
            $taftisyul = is_numeric($nilaiNonTulis->taftisyul_kutub) ? (int)$nilaiNonTulis->taftisyul_kutub : 0;

            // Jangan jumlahkan muhafadzhoh (karena teks)
            $totalNilai += $qiroatul + $taftisyul;
            $jumlahMapel += ($qiroatul > 0 ? 1 : 0) + ($taftisyul > 0 ? 1 : 0);
        }

        $rataRata = $jumlahMapel > 0 ? round($totalNilai / $jumlahMapel, 2) : 0;

        // Hitung ranking
        $rankingData = $this->calculateRanking(
            $waliData['kelas']->id,
            $waliData['semester'],
            $waliData['tahunAjaran']->nama_tahun
        );

        $rankingKeys = array_keys($rankingData);
        $ranking = array_search($siswaId, $rankingKeys);
        $ranking = $ranking !== false ? $ranking + 1 : '-';

        // Ambil pengaturan madrasah
        $settingSekolah = SettingSekolah::first();

        //-- PERUBAHAN: Ambil data user wali kelas (objek lengkap) --
        $waliKelasUser = null;
        if ($siswa->kelas && $siswa->kelas->wali_id) {
            $waliKelasUser = \App\Models\User::find($siswa->kelas->wali_id);
        }
        //-- AKHIR PERUBAHAN --

        return compact(
            'siswa',
            'nilaiTulis',
            'nilaiNonTulis',
            'penilaianKarakter',
            'absensi',
            'catatanRapor',
            'totalNilai',
            'rataRata',
            'ranking',
            'rankingData',
            'settingSekolah',
            'waliKelasUser'
        );

    } catch (ModelNotFoundException $e) {
        return null;
    }
}

    /**
     * Menampilkan halaman pemilihan kelas (untuk Admin) atau langsung ke daftar siswa (untuk Wali).
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function pilihKelasView(Request $request)
    {
        $user = auth()->user();

        if ($user->isWaliKelas()) {
            // Jika Wali, langsung ambil data kelasnya dan tampilkan
            $waliData = $this->getWaliData($request);
            if (!$waliData) {
                return redirect()->route('dashboard')->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
            }
            // Kita akan memanggil logika yang sama seperti method getSiswaData
            return $this->getSiswaData($waliData['kelas']->id, $request, false); // `false` berarti bukan response AJAX
        }

        // Jika Admin, tampilkan halaman dengan dropdown kelas
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('rapor.index', compact('kelasList'));
    }

    /**
     * Menampilkan daftar siswa dan status kelengkapan data rapor.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $waliData = $this->getWaliData($request);

        if (!$waliData) {
            return redirect()->route('wali.dashboard')->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }

        $siswaList = Siswa::where('kelas_id', $waliData['kelas']->id)->orderBy('nama')->get();
        $dataKelengkapan = [];

        // --- Inisialisasi variabel perhitungan ---
        $jumlahLengkap = 0;
        $jumlahBelumLengkap = 0;

        // --- Lakukan semua perhitungan dalam SATU loop saja ---
        foreach ($siswaList as $siswa) {
            // Hitung persentase kelengkapan data
            $persentase = $this->calculateProgressPercentage($siswa->id, $waliData);
            $isLengkap = ($persentase == 100.0); // Dianggap lengkap jika 100%

            $dataKelengkapan[$siswa->id] = [
                'lengkap' => $isLengkap,
                'persentase' => $persentase // Simpan persentase
            ];

            if ($isLengkap) {
                $jumlahLengkap++;
            } else {
                $jumlahBelumLengkap++;
            }
        }

        // --- Hitung persentase ---
        $totalSiswa = $siswaList->count();
        $persentaseLengkap = $totalSiswa > 0 ? round(($jumlahLengkap / $totalSiswa) * 100, 1) : 0;
        $persentaseBelumLengkap = $totalSiswa > 0 ? round(($jumlahBelumLengkap / $totalSiswa) * 100, 1) : 0;

        // --- KIRIM SEMUA VARIABEL ke view ---
        return view('rapor.index', array_merge($waliData, compact(
            'siswaList', 
            'dataKelengkapan', 
            'jumlahLengkap', 
            'jumlahBelumLengkap', 
            'persentaseLengkap', 
            'persentaseBelumLengkap'
        )));
    }

    /**
     * Menampilkan halaman detail rapor siswa.
     *
     * @param string $siswaId
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($siswaId, Request $request)
    {
        $siswa = Siswa::find($siswaId);
        if (!$siswa) {
            return redirect()->route('rapor.index')->with('error', 'Siswa tidak ditemukan.');
        }
        $kelasId = $siswa->kelas_id;

        $waliData = $this->getWaliData($request, $kelasId);
        if (!$waliData) {
            return redirect()->route('rapor.index')->with('error', 'Akses ditolak.');
        }

        $raporData = $this->getRaporData($siswaId, $waliData);
        if (!$raporData) {
            return redirect()->route('rapor.index')->with('error', 'Data rapor tidak ditemukan.');
        }

        return view('rapor.show', array_merge($waliData, $raporData));
    }

    /**
     * Mencetak rapor siswa.
     *
     * @param string $siswaId
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function cetakRapor($siswaId, Request $request)
    {
        // --- Langkah 1: Ambil Data Siswa dan Validasi Akses ---
        $siswa = \App\Models\Siswa::find($siswaId);
        if (!$siswa) {
            return redirect()->route('rapor.index')->with('error', 'Siswa tidak ditemukan.');
        }
    
        $waliData = $this->getWaliData($request, $siswa->kelas_id);
        if (!$waliData) {
            return redirect()->route('rapor.index')->with('error', 'Akses ditolak. Anda tidak memiliki akses ke kelas siswa ini.');
        }
    
        // --- Langkah 2: Siapkan Semua Data yang Dibutuhkan ---
        // Gunakan helper method yang sama seperti di show() untuk menghindari duplikasi
        // dan memastikan semua data (termasuk absensi) terambil.
        $raporData = $this->getRaporData($siswaId, $waliData);
    
        if (!$raporData) {
            return redirect()->route('rapor.index')->with('error', 'Data rapor tidak ditemukan atau belum lengkap.');
        }
    
        // Ambil setting sekolah
        $settingSekolah = \App\Models\SettingSekolah::first();
        if (!$settingSekolah) {
            return redirect()->route('rapor.index')->with('error', 'Data pengaturan sekolah belum diisi. Silakan hubungi administrator.');
        }
    
        // Siapkan variabel tambahan yang dibutuhkan view
        $waliSantri = $siswa->nama_ayah ?: $siswa->nama_ibu;
        $jumlahSiswaDiKelas = count($raporData['rankingData']);
    
        // Gabungkan semua data ke dalam satu array
        // PERHATIKAN: Semua data yang dibutuhkan view rapor.pdf.blade.php ada di sini
        $data = array_merge($raporData, [
            'waliData'       => $waliData,
            'waliSantri'     => $waliSantri,
            'jumlahSiswaDiKelas' => $jumlahSiswaDiKelas,
            'settingSekolah' => $settingSekolah,
        ]);
    
        // --- Langkah 3: Proses Pembuatan PDF dengan mPDF ---
        try {
            ini_set('pcre.backtrack_limit', '1000000');
    
            // Konfigurasi mPDF
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];
    
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [215, 330],
                'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
                'fontdata' => $fontData + [
                    'traditionalarabic' => [
                        'R' => 'trado.ttf',
                        'B' => 'tradbdo.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ],
                ],
                'default_font' => 'traditionalarabic',
                'autoLangToFont' => true,
                'autoScriptToLang' => true,
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_header' => 0,
            ]);
    
            // Render view menjadi HTML dengan semua data yang sudah disiapkan
            $html = view('rapor.pdf', $data)->render();
            $mpdf->WriteHTML($html);
    
            // Proses download file
            $namaSiswa   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $siswa->nama);
            $namaKelas   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $waliData['kelas']->nama_kelas);
            $tahunAjaran = preg_replace('/[^A-Za-z0-9_\-]/', '_', $waliData['tahunAjaran']->nama_tahun);
            $namaFile    = "Rapor_{$namaSiswa}_{$namaKelas}_{$waliData['semester']}_{$tahunAjaran}.pdf";
    
            return response($mpdf->Output($namaFile, 'I'))
                ->header('Content-Type', 'application/pdf');
    
        } catch (\Exception $e) {
            return redirect()->route('rapor.index')->with('error', 'Gagal mencetak PDF: ' . $e->getMessage());
        }
    }
        
    /**
     * Mencetak semua rapor siswa yang datanya lengkap dalam satu kelas.
     *
     * @param int $kelasId
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function cetakSemuaRapor($kelasId, Request $request)
    {
        // --- Langkah 1: Validasi Akses ---
        $waliData = $this->getWaliData($request, $kelasId);
        if (!$waliData) {
            return redirect()->route('rapor.index')->with('error', 'Akses ditolak.');
        }
    
        // --- Langkah 2: Ambil Semua Siswa di Kelas Tersebut ---
        $allSiswaInClass = \App\Models\Siswa::where('kelas_id', $kelasId)->orderBy('nama')->get();
    
        // --- Langkah 3: Filter Siswa yang Datanya Lengkap ---
        $siswaLengkap = $allSiswaInClass->filter(function ($siswa) use ($waliData) {
            return $this->isRaporDataLengkap($siswa->id, $waliData);
        });
    
        if ($siswaLengkap->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada rapor yang lengkap untuk dapat dicetak di kelas ini.');
        }
    
        // --- Langkah 4: Siapkan Pengaturan Sekolah (sekali saja) ---
        $settingSekolah = \App\Models\SettingSekolah::first();
        if (!$settingSekolah) {
            return redirect()->route('rapor.index')->with('error', 'Data pengaturan sekolah belum diisi.');
        }
    
        // --- Langkah 5: Siapkan data ranking untuk semua siswa ---
        $rankingData = $this->calculateRanking(
            $kelasId,
            $waliData['semester'],
            $waliData['tahunAjaran']->nama_tahun
        );
        $jumlahSiswaDiKelas = count($rankingData);
    
        // --- Langkah 6: Inisialisasi mPDF ---
        try {
            ini_set('pcre.backtrack_limit', '2000000');
            ini_set('memory_limit', '1G');
            set_time_limit(600); // 10 menit
    
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];
    
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [215, 330],
                'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
                'fontdata' => $fontData + [
                    'traditionalarabic' => [
                        'R' => 'trado.ttf',
                        'B' => 'tradbdo.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ],
                ],
                'default_font' => 'traditionalarabic',
                'autoLangToFont' => true,
                'autoScriptToLang' => true,
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_header' => 0,
            ]);
            
            $mpdf->showImageErrors = true;
    
            // --- Langkah 7: Looping Siswa, Tulis Langsung ke PDF ---
            $totalSiswa = $siswaLengkap->count();
    
            foreach ($siswaLengkap as $index => $siswa) {
                // Gunakan helper method yang sama untuk setiap siswa
                $raporData = $this->getRaporData($siswa->id, $waliData);
    
                if (!$raporData) {
                    continue;
                }
    
                $waliSantri = $siswa->nama_ayah ?: $siswa->nama_ibu;
    
                // Gabungkan semua data untuk dikirim ke view
                $data = array_merge($raporData, [
                    'waliData'       => $waliData,
                    'waliSantri'     => $waliSantri,
                    'jumlahSiswaDiKelas' => $jumlahSiswaDiKelas,
                    'settingSekolah' => $settingSekolah,
                ]);
    
                // Render view rapor untuk siswa saat ini
                $html = view('rapor.pdf', $data)->render();
                
                // TULIS LANGSUNG KE PDF
                $mpdf->WriteHTML($html);
                
                // TAMBAHKAN HALAMAN BARU jika ini bukan siswa terakhir
                if ($index < $totalSiswa - 1) {
                    $mpdf->AddPage();
                }
            }
    
            // --- Langkah 8: Proses Download File (TANPA DISIMPAN) ---
            $namaKelas   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $waliData['kelas']->nama_kelas);
            $tahunAjaran = preg_replace('/[^A-Za-z0-9_\-]/', '_', $waliData['tahunAjaran']->nama_tahun);
            $namaFile    = "Rekap_Rapor_Kelas_{$namaKelas}_Semester_{$waliData['semester']}_{$tahunAjaran}.pdf";
    
            // Langsung kirim file ke browser untuk diunduh
            return response($mpdf->Output($namaFile, 'I'))
                ->header('Content-Type', 'application/pdf');
    
        } catch (\Exception $e) {
            \Log::error('Gagal mencetak semua rapor: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal mencetak PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Mencetak cover rapor siswa (hanya halaman pertama).
     *
     * @param string $siswaId
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function cetakCover($siswaId, Request $request)
    {
        // --- Langkah 1: Ambil Data Siswa dan Validasi Akses ---
        $siswa = \App\Models\Siswa::find($siswaId);
        if (!$siswa) {
            return redirect()->route('rapor.index')->with('error', 'Siswa tidak ditemukan.');
        }

        // PERUBAHAN: Panggil getWaliData dengan kelasId dari siswa
        $waliData = $this->getWaliData($request, $siswa->kelas_id);
        if (!$waliData) {
            return redirect()->route('rapor.index')->with('error', 'Akses ditolak. Anda tidak memiliki akses ke kelas siswa ini.');
        }

        // --- Langkah 2: Siapkan Data yang Dibutuhkan ---
        $settingSekolah = \App\Models\SettingSekolah::first();
        if (!$settingSekolah) {
            return redirect()->route('rapor.index')->with('error', 'Data pengaturan sekolah belum diisi.');
        }

        // --- Langkah 3: Proses Logo (Menggunakan Intervention Image v2) ---
        $logoPath = null;
        if ($settingSekolah->logo && file_exists(public_path('img/logo/' . $settingSekolah->logo))) {
            $originalLogoPath = public_path('img/logo/' . $settingSekolah->logo);
            $resizedFilename = 'resized_cover_logo_' . $settingSekolah->logo;
            $resizedLogoPath = public_path('img/logo/' . $resizedFilename);

            try {
                Image::make($originalLogoPath)
                    ->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save($resizedLogoPath);

                $logoPath = $resizedLogoPath;
            } catch (\Exception $e) {
                \Log::error('Gagal memproses logo: ' . $e->getMessage());
            }
        }

        // --- Langkah 4: Siapkan Data untuk View ---
        $data = [
            'siswa'          => $siswa,
            'settingSekolah' => $settingSekolah,
            'waliData'       => $waliData,
            'logoPath'       => $logoPath,
        ];

        // --- Langkah 5: Inisialisasi dan Proses mPDF ---
        try {
            ini_set('pcre.backtrack_limit', '1000000');

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [215, 330],
                'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
                'fontdata' => $fontData + [
                    'traditionalarabic' => [
                        'R' => 'trado.ttf',
                        'B' => 'tradbdo.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ],
                ],
                'default_font' => 'traditionalarabic',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_header' => 0,
                'margin_footer' => 0,
            ]);
            
            $mpdf->showImageErrors = true;

            // Render view menjadi HTML
            $mpdf->SetTitle('Cover | ' . $siswa->nama);
            $html = view('rapor.cover', $data)->render();
            $mpdf->WriteHTML($html);

            // --- Langkah 6: Proses Download File ---
            $namaSiswa   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $siswa->nama);
            $namaKelas   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $waliData['kelas']->nama_kelas);
            $tahunAjaran = preg_replace('/[^A-Za-z0-9_\-]/', '_', $waliData['tahunAjaran']->nama_tahun);
            $namaFile    = "Cover_Rapor_{$namaSiswa}_{$namaKelas}_{$waliData['semester']}_{$tahunAjaran}.pdf";

            return response($mpdf->Output($namaFile, 'I'))
                ->header('Content-Type', 'application/pdf');

        } catch (\Exception $e) {
            \Log::error('Gagal mencetak cover rapor: ' . $e->getMessage());
            return redirect()->route('rapor.index')->with('error', 'Gagal mencetak PDF Cover: ' . $e->getMessage());
        }
    }
        
    /**
     * Mencetak cover rapor untuk semua siswa dalam satu kelas.
     *
     * @param int $kelasId
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function cetakSemuaCover($kelasId, Request $request)
    {
        // --- Langkah 1: Validasi Akses ---
        // PERUBAHAN: Panggil getWaliData dengan kelasId dari URL
        $waliData = $this->getWaliData($request, $kelasId);
        if (!$waliData) {
            return redirect()->route('rapor.index')->with('error', 'Akses ditolak.');
        }

        $allSiswaInClass = \App\Models\Siswa::where('kelas_id', $kelasId)->orderBy('nama')->get();

        if ($allSiswaInClass->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa di kelas ini.');
        }

        // --- Langkah 3: Siapkan Pengaturan Sekolah ---
        $settingSekolah = \App\Models\SettingSekolah::first();
        if (!$settingSekolah) {
            return redirect()->route('rapor.index')->with('error', 'Data pengaturan sekolah belum diisi.');
        }

        // --- Langkah 4: Proses Logo ---
        $logoPath = null;
        if ($settingSekolah->logo && file_exists(public_path('img/logo/' . $settingSekolah->logo))) {
            $originalLogoPath = public_path('img/logo/' . $settingSekolah->logo);
            $resizedFilename = 'resized_cover_logo_' . $settingSekolah->logo;
            $resizedLogoPath = public_path('img/logo/' . $resizedFilename);

            try {
                Image::make($originalLogoPath)
                    ->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save($resizedLogoPath);

                $logoPath = $resizedLogoPath;
            } catch (\Exception $e) {
                \Log::error('Gagal memproses logo: ' . $e->getMessage());
            }
        }

        // --- Langkah 5: Inisialisasi mPDF ---
        try {
            ini_set('pcre.backtrack_limit', '1000000');
            // Tambahkan ini untuk mencegah masalah memory/time jika kelasnya besar
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 menit

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [215, 330],
                'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
                'fontdata' => $fontData + [
                    'traditionalarabic' => [
                        'R' => 'trado.ttf',
                        'B' => 'tradbdo.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ],
                ],
                'default_font' => 'traditionalarabic',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_header' => 0,
                'margin_footer' => 0,
            ]);
            
            $mpdf->showImageErrors = true;
            $mpdf->SetTitle('Cover | ' . $waliData['kelas']->nama_kelas);

            // --- Langkah 6: Looping Siswa, Tulis HTML ke mPDF Satu Per Satu ---
            $totalSiswa = $allSiswaInClass->count();

            foreach ($allSiswaInClass as $index => $siswa) {
                // Siapkan data untuk setiap siswa
                $data = [
                    'siswa'          => $siswa,
                    'settingSekolah' => $settingSekolah,
                    'waliData'       => $waliData,
                    'logoPath'       => $logoPath,
                ];

                // Render view cover untuk siswa saat ini
                $html = view('rapor.cover', $data)->render();
                
                // Tulis HTML ke mPDF
                $mpdf->WriteHTML($html);
                
                // Tambahkan halaman baru kecuali untuk siswa terakhir
                if ($index < $totalSiswa - 1) {
                    $mpdf->AddPage();
                }
            }

            // --- Langkah 7: Unduh File PDF ---
            $namaKelas   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $waliData['kelas']->nama_kelas);
            $tahunAjaran = preg_replace('/[^A-Za-z0-9_\-]/', '_', $waliData['tahunAjaran']->nama_tahun);
            $namaFile    = "Cover_Rapor_Kelas_{$namaKelas}_Semester_{$waliData['semester']}_{$tahunAjaran}.pdf";

            return response($mpdf->Output($namaFile, 'I'))
                ->header('Content-Type', 'application/pdf');

        } catch (\Exception $e) {
            \Log::error('Gagal mencetak semua cover rapor: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencetak PDF Cover: ' . $e->getMessage());
        }
    }

    /**
     * Checks if a student's report data is complete for a given semester and year.
     *
     * @param int $siswaId
     * @param array $waliData
     * @return bool
     */
    private function isRaporDataLengkap($siswaId, $waliData)
    {
        $isLengkap = true;
        $tahunAjaran = $waliData['tahunAjaran']->nama_tahun;
        $semester = $waliData['semester'];

        // 1. CEK NILAI TULIS: Apakah siswa memiliki nilai untuk SEMUA mata pelajaran?
        // Ambil semua mata pelajaran yang WAJIB di kelas ini
        $requiredMapels = \App\Models\GuruMapelKelas::where('kelas_id', $waliData['kelas']->id)
            ->pluck('mapel_id');

        // Ambil mata pelajaran yang SUDAH DINILAI untuk siswa ini
        $studentNilaiMapels = \App\Models\Nilai::where('siswa_id', $siswaId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->join('guru_mapel_kelas', 'nilai.guru_mapel_kelas_id', '=', 'guru_mapel_kelas.id')
            ->pluck('guru_mapel_kelas.mapel_id');

        // Bandingkan. Jika ada mata pelajaran wajib yang belum dinilai, data tidak lengkap.
        if ($requiredMapels->diff($studentNilaiMapels)->isNotEmpty()) {
            $isLengkap = false;
        }

        // 2. CEK KOMPONEN LAIN: Apakah semua komponen lain sudah ada?
        // Kita bisa loop untuk mempersingkat kode.
        $components = [
            'nilaiNonTulis' => \App\Models\NilaiNonTulis::class,
            'penilaianKarakter' => \App\Models\PenilaianKarakter::class,
            'absensi' => \App\Models\Absensi::class,
            'catatanRapor' => \App\Models\CatatanRapor::class,
        ];

        foreach ($components as $componentModel) {
            $exists = $componentModel::where('siswa_id', $siswaId)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->exists();
            if (!$exists) {
                $isLengkap = false;
                break; // Tidak perlu cek lagi jika sudah ada yang tidak lengkap
            }
        }

        return $isLengkap;
    }

    /**
     * Menghitung persentase kelengkapan data rapor untuk satu siswa.
     *
     * @param int $siswaId
     * @param array $waliData
     * @return float
     */
    private function calculateProgressPercentage($siswaId, $waliData)
    {
        $tahunAjaran = $waliData['tahunAjaran']->nama_tahun;
        $semester = $waliData['semester'];
        $completedComponents = 0;
        $totalComponents = 0;

        // 1. Komponen Nilai Tulis (jumlah mata pelajaran)
        $requiredMapels = \App\Models\GuruMapelKelas::where('kelas_id', $waliData['kelas']->id)->count();
        $totalComponents += $requiredMapels;
        $completedNilaiTulis = \App\Models\Nilai::where('siswa_id', $siswaId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->join('guru_mapel_kelas', 'nilai.guru_mapel_kelas_id', '=', 'guru_mapel_kelas.id')
            ->where('guru_mapel_kelas.kelas_id', $waliData['kelas']->id)
            ->count();
        $completedComponents += $completedNilaiTulis;

        // 2. Komponen Nilai Non Tulis (dianggap 1 bagian)
        $totalComponents += 1;
        if (\App\Models\NilaiNonTulis::where('siswa_id', $siswaId)->where('tahun_ajaran', $tahunAjaran)->where('semester', $semester)->exists()) {
            $completedComponents++;
        }

        // 3. Komponen Penilaian Karakter
        $totalComponents += 1;
        if (\App\Models\PenilaianKarakter::where('siswa_id', $siswaId)->where('tahun_ajaran', $tahunAjaran)->where('semester', $semester)->exists()) {
            $completedComponents++;
        }

        // 4. Komponen Absensi
        $totalComponents += 1;
        if (\App\Models\Absensi::where('siswa_id', $siswaId)->where('tahun_ajaran', $tahunAjaran)->where('semester', $semester)->exists()) {
            $completedComponents++;
        }

        // 5. Komponen Catatan Rapor
        $totalComponents += 1;
        if (\App\Models\CatatanRapor::where('siswa_id', $siswaId)->where('tahun_ajaran', $tahunAjaran)->where('semester', $semester)->exists()) {
            $completedComponents++;
        }

        if ($totalComponents == 0) {
            return 0; // Hindari pembagian dengan nol
        }

        return round(($completedComponents / $totalComponents) * 100, 1);
    }
}