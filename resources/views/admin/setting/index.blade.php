@extends('layouts.app')

@section('title', 'Pengaturan Sekolah dan Rapor')

@section('breadcrumb', 'Pengaturan Sekolah dan Rapor')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengaturan Sekolah</h3>
            </div>
            <form action="{{ route('admin.setting.update') }}" method="post" enctype="multipart/form-data">
                @csrf
                @if($setting) @method('PUT') @endif
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_madrasah">Nama Madrasah (Indonesia)</label>
                        <input type="text" class="form-control" id="nama_madrasah" name="nama_madrasah" value="{{ old('nama_madrasah', $setting->nama_madrasah ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_madrasah_ar">Nama Madrasah (Arab)</label>
                        <input type="text" class="form-control" id="nama_madrasah_ar" name="nama_madrasah_ar" value="{{ old('nama_madrasah_ar', $setting->nama_madrasah_ar ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="desa">Desa/Kelurahan</label>
                        <input type="text" class="form-control" id="desa" name="desa" value="{{ old('desa', $setting->desa ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="kecamatan">Kecamatan</label>
                        <input type="text" class="form-control" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $setting->kecamatan ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="kabupaten">Kabupaten/Kota</label>
                        <input type="text" class="form-control" id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $setting->kabupaten ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="sekretariat">Sekretariat</label>
                        <input type="text" class="form-control" id="sekretariat" name="sekretariat" value="{{ old('sekretariat', $setting->sekretariat ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="tempat_ttd">Tempat Tandatangan Rapor</label>
                        <input type="text" class="form-control" id="tempat_ttd" name="tempat_ttd" value="{{ old('tempat_ttd', $setting->tempat_ttd ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_rapor">Penanggalan Rapor</label>
                        <input type="date" class="form-control" id="tanggal_rapor" name="tanggal_rapor" value="{{ old('tanggal_rapor', $setting->tanggal_rapor ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="npsn">Nomor Statistik Madrasah</label>
                        <input type="text" class="form-control" id="npsn" name="npsn" value="{{ old('npsn', $setting->npsn ?? '') }}" required>
                    </div>
                    
                    <!-- Input untuk Logo -->
                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="logo" class="custom-file-input" id="logo">
                                <label class="custom-file-label" for="logo">Pilih file...</label>
                            </div>
                        </div>
                        @if($setting && $setting->logo)
                            <small class="form-text text-muted">Logo saat ini:</small>
                            <img src="{{ asset('img/logo/' . $setting->logo) }}" alt="Logo Madrasah" style="max-height: 80px; border: 1px solid #ddd; padding: 5px; margin-top: 5px;">
                        @endif
                    </div>

                    <!-- Input untuk Tanda Tangan Kepala Madrasah -->
                    <div class="form-group">
                        <label for="ttd_kepala_madrasah">Tanda Tangan Kepala Madrasah</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="ttd_kepala_madrasah" class="custom-file-input" id="ttd_kepala_madrasah" accept="image/*">
                                <label class="custom-file-label" for="ttd_kepala_madrasah">Pilih file tanda tangan...</label>
                            </div>
                        </div>
                        @if($setting && $setting->ttd_kepala_madrasah)
                            <small class="form-text text-muted">Tanda tangan saat ini:</small>
                            <img src="{{ asset('img/ttd/' . $setting->ttd_kepala_madrasah) }}" alt="Tanda Tangan Kepala Madrasah" style="max-height: 80px; border: 1px solid #ddd; padding: 5px; margin-top: 5px;">
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label for="kepala_madrasah">Kepala Madrasah</label>
                        <input type="text" class="form-control" id="kepala_madrasah" name="kepala_madrasah" value="{{ old('kepala_madrasah', $setting->kepala_madrasah ?? '') }}" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Semua Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection