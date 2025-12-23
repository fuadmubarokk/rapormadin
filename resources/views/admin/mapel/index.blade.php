@extends('layouts.app')

@section('title', 'Data Mata Pelajaran')

@section('breadcrumb', 'Data Mata Pelajaran')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- ==================== TAMBAHKAN KODE INI ==================== -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sort-numeric-down mr-2"></i>Pengaturan Urutan Mapel per Angkatan</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Pilih angkatan untuk mengatur urutan mata pelajaran yang akan muncul di rapor.</p>
                <div class="list-group">
                    @forelse ($angkatans as $angkatan)
                        <a href="{{ route('admin.mapel.urutan', $angkatan->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-gear-fill mr-2"></i> Atur Urutan Mapel: <strong>{{ $angkatan->nama_angkatan }}</strong>
                            </span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Belum ada data angkatan. Silakan tambahkan terlebih dahulu melalui database atau fitur import.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- ==================== AKHIR KODE TAMBAHAN ==================== -->

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Mata Pelajaran</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama Mapel (ID)</th>
                            <th>Nama Mapel (AR)</th>
                            <th>Kategori</th>
                            <th>KKM</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form action="{{ route('admin.mapel.store') }}" method="post">
                            @csrf
                            <tr>
                                <td><input type="text" name="nama_mapel" class="form-control" placeholder="Nama Mapel" required></td>
                                <td><input type="text" name="nama_mapel_ar" class="form-control" placeholder="اسم المادة" required></td>
                                <td>
                                    <select name="kategori" class="form-control" required>
                                        <option value="tulis">Tulis</option>
                                        <option value="non-tulis">Non-Tulis</option>
                                    </select>
                                </td>
                                <td><input type="number" name="kkm" class="form-control" placeholder="KKM" min="0" max="100" required></td>
                                <td><button type="submit" class="btn btn-primary btn-sm">Tambah</button></td>
                            </tr>
                        </form>
                        @foreach($mapel as $m)
                        <tr>
                            <td>{{ $m->nama_mapel }}</td>
                            <td class="font-arab">{{ $m->nama_mapel_ar }}</td>
                            <td><span class="badge badge-info">{{ $m->kategori }}</span></td>
                            <td>{{ $m->kkm }}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit{{ $m->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.mapel.destroy', $m->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data mapel ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals Edit -->
@foreach($mapel as $m)
<div class="modal fade" id="modalEdit{{ $m->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.mapel.update', $m->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">Edit Mapel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Mapel</label>
                        <input type="text" name="nama_mapel" class="form-control" value="{{ $m->nama_mapel }}" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Mapel (AR)</label>
                        <input type="text" name="nama_mapel_ar" class="form-control" value="{{ $m->nama_mapel_ar }}" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                            <option value="tulis" {{ $m->kategori == 'tulis' ? 'selected' : '' }}>Tulis</option>
                            <option value="non-tulis" {{ $m->kategori == 'non-tulis' ? 'selected' : '' }}>Non-Tulis</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>KKM</label>
                        <input type="number" name="kkm" class="form-control" value="{{ $m->kkm }}" min="0" max="100" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection