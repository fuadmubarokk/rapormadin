@extends('layouts.app')

@section('title', 'Detail Rapor ' . $siswa->nama)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Tombol Aksi -->
            <div class="d-flex flex-column flex-md-row justify-content-md-start mb-3">
                <a href="{{ route('rapor.index') }}" class="btn btn-secondary mb-2 mb-md-0 mr-md-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('rapor.cetakRapor', $siswa->id) }}" class="btn btn-success" target="_blank">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-graduate mr-2"></i>Rapor Siswa</h3>
                </div>
                <div class="card-body">
                    <!-- Identitas -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Identitas Siswa</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <tr><td><strong>Nama</strong></td><td>{{ $siswa->nama }}</td></tr>
                                    <tr><td><strong>NIS</strong></td><td>{{ $siswa->nisn ?? '-' }}</td></tr>
                                    <tr><td><strong>Kelas</strong></td><td>{{ $kelas->nama_kelas }}</td></tr>
                                    <tr><td><strong>Semester</strong></td><td>{{ $semester }}</td></tr>
                                    <tr><td><strong>Tahun Ajaran</strong></td><td>{{ $tahunAjaran->nama_tahun }}</td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Identitas Sekolah</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <tr><td><strong>Nama</strong></td><td>{{ $settingSekolah->nama_madrasah ?? '-' }}</td></tr>
                                    <tr><td><strong>Alamat</strong></td><td>{{ $settingSekolah->desa ?? '-' }} - {{ $settingSekolah->kecamatan ?? '-' }} - {{ $settingSekolah->kabupaten ?? '-' }}</td></tr>
                                    <tr><td><strong>Kepala Madrasah</strong></td><td>{{ $settingSekolah->kepala_madrasah ?? '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Nilai Tulisan -->
                    <h5 class="mt-4">Nilai Mata Pelajaran</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">No</th>
                                    <th class="text-nowrap">Mata Pelajaran</th>
                                    <th class="text-nowrap">Nilai UAS</th>
                                    <th class="text-nowrap">Terbilang</th>
                                    <th class="text-nowrap">Terbilang Arab</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nilaiTulis as $key => $nilai)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td>{{ $nilai->guruMapelKelas->mapel->nama_mapel ?? '-' }}</td>
                                        <td class="text-center">{{ $nilai->nilai_uas }}</td>
                                        <td class="text-nowrap">{{ ucwords(terbilang($nilai->nilai_uas ?? 0)) }}</td>
                                        <td class="text-nowrap" dir="rtl">{{ terbilangArab($nilai->nilai_uas ?? 0) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Belum ada nilai tulis.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Nilai Non Tulis -->
                    <h5 class="mt-4">Nilai Non Tulis</h5>
                    @if($nilaiNonTulis)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped">
                                <tr><th class="text-nowrap">Qiroatul Kutub</th><td>{{ $nilaiNonTulis->qiroatul_kutub ?? '-' }}</td></tr>
                                <tr><th class="text-nowrap">Taftisyul Kutub</th><td>{{ $nilaiNonTulis->taftisyul_kutub ?? '-' }}</td></tr>
                                <tr><th class="text-nowrap">Muhafadzhoh</th><td>{{ $nilaiNonTulis->muhafadzhoh ?? '-' }}</td></tr>
                            </table>
                        </div>
                    @else
                        <p>Belum ada nilai non tulis.</p>
                    @endif

                    <!-- Karakter -->
                    <h5 class="mt-4">Penilaian Karakter</h5>
                    @if($penilaianKarakter)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped">
                                <tr><th class="text-nowrap">Kelakuan</th><td>{{ $penilaianKarakter->kelakuan }}</td></tr>
                                <tr><th class="text-nowrap">Kerajinan</th><td>{{ $penilaianKarakter->kerajinan }}</td></tr>
                                <tr><th class="text-nowrap">Kerapihan</th><td>{{ $penilaianKarakter->kerapihan }}</td></tr>
                            </table>
                        </div>
                    @else
                        <p>Belum ada penilaian karakter.</p>
                    @endif

                    <!-- Absensi -->
                    <h5 class="mt-4">Absensi</h5>
                    @if($absensi)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped">
                                <tr><th class="text-nowrap">Sakit</th><td>{{ $absensi->sakit }} Hari</td></tr>
                                <tr><th class="text-nowrap">Izin</th><td>{{ $absensi->izin }} Hari</td></tr>
                                <tr><th class="text-nowrap">Tanpa Keterangan</th><td>{{ $absensi->alpa }} Hari</td></tr>
                            </table>
                        </div>
                    @else
                        <p>Belum ada data absensi.</p>
                    @endif

                    <!-- Catatan -->
                    <h5 class="mt-4">Catatan Wali Kelas</h5>
                    <p>{{ $catatanRapor->catatan ?? 'Belum ada catatan wali kelas.' }}</p>

                    <!-- Rangkuman -->
                    <h5 class="mt-4">Rangkuman</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <tr><th class="text-nowrap">Total Nilai</th><td>{{ $totalNilai }}</td></tr>
                            <tr><th class="text-nowrap">Rata-Rata</th><td>{{ number_format($rataRata, 2) }}</td></tr>
                            <tr><th class="text-nowrap">Ranking di Kelas</th><td>{{ $ranking }} dari {{ count($rankingData) }} siswa</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection