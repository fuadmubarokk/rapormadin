@extends('layouts.app')

@section('title', 'Data Kelas')

@section('breadcrumb', 'Data Kelas')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Kelas</h3>
            </div>

            <div class="card-body">

                {{-- FORM TAMBAH DATA --}}
                <form action="{{ route('admin.kelas.store') }}" method="post" class="mb-3">
                    @csrf
                    <div class="row g-2">

                        <div class="col-lg-4 col-md-6 col-12">
                            <input type="text" name="nama_kelas" class="form-control" placeholder="Nama Kelas" required>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <input type="text" name="tingkat" class="form-control" placeholder="Tingkat" required>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <select name="wali_id" class="form-control">
                                <option value="">-- Pilih Wali --</option>
                                @foreach($wali as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6 col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                Tambah
                            </button>
                        </div>

                    </div>
                </form>

                {{-- TABEL RESPONSIF --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Wali Kelas</th>
                                <th width="90">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas as $k)
                            <tr>
                                <td>{{ $k->nama_kelas }}</td>
                                <td>{{ $k->tingkat }}</td>
                                <td>{{ $k->waliKelas ? $k->waliKelas->name : '-' }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-toggle="modal" data-target="#modalEdit{{ $k->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('admin.kelas.destroy', $k->id) }}"
                                          method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Hapus data kelas ini?')">
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
</div>

{{-- MODAL EDIT --}}
@foreach($kelas as $k)
<div class="modal fade" id="modalEdit{{ $k->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.kelas.update', $k->id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h4 class="modal-title">Edit Kelas</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas"
                               class="form-control"
                               value="{{ $k->nama_kelas }}" required>
                    </div>

                    <div class="form-group">
                        <label>Tingkat</label>
                        <input type="text" name="tingkat"
                               class="form-control"
                               value="{{ $k->tingkat }}" required>
                    </div>

                    <div class="form-group">
                        <label>Wali Kelas</label>
                        <select name="wali_id" class="form-control">
                            <option value="">-- Pilih Wali --</option>
                            @foreach($wali as $w)
                            <option value="{{ $w->id }}"
                                {{ $k->wali_id == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                            @endforeach
                        </select>
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
