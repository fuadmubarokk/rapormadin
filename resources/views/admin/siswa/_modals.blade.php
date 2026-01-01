<!-- Modal Create -->
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('admin.siswa.store') }}" method="post" enctype="multipart/form-data" id="formCreate">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Santri Baru</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tanggal Diterima <span class="text-danger">*</span></label>
                                <input type="date" name="diterima_tanggal" class="form-control" value="{{ old('diterima_tanggal') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status Keluarga <span class="text-danger">*</span></label>
                                <select name="status_keluarga" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Anak Pertama" {{ old('status_keluarga') == 'Anak Pertama' ? 'selected' : '' }}>Anak Pertama</option>
                                    <option value="Anak Kedua" {{ old('status_keluarga') == 'Anak Kedua' ? 'selected' : '' }}>Anak Kedua</option>
                                    <option value="Anak Ketiga" {{ old('status_keluarga') == 'Anak Ketiga' ? 'selected' : '' }}>Anak Ketiga</option>
                                    <option value="Anak Keempat" {{ old('status_keluarga') == 'Anak Keempat' ? 'selected' : '' }}>Anak Keempat</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Agama <span class="text-danger">*</span></label>
                                <input type="text" name="agama" class="form-control" value="{{ old('agama') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Ayah <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pekerjaan Ayah <span class="text-danger">*</span></label>
                                    <input type="text" name="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah') }}" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Ibu <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pekerjaan Ibu <span class="text-danger">*</span></label>
                                    <input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu') }}" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">No. HP Orang Tua <span class="text-danger">*</span></label>
                                <input type="text" name="no_hp_ortu" class="form-control" value="{{ old('no_hp_ortu') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select name="kelas_id" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Foto Siswa</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                                <div class="form-text">Format: JPG, PNG, JPEG. Maksimal 2MB.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="formEdit" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">Edit Data Santri</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="text" name="nisn" id="edit_nisn" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="edit_nama" class="form-control" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tanggal Diterima <span class="text-danger">*</span></label>
                                <input type="date" name="diterima_tanggal" id="edit_diterima_tanggal" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status Keluarga <span class="text-danger">*</span></label>
                                <select name="status_keluarga" id="edit_status_keluarga" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Anak Kandung">Anak Kandung</option>
                                    <option value="Anak Tiri">Anak Tiri</option>
                                    <option value="Anak Angkat">Anak Angkat</option>
                                    <option value="Cucu">Cucu</option>
                                    <option value="Famili Lain">Famili Lain</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Agama <span class="text-danger">*</span></label>
                                <input type="text" name="agama" id="edit_agama" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" id="edit_alamat" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Ayah <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_ayah" id="edit_nama_ayah" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pekerjaan Ayah <span class="text-danger">*</span></label>
                                    <input type="text" name="pekerjaan_ayah" id="edit_pekerjaan_ayah" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Ibu <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_ibu" id="edit_nama_ibu" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pekerjaan Ibu <span class="text-danger">*</span></label>
                                    <input type="text" name="pekerjaan_ibu" id="edit_pekerjaan_ibu" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">No. HP Orang Tua <span class="text-danger">*</span></label>
                                <input type="text" name="no_hp_ortu" id="edit_no_hp_ortu" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select name="kelas_id" id="edit_kelas_id" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Foto Siswa</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                                <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto</div>
                                <div class="mt-2">
                                    <img id="edit_foto_preview" src="" alt="Foto" class="img-thumbnail" style="max-height: 150px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import Data Santri</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.siswa.import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                        <div class="form-text">Format file: .xlsx atau .xls</div>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Pastikan format file sesuai template. 
                            <a href="{{ route('admin.siswa.template') }}" class="alert-link">Download template</a>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>