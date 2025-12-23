@extends('layouts.app')

@section('title', 'Edit Data Santri')

@section('breadcrumb', 'Edit Data Santri')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Edit Data Santri</h3>
            </div>
            <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nisn">NIS</label>
                                <input type="text" class="form-control" id="nisn" name="nisn" value="{{ old('nisn', $siswa->nisn) }}" required>
                                @error('nisn')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" required>
                                @error('nama')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" required>
                                @error('tempat_lahir')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}" required>
                                @error('tanggal_lahir')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- ================== TAMBAHKAN BAGIAN INI ================== -->
                            <div class="form-group">
                                <label for="diterima_tanggal">Tanggal Diterima</label>
                                <input type="date" class="form-control" id="diterima_tanggal" name="diterima_tanggal" value="{{ old('diterima_tanggal', $siswa->diterima_tanggal) }}" required>
                                @error('diterima_tanggal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status_keluarga">Status Keluarga</label>
                                <select name="status_keluarga" id="status_keluarga" class="form-control" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Anak Pertama" {{ old('status_keluarga', $siswa->status_keluarga) == 'Anak Pertama' ? 'selected' : '' }}>Anak Pertama</option>
                                    <option value="Anak Kedua" {{ old('status_keluarga', $siswa->status_keluarga) == 'Anak Kedua' ? 'selected' : '' }}>Anak Kedua</option>
                                    <option value="Anak Ketiga" {{ old('status_keluarga', $siswa->status_keluarga) == 'Anak Ketiga' ? 'selected' : '' }}>Anak Ketiga</option>
                                    <option value="Anak Keempat" {{ old('status_keluarga', $siswa->status_keluarga) == 'Anak Keempat' ? 'selected' : '' }}>Anak Keempat</option>
                                </select>
                                @error('status_keluarga')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- ================== AKHIR DARI BAGIAN YANG DITAMBAHKAN ================== -->

                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <input type="text" class="form-control" id="agama" name="agama" value="{{ old('agama', $siswa->agama) }}" required>
                                @error('agama')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3" required>{{ old('alamat', $siswa->alamat) }}</textarea>
                                @error('alamat')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_ayah">Nama Ayah</label>
                                <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $siswa->nama_ayah) }}" required>
                                @error('nama_ayah')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                                <input type="text" class="form-control" id="pekerjaan_ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $siswa->pekerjaan_ayah) }}" required>
                                @error('pekerjaan_ayah')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="nama_ibu">Nama Ibu</label>
                                <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu', $siswa->nama_ibu) }}" required>
                                @error('nama_ibu')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                                <input type="text" class="form-control" id="pekerjaan_ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $siswa->pekerjaan_ibu) }}" required>
                                @error('pekerjaan_ibu')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="no_hp_ortu">No. HP Orang Tua</label>
                                <input type="text" class="form-control" id="no_hp_ortu" name="no_hp_ortu" value="{{ old('no_hp_ortu', $siswa->no_hp_ortu) }}" required>
                                @error('no_hp_ortu')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kelas_id">Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id', $siswa->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="foto">Foto Siswa</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="foto" class="custom-file-input" id="foto">
                                        <label class="custom-file-label" for="foto">Pilih file...</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</small>
                                @if($siswa->foto)
                                <small class="form-text text-muted d-block">Foto saat ini: <img src="{{ asset('img/foto_siswa/' . $siswa->foto) }}" alt="Foto Siswa" width="50" height="50" class="img-thumbnail"></small>
                                @endif
                                @error('foto')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection