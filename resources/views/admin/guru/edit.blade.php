@extends('layouts.app')

@section('title', 'Edit Ustadz/Ustadzah')

@section('breadcrumb', 'Edit Ustadz/Ustadzah')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Edit Ustadz/Ustadzah</h3>
            </div>
            <form action="{{ route('admin.guru.update', $guru->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $guru->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $guru->email) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password <small>(Kosongkan jika tidak ingin mengubah)</small></label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    <div class="form-group">
                        <label>Pilih Role (bisa lebih dari satu)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="guru" id="roleGuru" {{ (old('roles') && in_array('guru', old('roles'))) || $guru->roles->contains('name', 'guru') ? 'checked' : '' }}>
                            <label class="form-check-label" for="roleGuru">
                                Guru
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="wali" id="roleWali" {{ (old('roles') && in_array('wali', old('roles'))) || $guru->roles->contains('name', 'wali') ? 'checked' : '' }}>
                            <label class="form-check-label" for="roleWali">
                                Wali Kelas
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="foto" class="custom-file-input" id="foto">
                                <label class="custom-file-label" for="foto">Pilih file...</label>
                            </div>
                        </div>
                        {{-- PERUBAHAN 1: Path Foto Guru --}}
                        @if($guru->foto)
                        <small class="form-text text-muted">Foto saat ini: <img src="{{ asset('img/foto_guru/' . $guru->foto) }}" width="50"></small>
                        @endif
                    </div>
                    
                    {{-- BAGIAN UNTUK UPLOAD TTD WALI KELAS --}}
                    <div class="form-group">
                        <label for="ttd_wali_kelas">Tanda Tangan Wali Kelas</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="ttd_wali_kelas" class="custom-file-input" id="ttd_wali_kelas" accept="image/*">
                                <label class="custom-file-label" for="ttd_wali_kelas">Pilih file tanda tangan...</label>
                            </div>
                        </div>
                        
                        {{-- Tampilkan tanda tangan jika sudah ada --}}
                        @if($guru->ttd_wali_kelas)
                            <small class="form-text text-muted">Tanda tangan saat ini:</small>
                            <img src="{{ asset('img/ttd/' . $guru->ttd_wali_kelas) }}" alt="Tanda Tangan Wali Kelas" style="max-height: 80px; border: 1px solid #ddd; padding: 5px; margin-top: 5px;">
                        @endif
                    
                        <div class="mt-3 p-3 rounded" style="background-color: #eef2f7; border-left: 4px solid #007bff;">
                            <strong>📌 Petunjuk Upload Tanda Tangan:</strong>
                            <ul class="mb-0 mt-2 pl-4" style="font-size: 0.9em; line-height: 1.6;">
                                <li><strong>Format:</strong> Gunakan gambar dengan format <strong>.JPG</strong> atau <strong>.PNG</strong>.</li>
                                <li><strong>Background:</strong> Pastikan background gambar <strong>putih</strong> atau <strong>transparan</strong> agar menyatu dengan dokumen.</li>
                                <li><strong>Kualitas:</strong> Upload gambar yang <strong>jelas</strong> dan tidak blur.</li>
                                <li><strong>Ukuran Ideal:</strong> Lebar sekitar <strong>1200 pixel</strong> dan tinggi sekitar <strong>500 pixel</strong>.</li>
                            </ul>
                        </div>
                        {{-- ==================== AKHIR BAGIAN YANG DITAMBAHKAN ==================== --}}
                    </div>
                <div class="card-footer">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection