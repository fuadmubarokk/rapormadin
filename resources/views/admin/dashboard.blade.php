@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('breadcrumb', 'Dashboard Admin')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalGuru }}</h3>
                <p>Total Guru</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.guru.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalSiswa }}</h3>
                <p>Total Santri</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <a href="{{ route('admin.siswa.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalKelas }}</h3>
                <p>Total Kelas</p>
            </div>
            <div class="icon">
                <i class="fas fa-school"></i>
            </div>
            <a href="{{ route('admin.kelas.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalMapel }}</h3>
                <p>Total Mapel</p>
            </div>
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
            <a href="{{ route('admin.mapel.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Selamat Datang, {{ auth()->user()->name }}!</h3>
            </div>
            <div class="card-body">
                <p>Anda login sebagai <strong>Admin</strong> di Sistem Rapor Madrasah Diniyah Online.</p>
                <p>Silakan gunakan menu di sebelah kiri untuk mengelola data guru, santri, kelas, mapel, dan lainnya.</p>
            </div>
        </div>
    </div>
</div>

<!-- TAMBAHKAN GRAFIK DI SINI -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Rata-Rata Nilai per Mapel (Seluruh Madrasah)</h3>
            </div>
            <div class="card-body">
                <canvas id="nilaiChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Rata-Rata Nilai per Mapel</h3>
            </div>
            <div class="card-body">
                <canvas id="nilaiChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Jumlah Santri per Kelas</h3>
            </div>
            <div class="card-body">
                <canvas id="kelasChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Grafik Nilai per Mapel
        const ctxNilai = document.getElementById('nilaiChart');
        if (ctxNilai) {
            new Chart(ctxNilai.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Rata-rata Nilai',
                        data: @json($chartData),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Rata-rata Nilai'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mata Pelajaran'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toFixed(2);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Grafik Jumlah Siswa per Kelas
        const ctxKelas = document.getElementById('kelasChart');
        if (ctxKelas) {
            new Chart(ctxKelas.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: @json($kelasLabels),
                    datasets: [{
                        data: @json($kelasData),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed + ' siswa';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection

<!-- TAMBAHKAN SCRIPT DI BAWAH -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Script akan kita ubah menjadi dinamis
</script>
@endpush
