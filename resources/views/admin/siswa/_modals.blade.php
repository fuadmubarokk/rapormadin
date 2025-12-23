<!-- Modal Create (DIPERBARUI) -->
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('admin.siswa.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Santri Baru</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nisn">NIS</label>
                                <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}" required>
                                @error('nisn')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                                @error('nama')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                                @error('tempat_lahir')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                                @error('tanggal_lahir')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- ================== TAMBAHKAN BAGIAN INI ================== -->
                            <div class="form-group">
                                <label for="diterima_tanggal">Tanggal Diterima</label>
                                <input type="date" name="diterima_tanggal" class="form-control" value="{{ old('diterima_tanggal') }}" required>
                                @error('diterima_tanggal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status_keluarga">Status Keluarga</label>
                                <select name="status_keluarga" class="form-control" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Anak Pertama" {{ old('status_keluarga') == 'Anak Pertama' ? 'selected' : '' }}>Anak Pertama</option>
                                    <option value="Anak Kedua" {{ old('status_keluarga') == 'Anak Kedua' ? 'selected' : '' }}>Anak Kedua</option>
                                    <option value="Anak Ketiga" {{ old('status_keluarga') == 'Anak Ketiga' ? 'selected' : '' }}>Anak Ketiga</option>
                                    <option value="Anak Keempat" {{ old('status_keluarga') == 'Anak Keempat' ? 'selected' : '' }}>Anak Keempat</option>
                                </select>
                                @error('status_keluarga')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- ================== AKHIR DARI BAGIAN YANG DITAMBAHKAN ================== -->

                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <input type="text" name="agama" class="form-control" value="{{ old('agama') }}" required>
                                @error('agama')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_ayah">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}" required>
                                @error('nama_ayah')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah') }}" required>
                                @error('pekerjaan_ayah')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="nama_ibu">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}" required>
                                @error('nama_ibu')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu') }}" required>
                                @error('pekerjaan_ibu')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="no_hp_ortu">No. HP Orang Tua</label>
                                <input type="text" name="no_hp_ortu" class="form-control" value="{{ old('no_hp_ortu') }}" required>
                                @error('no_hp_ortu')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kelas_id">Kelas</label>
                                <select name="kelas_id" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
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
                                <small class="form-text text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB.</small>
                                @error('foto')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImport">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import Data Santri</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.siswa.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Pilih File Excel</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" id="file" required>
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