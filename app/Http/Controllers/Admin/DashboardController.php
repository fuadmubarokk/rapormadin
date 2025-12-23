<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
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
}