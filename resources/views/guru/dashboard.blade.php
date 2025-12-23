@extends('layouts.app')

@section('title', 'Dashboard Ustadz/ah')

@section('breadcrumb', 'Dashboard Ustadz/ah')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Selamat Datang, {{ auth()->user()->name }}!</h3>
            </div>
            <div class="card-body">
                <p>Anda login sebagai <strong>Ustadz/ah</strong> di Sistem Rapor Madrasah Diniyah.</p>
                <p>Tahun Ajaran Aktif: <strong>{{ $tahunAjaran ? $tahunAjaran->nama_tahun : 'Belum ditetapkan' }}</strong></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Mata Pelajaran yang Diampu</h3>
            </div>
            <div class="card-body">
                @if($guruMapelKelas->count() > 0)
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guruMapelKelas as $index => $gmk)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $gmk->mapel->nama_mapel }}</td>
                            <td>{{ $gmk->kelas->nama_kelas }}</td>
                            <td>
                                <a href="{{ route('guru.nilai.index', $gmk->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-pencil-alt"></i> Input Nilai
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>Anda belum ditugaskan untuk mengajar mata pelajaran apa pun.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection