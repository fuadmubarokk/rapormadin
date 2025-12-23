@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb', 'Dashboard')

@push('styles')
    <!-- Impor Font Modern dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        /* Gaya untuk jam modern */
        .modern-clock {
            font-family: 'Roboto Mono', monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            background-color: #343a40;
            padding: 5px 12px;
            border-radius: 20px;
            letter-spacing: 2px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.3s ease-in-out;
            margin-left: auto;
            margin-right: 15px;
        }

        .modern-clock:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .modern-clock::after {
            content: ' WIB';
            font-size: 0.8rem;
            font-weight: 400;
            margin-left: 5px;
            opacity: 0.8;
        }
        
        /* Style untuk ranking badge */
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-weight: bold;
            color: white;
        }
        
        .rank-1 {
            background-color: #FFD700;
            color: #333;
        }
        
        .rank-2 {
            background-color: #C0C0C0;
            color: #333;
        }
        
        .rank-3 {
            background-color: #CD7F32;
            color: #fff;
        }
        
        /* Style tambahan untuk tabel ranking */
        .ranking-table {
            margin-bottom: 20px;
        }
        
        .ranking-table .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        
        .kelas-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        /* Style tambahan untuk tabel ranking compact */
        .ranking-compact td {
            vertical-align: top;
        }
        
        .ranking-compact .rank-badge {
            font-size: 0.8rem;
            width: 25px;
            height: 25px;
        }
        
        .ranking-compact small {
            font-size: 0.75rem;
        }

/* Center content in ranking cells */
.ranking-cell {
    text-align: center;
}

.ranking-cell .rank-badge {
    margin-bottom: 5px;
}

.ranking-cell small {
    display: block;
    margin-top: 2px;
}
    </style>
@endpush

@section('content')
{{-- ================== ISI DASHBOARD ADMIN ================== --}}
@if($isAdmin)
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{ $totalGuru }}</h3><p>Total Ustadz/ah</p></div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('admin.guru.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{ $totalSiswa }}</h3><p>Total Santri</p></div>
                <div class="icon"><i class="fas fa-user-graduate"></i></div>
                <a href="{{ route('admin.siswa.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-warning">
                <div class="inner"><h3>{{ $totalKelas }}</h3><p>Total Kelas</p></div>
                <div class="icon"><i class="fas fa-school"></i></div>
                <a href="{{ route('admin.kelas.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-danger">
                <div class="inner"><h3>{{ $totalMapel }}</h3><p>Total Mata Pelajaran</p></div>
                <div class="icon"><i class="fas fa-book"></i></div>
                <a href="{{ route('admin.mapel.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center">
                    <h3 class="card-title mb-2 mb-md-0">{{ $greeting }}!</h3>
                    <div class="modern-clock ml-md-auto" id="live-clock">
                        <!-- Jam akan dimunculkan di sini oleh JavaScript -->
                    </div>
                </div>
                <div class="card-body">
                    <p>Anda login sebagai <strong>{{ auth()->user()->name }}</strong> di Sistem Rapor Madrasah Diniyah Online.</p>
                    <p>Silakan gunakan menu di sebelah kiri untuk mengelola data ustadz/ah, santri, kelas, mapel, dan lainnya.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ranking Per Kelas (Admin) - Satu Baris Per Kelas -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy text-warning mr-2"></i>
                        Peringkat 3 Terbaik Per Kelas
                    </h3>
                </div>
                <div class="card-body">
                    @if(!empty($allKelasRankings))
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th width="15%">Kelas</th>
                                        <th class="text-center" width="25%">Peringkat 1</th>
                                        <th class="text-center" width="25%">Peringkat 2</th>
                                        <th class="text-center" width="25%">Peringkat 3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allKelasRankings as $index => $kelasRanking)
                                        <tr>
                                            <td class="text-center text-nowrap">{{ $index + 1 }}</td>
                                            <td class="font-weight-bold text-nowrap">{{ $kelasRanking['kelas'] }}</td>
                                            
                                            <td class="text-nowrap">
                                                @if($kelasRanking['peringkat1'])
                                                    <div>{{ $kelasRanking['peringkat1']['siswa']->nama }}</div>
                                                    <small class="text-muted">Total Nilai: {{ $kelasRanking['peringkat1']['total_nilai'] }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            
                                            <td class="text-nowrap">
                                                @if($kelasRanking['peringkat2'])
                                                    <div>{{ $kelasRanking['peringkat2']['siswa']->nama }}</div>
                                                    <small class="text-muted">Total Nilai: {{ $kelasRanking['peringkat2']['total_nilai'] }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            
                                            <td class="text-nowrap">
                                                @if($kelasRanking['peringkat3'])
                                                    <div>{{ $kelasRanking['peringkat3']['siswa']->nama }}</div>
                                                    <small class="text-muted">Total Nilai: {{ $kelasRanking['peringkat3']['total_nilai'] }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Belum ada data ranking tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Grafik Rata-Rata Nilai per Mapel</h3></div>
                <div class="card-body"><canvas id="nilaiChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Grafik Jumlah Santri per Kelas</h3></div>
                <div class="card-body"><canvas id="kelasChart"></canvas></div>
            </div>
        </div>
    </div>

@else
    {{-- ================== ISI DASHBOARD GURU & WALI (JIKA BUKAN ADMIN) ================== --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center">
                    <h3 class="card-title mb-2 mb-md-0">{{ $greeting }}!</h3>
                    <div class="modern-clock ml-md-auto" id="live-clock">
                        <!-- Jam akan dimunculkan di sini oleh JavaScript -->
                    </div>
                </div>
                <div class="card-body">
                    <p>Anda login sebagai <strong>{{ auth()->user()->name }}</strong> di Sistem Rapor Madrasah Diniyah.</p>
                    <p>Tahun Ajaran Aktif: <strong>{{ $tahunAjaran ? $tahunAjaran->nama_tahun : 'Belum ditetapkan' }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Ranking Siswa Terbaik (Wali Kelas) - Posisi setelah card login --}}
    @if($isWali && !empty($topRankingsKelas))
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy text-warning mr-2"></i>
                        Ranking 3 Terbaik - {{ $kelas->nama_kelas }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" width="10%">Ranking</th>
                                    <th width="55%">Nama Siswa</th>
                                    <th class="text-center" width="20%">Total Nilai</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topRankingsKelas as $ranking)
                                    <tr>
                                        <td class="text-center">
                                            <span class="rank-badge rank-{{ $ranking['rank'] }}">
                                                {{ $ranking['rank'] }}
                                            </span>
                                        </td>
                                        <td>{{ $ranking['siswa']->nama }}</td>
                                        <td class="text-center font-weight-bold">{{ $ranking['total_nilai'] }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('rapor.show', $ranking['siswa']->id) }}" class="btn btn-sm btn-info" title="Lihat Rapor">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TAMPILKAN PROGRES GURU --}}
    @if($isGuru && $guruProgressData->isNotEmpty())
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progres Tugas Guru</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">No</th>
                                    <th class="text-nowrap">Mata Pelajaran</th>
                                    <th class="text-nowrap">Kelas</th>
                                    <th class="text-nowrap">Progress</th>
                                    <th class="text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guruProgressData as $index => $progress)
                                <tr>
                                    <td class="text-center text-nowrap">{{ $index + 1 }}</td>
                                    <td class="text-nowrap">{{ $progress['mapel'] }}</td>
                                    <td class="text-nowrap">{{ $progress['kelas'] }}</td>
                                    <td class="text-nowrap">
                                        <div class="progress" style="height: 22px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $progress['persentase'] > 0 ? $progress['persentase'] . '%' : '35px' }}; background-color: {{ $progress['persentase'] > 0 ? '#28a745' : '#dc3545' }}; color: white;" 
                                                 aria-valuenow="{{ $progress['persentase'] }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $progress['persentase'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ $progress['route'] }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TAMPILKAN PROGRES WALI KELAS --}}
    @if($isWali && $waliProgressData->isNotEmpty())
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progres Tugas Wali Kelas</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">No</th>
                                    <th class="text-nowrap">Tugas</th>
                                    <th class="text-nowrap">Progress</th>
                                    <th class="text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($waliProgressData as $index => $progress)
                                <tr>
                                    <td class="text-center text-nowrap">{{ $index + 1 }}</td>
                                    <td class="text-nowrap">{{ $progress['name'] }}</td>
                                    <td class="text-nowrap">
                                        <div class="progress" style="height: 22px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $progress['persentase'] > 0 ? $progress['persentase'] . '%' : '35px' }}; background-color: {{ $progress['persentase'] > 0 ? '#28a745' : '#dc3545' }}; color: white;" 
                                                 aria-valuenow="{{ $progress['persentase'] }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $progress['persentase'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ $progress['route'] }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif($isWali)
    {{-- TAMPILKAN PESAN JIKA WALI KELAS BELUM DITETAPKAN --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card"><div class="card-body"><p>Anda belum ditetapkan sebagai Mustahiq (Wali Kelas).</p></div></div>
        </div>
    </div>
    @endif
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($isAdmin)
            // Grafik Nilai per Mapel
            const ctxNilai = document.getElementById('nilaiChart');
            if (ctxNilai) {
                new Chart(ctxNilai.getContext('2d'), {
                    type: 'bar', data: {
                        labels: @json($chartLabels),
                        datasets: [{ label: 'Rata-rata Nilai', data: @json($chartData), backgroundColor: 'rgba(54, 162, 235, 0.2)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }]
                    }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100, title: { display: true, text: 'Rata-rata Nilai' } }, x: { title: { display: true, text: 'Mata Pelajaran' } } }, plugins: { legend: { display: false } } }
                });
            }
            // Grafik Jumlah Siswa per Kelas
            const ctxKelas = document.getElementById('kelasChart');
            if (ctxKelas) {
                new Chart(ctxKelas.getContext('2d'), {
                    type: 'pie', data: {
                        labels: @json($kelasLabels),
                        datasets: [{ data: @json($kelasData), backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'], borderWidth: 1 }]
                    }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
                });
            }
        @endif
    });

    function updateClock() {
        const now = new Date();
        
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();

        hours = hours.toString().padStart(2, '0');
        minutes = minutes.toString().padStart(2, '0');
        seconds = seconds.toString().padStart(2, '0');

        const timeString = `${hours}.${minutes}.${seconds}`;

        const clockElement = document.getElementById('live-clock');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }

    setInterval(updateClock, 1000);
    document.addEventListener('DOMContentLoaded', updateClock);
</script>
@endpush