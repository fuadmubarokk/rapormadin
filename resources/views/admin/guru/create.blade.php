@extends('layouts.app')

@section('title', 'Tambah Ustadz/Ustadzah')

@section('breadcrumb', 'Tambah Ustadz/Ustadzah')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Ustadz/Ustadzah</h3>
            </div>
            <form action="{{ route('admin.guru.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password (minimal 8 karakter)</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="form-group">
                        <label>Pilih Role (bisa lebih dari satu)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="guru" id="roleGuru" {{ old('roles') && in_array('guru', old('roles')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="roleGuru">
                                Guru
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="wali" id="roleWali" {{ old('roles') && in_array('wali', old('roles')) ? 'checked' : '' }}>
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
                    </div>
                    
                    {{-- ==================== TAMBAHKAN BAGIAN INI ==================== --}}
                    {{-- BAGIAN UNTUK UPLOAD TTD WALI KELAS --}}
                    <div class="form-group">
                        <label for="ttd_wali_kelas">Tanda Tangan Wali Kelas</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="ttd_wali_kelas" class="custom-file-input" id="ttd_wali_kelas" accept="image/*">
                                <label class="custom-file-label" for="ttd_wali_kelas">Pilih file tanda tangan...</label>
                            </div>
                        </div>
                        
                        {{-- Bagian petunjuk upload --}}
                        <div class="mt-3 p-3 rounded" style="background-color: #eef2f7; border-left: 4px solid #007bff;">
                            <strong>📌 Petunjuk Upload Agar Tanda Tangan Pas:</strong>
                            <ul class="mb-0 mt-2 pl-4" style="font-size: 0.9em; line-height: 1.6;">
                                <li><strong>Format:</strong> Gunakan gambar dengan format <strong>.JPG</strong> atau <strong>.PNG</strong>.</li>
                                <li><strong>Background:</strong> Pastikan background gambar <strong>putih</strong> atau <strong>transparan</strong> agar menyatu dengan dokumen.</li>
                                <li><strong>Kualitas:</strong> Upload gambar yang <strong>jelas</strong> dan tidak blur.</li>
                                <li><strong>Ukuran Ideal:</strong> Lebar sekitar <strong>1200 pixel</strong> dan tinggi sekitar <strong>500 pixel</strong>.</li>
                            </ul>
                        </div>
                    </div>
                    {{-- ==================== AKHIR BAGIAN YANG DITAMBAHKAN ==================== --}}
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection