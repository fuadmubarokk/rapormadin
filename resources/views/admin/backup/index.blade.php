@extends('layouts.app')

@section('title', 'Backup Database')

@section('breadcrumb', 'Backup Database')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Backup Database</h3>
                <div class="card-tools">
                    <form action="{{ route('backup.create') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Buat Backup Baru
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($files->count() > 0)
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Ukuran</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                        <tr>
                            <td>{{ $file->getFilename() }}</td>
                            <td>{{ number_format($file->getSize() / 1048576, 2) }} MB</td>
                            <td>{{ date('d-m-Y H:i:s', $file->getMTime()) }}</td>
                            <td>
                                <a href="{{ route('backup.download', $file->getFilename()) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <form action="{{ route('backup.delete', $file->getFilename()) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus file backup ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>Belum ada file backup. Klik tombol "Buat Backup Baru" untuk membuat backup database.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection