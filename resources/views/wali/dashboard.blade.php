@extends('layouts.app')

@section('title', 'Dashboard Mustahiq')

@section('breadcrumb', 'Dashboard Mustahiq')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Selamat Datang, {{ auth()->user()->name }}!</h3>
            </div>
            <div class="card-body">
                <p>Anda login sebagai <strong>Mustahiq</strong> di Sistem Rapor Madrasah Diniyah.</p>
                <p>Tahun Ajaran Aktif: <strong>{{ $tahunAjaran ? $tahunAjaran->nama_tahun : 'Belum ditetapkan' }}</strong></p>
            </div>
        </div>
    </div>
</div>

@if($kelas)
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Kelas</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="150">Nama Kelas</th>
                        <td>{{ $kelas->nama_kelas }}</td>
                    </tr>
                    <tr>
                        <th>Tingkat</th>
                        <td>{{ $kelas->tingkat }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Santri</th>
                        <td>{{ $siswa }} Santri</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Nilai</h3>
                <p>Non Tulis</p>
            </div>
            <div class="icon">
                <i class="fas fa-book-open"></i>
            </div>
            <a href="{{ route('wali.nilai_non_tulis.index') }}" class="small-box-footer">
                Input <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-md-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Karakter</h3>
                <p>Penilaian</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
            <a href="{{ route('wali.karakter.index') }}" class="small-box-footer">
                Input <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-md-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Absensi</h3>
                <p>Rekap</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <a href="{{ route('wali.absensi.index') }}" class="small-box-footer">
                Input <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-md-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Rapor</h3>
                <p>Cetak</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <a href="{{ route('rapor.index') }}" class="small-box-footer">
                Lihat <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
</div>
@else
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <p>Anda belum ditetapkan sebagai Mustahiq.</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection