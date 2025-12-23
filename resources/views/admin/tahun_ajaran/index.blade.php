@extends('layouts.app')

@section('title', 'Data Tahun Ajaran')
@section('breadcrumb', 'Data Tahun Ajaran')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Tahun Ajaran</h3>
                <div class="card-tools">
                    <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahTahunAjaran">
                        <i class="fas fa-plus"></i> Tambah
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Tahun Ajaran</th>
                            <th width="15%">Semester</th>
                            <th width="15%">Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tahunAjaranList as $index => $ta)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ta->nama_tahun }}</td>
                            <td>{{ $ta->semester }}</td>
                            <td>
                                @if($ta->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                {{-- Tombol Edit selalu muncul --}}
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit{{ $ta->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                {{-- Tombol Aktifkan & Hapus hanya muncul jika tidak aktif --}}
                                @if(!$ta->is_active)
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalAktifkan{{ $ta->id }}">
                                        <i class="fas fa-check"></i> Aktifkan
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalHapus{{ $ta->id }}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- MODAL AKTIFKAN, EDIT, dan HAPUS --}}
                @foreach($tahunAjaranList as $ta)
                    {{-- MODAL AKTIFKAN --}}
                    <div class="modal fade" id="modalAktifkan{{ $ta->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.tahun_ajaran.aktifkan', $ta->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Aktifkan Periode Ajaran</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Anda akan mengaktifkan periode berikut:</p>
                                        <table class="table table-sm">
                                            <tr><td width="30%">Tahun Ajaran</td><td><strong>{{ $ta->nama_tahun }}</strong></td></tr>
                                            <tr>
                                                <td>Semester</td>
                                                <td>
                                                    <select name="semester" class="form-control">
                                                        <option value="Ganjil" {{ $ta->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                                        <option value="Genap" {{ $ta->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta->id }}">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Ya, Aktifkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEdit{{ $ta->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.tahun_ajaran.update', $ta->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Tahun Ajaran</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="nama_tahun_{{ $ta->id }}">Nama Tahun Ajaran</label>
                                            <input type="text" name="nama_tahun" class="form-control" id="nama_tahun_{{ $ta->id }}" value="{{ $ta->nama_tahun }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="semester_{{ $ta->id }}">Semester</label>
                                            <select name="semester" id="semester_{{ $ta->id }}" class="form-control" required>
                                                <option value="Ganjil" {{ $ta->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                                <option value="Genap" {{ $ta->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL HAPUS --}}
                    <div class="modal fade" id="modalHapus{{ $ta->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.tahun_ajaran.destroy', $ta->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus tahun ajaran <strong>{{ $ta->nama_tahun }}</strong>?</p>
                                        <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahTahunAjaran" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tahun_ajaran.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tahun Ajaran Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_tahun">Nama Tahun Ajaran</label>
                        <input type="text" name="nama_tahun" class="form-control" id="nama_tahun" value="{{ old('nama_tahun') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select name="semester" id="semester" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
