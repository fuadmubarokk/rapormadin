<?php

namespace App\Http\Controllers;

use App\Models\GuruMapelKelas;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\NilaiNonTulis;
use App\Models\PenilaianKarakter;
use App\Models\Absensi;
use App\Models\CatatanRapor;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mengambil data dasar wali kelas (kelas, tahun ajaran, semester).
     */
    private function getWaliData($user = null)
    {
        $wali = $user ?: auth()->user();
        $kelas = $wali->kelasWali;

        if (!$kelas) {
            return null;
        }

        $tahunAjaran = TahunAjaran::where('status', true)->first();

        if (!$tahunAjaran) {
            return null;
        }

        $semester = session('semester', $tahunAjaran->semester);

        return compact('wali', 'kelas', 'tahunAjaran', 'semester');
    }

    /**
     * Menghitung ranking siswa di kelas berdasarkan total nilai.
     */
    private function calculateRanking($kelasId = null, $semester, $tahunAjaran, $limit = 3)
    {
        $nilaiTulisQuery = Nilai::with('siswa')
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran);

        if ($kelasId) {
            $nilaiTulisQuery->whereHas('guruMapelKelas', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            });
        }

        $nilaiTulisKelas = $nilaiTulisQuery->get()->groupBy('siswa_id');

        $nilaiNonTulisQuery = NilaiNonTulis::where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran);

        if ($kelasId) {
            $nilaiNonTulisQuery->whereIn('siswa_id', Siswa::where('kelas_id', $kelasId)->pluck('id'));
        }

        $nilaiNonTulisKelas = $nilaiNonTulisQuery->get()->keyBy('siswa_id');

        $rankingData = [];
        
        $allSiswaQuery = Siswa::query();
        if ($kelasId) {
            $allSiswaQuery->where('kelas_id', $kelasId);
        }
        $allSiswaIds = $allSiswaQuery->pluck('id');

        foreach ($allSiswaIds as $siswaId) {
            $total = 0;
            if (isset($nilaiTulisKelas[$siswaId])) {
                foreach ($nilaiTulisKelas[$siswaId] as $nilai) {
                    $total += $nilai->nilai_uas;
                }
            }
            if (isset($nilaiNonTulisKelas[$siswaId])) {
                $qiroatul = is_numeric($nilaiNonTulisKelas[$siswaId]->qiroatul_kutub)
                    ? (int)$nilaiNonTulisKelas[$siswaId]->qiroatul_kutub : 0;

                $taftisyul = is_numeric($nilaiNonTulisKelas[$siswaId]->taftisyul_kutub)
                    ? (int)$nilaiNonTulisKelas[$siswaId]->taftisyul_kutub : 0;

                $total += $qiroatul + $taftisyul;
            }
            $rankingData[$siswaId] = $total;
        }

        arsort($rankingData);
        
        $topRankings = array_slice($rankingData, 0, $limit, true);
        
        $result = [];
        $rank = 1;
        foreach ($topRankings as $siswaId => $totalNilai) {
            $siswa = Siswa::find($siswaId);
            $result[] = [
                'rank' => $rank++,
                'siswa' => $siswa,
                'total_nilai' => $totalNilai,
                'kelas' => $siswa->kelas ? $siswa->kelas->nama_kelas : 'Tidak ada kelas'
            ];
        }
        
        return $result;
    }

    /**
     * Menghitung ranking per kelas - 3 terbaik per kelas dalam satu baris.
     * Diurutkan berdasarkan tingkat kustom (Isti'dadiyah, Tsanawiyah, Aliyah), lalu nama kelas A-Z.
     */
    private function calculateRankingByAngkatan($semester, $tahunAjaran)
    {
        // Ambil semua kelas yang ada
        $allKelas = Kelas::all();
        
        // Urutkan berdasarkan tingkat kustom, lalu nama kelas
        $tingkatOrder = [
            'Isti\'dadiyah' => 1,
            'Tsanawiyah' => 2,
            'Aliyah' => 3
        ];
        
        $sortedKelas = $allKelas->sortBy(function($kelas) use ($tingkatOrder) {
            $tingkatPriority = isset($tingkatOrder[$kelas->tingkat]) ? $tingkatOrder[$kelas->tingkat] : 999;
            return [$tingkatPriority, $kelas->nama_kelas];
        });
        
        $kelasRankings = [];
    
        foreach ($sortedKelas as $kelas) {
            // Ambil 3 ranking terbaik untuk kelas ini
            $topRankings = $this->calculateRanking($kelas->id, $semester, $tahunAjaran, 3);
            
            if (!empty($topRankings)) {
                // Siapkan data untuk setiap peringkat
                $peringkat1 = $peringkat2 = $peringkat3 = null;
                
                foreach ($topRankings as $ranking) {
                    switch($ranking['rank']) {
                        case 1:
                            $peringkat1 = $ranking;
                            break;
                        case 2:
                            $peringkat2 = $ranking;
                            break;
                        case 3:
                            $peringkat3 = $ranking;
                            break;
                    }
                }
                
                $kelasRankings[] = [
                    'kelas' => $kelas->nama_kelas,
                    'tingkat' => $kelas->tingkat, // Disimpan untuk grouping (tidak ditampilkan)
                    'peringkat1' => $peringkat1,
                    'peringkat2' => $peringkat2,
                    'peringkat3' => $peringkat3
                ];
            }
        }
    
        return $kelasRankings;
    }

    /**
     * Mengambil ranking untuk semua kelas (3 terbaik per kelas).
     */
    private function getAllKelasRankings($semester, $tahunAjaran)
    {
        $allKelas = Kelas::orderBy('nama_kelas')->get();
        $kelasRankings = [];

        foreach ($allKelas as $kelas) {
            $topRankings = $this->calculateRanking($kelas->id, $semester, $tahunAjaran, 3);
            
            if (!empty($topRankings)) {
                $kelasRankings[] = [
                    'kelas' => $kelas->nama_kelas,
                    'rankings' => $topRankings
                ];
            }
        }

        return $kelasRankings;
    }

    public function index()
    {
        $user = auth()->user();
        $tahunAjaran = TahunAjaran::where('status', true)->first();

        // --- Tentukan Salam Berdasarkan Waktu ---
        $hour = now()->format('H');
        $greeting = ($hour >= 5 && $hour < 10) ? 'Selamat Pagi' : (($hour >= 10 && $hour < 15) ? 'Selamat Siang' : (($hour >= 15 && $hour < 18) ? 'Selamat Sore' : 'Selamat Malam'));

        // --- CEK PERAN USER ---
        $isAdmin = $user->isAdmin();
        $isGuru = $user->isGuru();
        $isWali = $user->isWaliKelas();

        // --- INISIALISASI VARIABEL ---
        // Variabel untuk Admin
        $totalGuru = 0; $totalSiswa = 0; $totalKelas = 0; $totalMapel = 0;
        $chartLabels = []; $chartData = [];
        $kelasLabels = []; $kelasData = [];
        $allKelasRankings = []; // Variabel untuk ranking per kelas
        $angkatanRankings = []; // Variabel untuk ranking per angkatan
        
        // Variabel untuk Guru
        $guruMapelKelas = null;
        $guruProgressData = collect();
        
        // Variabel untuk Wali
        $kelas = null;
        $siswaCount = 0;
        $waliProgressData = collect();
        $topRankingsKelas = [];

        // --- AMBIL DATA BERDASARKAN PERAN ---

        if ($isAdmin) {
            // Data untuk Admin
            $totalGuru = User::whereHas('roles', function($query) { $query->where('name', 'guru'); })->count();
            $totalSiswa = Siswa::count();
            $totalKelas = Kelas::count();
            $totalMapel = Mapel::count();
    
            $mapelData = GuruMapelKelas::with('mapel', 'nilai')->get()->groupBy('mapel_id')->map(function ($items) {
                $mapelName = $items->first()->mapel->nama_mapel;
                $allNilai = $items->pluck('nilai')->flatten();
                $avgNilai = $allNilai->avg('nilai_uas');
                return ['nama' => $mapelName, 'avg' => $avgNilai ? round($avgNilai, 2) : 0];
            });
            $chartLabels = $mapelData->pluck('nama');
            $chartData = $mapelData->pluck('avg');
    
            $kelasDataCollection = Kelas::withCount('siswa')->get();
            $kelasLabels = $kelasDataCollection->pluck('nama_kelas');
            $kelasData = $kelasDataCollection->pluck('siswa_count');
            
            // TAMBAHAN: Ambil ranking per kelas dan per angkatan
            if ($tahunAjaran) {
                // PERUBAHAN: Yang ini menjadi "Ranking Per Kelas" (tabel gabungan)
                $allKelasRankings = $this->calculateRankingByAngkatan(
                    $tahunAjaran->semester,
                    $tahunAjaran->nama_tahun
                );
                
                // PERUBAHAN: Yang ini menjadi "Ranking Per Angkatan" (pemisahan per kelas)
                $angkatanRankings = $this->getAllKelasRankings(
                    $tahunAjaran->semester,
                    $tahunAjaran->nama_tahun
                );
            }
        }

        if ($isGuru) {
            $guruMapelKelas = $user->guruMapelKelas()->with('mapel', 'kelas')->get();
            
            if ($tahunAjaran) {
                $semester = $tahunAjaran->semester; 
                $namaTahunAjaran = $tahunAjaran->nama_tahun;

                foreach ($guruMapelKelas as $gmk) {
                    $totalSiswaDiKelas = Siswa::where('kelas_id', $gmk->kelas_id)->count();
                    $siswaSudahDinilai = Nilai::where('guru_mapel_kelas_id', $gmk->id)
                        ->where('semester', $semester)
                        ->where('tahun_ajaran', $namaTahunAjaran)
                        ->count();

                    $persentase = $totalSiswaDiKelas > 0 ? round(($siswaSudahDinilai / $totalSiswaDiKelas) * 100, 1) : 0;

                    $guruProgressData->push([
                        'mapel' => $gmk->mapel->nama_mapel,
                        'kelas' => $gmk->kelas->nama_kelas,
                        'persentase' => $persentase,
                        'route' => route('guru.nilai.index', $gmk->id)
                    ]);
                }
            }
        }

        if ($isWali) {
            $kelas = $user->kelasWali;
            if ($kelas) {
                $siswaCount = Siswa::where('kelas_id', $kelas->id)->count();
                
                $waliData = $this->getWaliData($user);

                if (!$waliData) {
                    $waliProgressData = collect();
                } else {
                    // Ambil ranking untuk kelas wali
                    $topRankingsKelas = $this->calculateRanking(
                        $kelas->id,
                        $waliData['semester'],
                        $waliData['tahunAjaran']->nama_tahun,
                        3
                    );
                    
                    $siswaIds = Siswa::where('kelas_id', $kelas->id)->pluck('id');

                    $tugasWali = [
                        ['model' => NilaiNonTulis::class, 'name' => 'Input Nilai Non Tulis', 'route' => route('wali.nilai_non_tulis.index')],
                        ['model' => PenilaianKarakter::class, 'name' => 'Input Penilaian Karakter', 'route' => route('wali.karakter.index')],
                        ['model' => Absensi::class, 'name' => 'Input Rekap Absensi', 'route' => route('wali.absensi.index')],
                        ['model' => CatatanRapor::class, 'name' => 'Input Catatan Rapor', 'route' => route('rapor.index')],
                    ];

                    foreach ($tugasWali as $tugas) {
                        $model = $tugas['model'];
                        $totalKomponen = 0;
                        $komponenSelesai = 0;

                        if ($model === NilaiNonTulis::class) {
                            $totalKomponen = $siswaIds->count() * 3;
                            $records = $model::whereIn('siswa_id', $siswaIds)
                                ->where('semester', $waliData['semester'])
                                ->where('tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
                                ->get();
                            foreach ($records as $record) {
                                if (!is_null($record->muhafadzhoh)) $komponenSelesai++;
                                if (!is_null($record->qiroatul_kutub)) $komponenSelesai++;
                                if (!is_null($record->taftisyul_kutub)) $komponenSelesai++;
                            }
                        } elseif ($model === PenilaianKarakter::class) {
                            $totalKomponen = $siswaIds->count() * 3;
                            $records = $model::whereIn('siswa_id', $siswaIds)
                                ->where('semester', $waliData['semester'])
                                ->where('tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
                                ->get();
                            foreach ($records as $record) {
                                if (!is_null($record->kelakuan)) $komponenSelesai++;
                                if (!is_null($record->kerajinan)) $komponenSelesai++;
                                if (!is_null($record->kerapihan)) $komponenSelesai++;
                            }
                        } elseif ($model === Absensi::class) {
                            $totalKomponen = $siswaIds->count() * 3;
                            $records = $model::whereIn('siswa_id', $siswaIds)
                                ->where('semester', $waliData['semester'])
                                ->where('tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
                                ->get();
                            foreach ($records as $record) {
                                if (!is_null($record->sakit)) $komponenSelesai++;
                                if (!is_null($record->izin)) $komponenSelesai++;
                                if (!is_null($record->tanpa_keterangan)) $komponenSelesai++;
                            }
                        } elseif ($model === CatatanRapor::class) {
                            $totalKomponen = $siswaIds->count();
                            $records = $model::whereIn('siswa_id', $siswaIds)
                                ->where('semester', $waliData['semester'])
                                ->where('tahun_ajaran', $waliData['tahunAjaran']->nama_tahun)
                                ->get();
                            $komponenSelesai = 0;
                            foreach ($records as $record) {
                                if (!is_null($record->catatan)) {
                                    $komponenSelesai++;
                                }
                            }
                        }
                        
                        $persentase = $totalKomponen > 0 ? round(($komponenSelesai / $totalKomponen) * 100, 1) : 0;
                        
                        $waliProgressData->push([
                            'name' => $tugas['name'],
                            'persentase' => $persentase,
                            'route' => $tugas['route']
                        ]);
                    }
                }
            }
        }

        // --- SIAPKAN TEKS UNTUK DITAMPILKAN ---
        $roleText = 'User';
        if ($isAdmin) {
            $roleText = 'Admin';
        } elseif ($isGuru && $isWali) {
            $roleText = 'Ustadz/ah dan Mustahiq';
        } elseif ($isGuru) {
            $roleText = 'Ustadz/ah';
        } elseif ($isWali) {
            $roleText = 'Mustahiq';
        }

        return view('dashboard.unified', compact(
            'tahunAjaran',
            'isAdmin',
            'isGuru',
            'isWali',
            'roleText',
            'greeting',
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'totalMapel',
            'chartLabels',
            'chartData',
            'kelasLabels',
            'kelasData',
            'guruMapelKelas',
            'kelas',
            'siswaCount',
            'guruProgressData',
            'waliProgressData',
            'allKelasRankings', // TAMBAHAN: Data ranking per kelas untuk admin
            'angkatanRankings', // TAMBAHAN: Data ranking per angkatan untuk admin
            'topRankingsKelas' // TAMBAHAN: Data ranking untuk wali kelas
        ));
    }
}