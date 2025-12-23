{{-- resources/views/rapor/_siswa_list.blade.php --}}

@if(isset($siswaList) && $siswaList->isNotEmpty())
    {{-- Kartu Statistik Kelengkapan --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-pie mr-2"></i>
                Statistik Kelengkapan Data Rapor
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Lengkap</span>
                            <span class="info-box-number">{{ $jumlahLengkap }} siswa</span>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ $persentaseLengkap }}%"></div>
                            </div>
                            <span class="progress-description">
                                <small class="progress-label">{{ $persentaseLengkap }}%</small>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Belum Lengkap</span>
                            <span class="info-box-number">{{ $jumlahBelumLengkap }} siswa</span>
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: {{ $persentaseBelumLengkap }}%"></div>
                            </div>
                            <span class="progress-description">
                                <small class="progress-label">{{ $persentaseBelumLengkap }}%</small>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi Kelas --}}
    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row mt-3 mb-3">
        <div class="text-muted">
            <small><i class="fas fa-info-circle mr-1"></i>Pilih aksi untuk kelas ini:</small>
        </div>
        <div class="btn-group">
            <a href="{{ route('rapor.cetakSemuaCover', $kelas->id) }}" class="btn btn-warning btn-sm" title="Download Semua Cover">
                <i class="fas fa-images mr-1"></i> Download Semua Cover
            </a>
            <a href="{{ route('rapor.cetakSemuaRapor', $kelas->id) }}" class="btn btn-primary btn-sm" title="Download Semua Rapor">
                <i class="fas fa-download mr-1"></i> Download Semua Rapor
            </a>
        </div>
    </div>

    {{-- Tabel Daftar Siswa --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Lengkap</th>
                            <th class="text-center">Status Kelengkapan</th>
                            <th class="text-center" style="width: 200px;">Progress</th>
                            <th class="text-center" style="width: 250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($siswaList as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $no++ }}</td>
                                <td class="align-middle text-nowrap">
                                    <div class="d-flex align-items-center">
                                        
                                        <div>
                                            <strong>{{ $item->nama }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    @if($dataKelengkapan[$item->id]['lengkap'])
                                        <span class="badge badge-success">Lengkap</span>
                                    @else
                                        <span class="badge badge-warning">Belum Lengkap</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar {{ $dataKelengkapan[$item->id]['persentase'] > 0 ? 'bg-success' : 'bg-danger' }} progress-bar-striped" role="progressbar" 
                                             style="width: {{ $dataKelengkapan[$item->id]['persentase'] }}%;" 
                                             aria-valuenow="{{ $dataKelengkapan[$item->id]['persentase'] }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $dataKelengkapan[$item->id]['persentase'] }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('rapor.show', $item->id) }}" class="btn btn-info" title="Lihat Detail Rapor">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rapor.cetakCover', $item->id) }}" class="btn btn-warning" title="Cetak Cover Rapor" target="_blank">
                                            <i class="fas fa-file-image"></i>
                                        </a>
                                        <a href="{{ route('rapor.cetakRapor', $item->id) }}" 
                                           class="btn btn-success {{ $dataKelengkapan[$item->id]['lengkap'] ? '' : 'disabled' }}" 
                                           title="Cetak Rapor PDF" 
                                           @if(!$dataKelengkapan[$item->id]['lengkap']) 
                                               onclick="event.preventDefault(); Swal.fire('Peringatan', 'Data rapor belum lengkap. Silakan lengkapi semua data terlebih dahulu.', 'warning');" 
                                           @endif
                                           target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Tidak Ada Data</h4>
                <p class="text-muted">Tidak ada siswa di kelas ini, atau silakan pilih kelas terlebih dahulu.</p>
            </div>
        </div>
    </div>
@endif