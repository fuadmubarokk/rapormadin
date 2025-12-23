@extends('layouts.app')

@section('title', 'Data Ustadz/Ustadzah')

@section('breadcrumb', 'Data Ustadz/Ustadzah')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title mb-2 mb-md-0">Data Ustadz/Ustadzah</h3>

                <div class="card-tools">
                    <a href="{{ route('admin.guru.template') }}" class="btn btn-default btn-sm mb-1">
                        <i class="fas fa-file-download"></i> Template
                    </a>
                    <button type="button" class="btn btn-info btn-sm mb-1" data-toggle="modal" data-target="#modalImport">
                        <i class="fas fa-file-upload"></i> Import
                    </button>
                    <a href="{{ route('admin.guru.create') }}" class="btn btn-primary btn-sm mb-1">
                        <i class="fas fa-plus"></i> Tambah Ustadz/Ustadzah
                    </a>
                </div>
            </div>

            <div class="card-body">

                {{-- Responsive container --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="text-center">
                            <tr>
                                <th style="width: 40px;">No.</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th style="width: 60px;">Ttd</th>
                                <th>Role</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($guru as $g)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $g->name }}</td>
                                <td>{{ $g->email }}</td>

                                <td class="text-center">
                                    <img src="{{ !empty($g->ttd_wali_kelas) 
                                        ? asset('img/ttd/' . $g->ttd_wali_kelas) 
                                        : asset('img/logo.png') }}"
                                        style="max-height: 40px; width: auto; border-radius: 5px;" 
                                        alt="TTD/Logo Guru">
                                </td>

                                <td>
                                    @foreach($g->roles as $role)
                                        <span class="badge badge-{{ 
                                            $role->name == 'admin' ? 'danger' : 
                                            ($role->name == 'wali' ? 'warning' : 'info') 
                                        }}">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </td>

                                <td class="text-center">

                                    <a href="{{ route('admin.guru.edit', $g->id) }}"
                                        class="btn btn-warning btn-sm mb-1">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.guru.destroy', $g->id) }}" 
                                        method="POST" 
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="btn btn-danger btn-sm mb-1"
                                            onclick="return confirm('Hapus data guru ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                {{-- end responsive --}}

            </div>
        </div>

    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="modalImport">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Import Data Guru</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('admin.guru.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label for="file">Pilih File Excel</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" 
                                       name="file" 
                                       class="custom-file-input" 
                                       id="file" 
                                       required>
                                <label class="custom-file-label" for="file">Pilih file...</label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
