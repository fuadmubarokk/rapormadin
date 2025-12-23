{{-- resources/views/rapor/_kelas_content.blade.php --}}

<div class="card">
    <div class="card-header">
        <!-- Header akan menumpuk di layar kecil -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h3 class="card-title mb-2 mb-md-0">

                <!-- IKON: hanya tampil di md ke atas -->
                <i class="fas fa-list mr-2 d-none d-md-inline"></i>

                <!-- JUDUL DESKTOP -->
                <span class="d-none d-md-inline">
                    Daftar Rapor - Kelas {{ $kelas->nama_kelas }}
                </span>

                <!-- JUDUL MOBILE -->
                <span class="d-block d-md-none">
                    Daftar Rapor
                </span>

                <!-- BARIS KE-2 MOBILE -->
                <span class="d-block d-md-none text-muted">
                    Kelas {{ $kelas->nama_kelas }}
                </span>

                <!-- SUB JUDUL (desktop) -->
                <small class="d-none d-md-block text-muted mt-1">
                    Semester {{ $semester }} Tahun Ajaran {{ $tahunAjaran->nama_tahun }}
                </small>

            </h3>
            
            <!-- Tombol akan menumpuk vertikal di layar kecil -->
            <div class="d-flex flex-column flex-md-row align-items-center mt-2 mt-md-0">
                <a href="{{ route('rapor.cetakSemuaCover', $kelas->id) }}" 
                   class="btn btn-warning btn-sm mb-2 mb-md-0 mr-md-2" 
                   title="Download Semua Cover">
                    <i class="fas fa-images mr-1"></i> Download Semua Cover
                </a>

                <a href="{{ route('rapor.cetakSemuaRapor', $kelas->id) }}" 
                   class="btn btn-primary btn-sm" 
                   title="Download Semua Rapor">
                    <i class="fas fa-download mr-1"></i> Download Semua Rapor
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mt-1">
            <small class="text-muted d-block mb-1">
                Progress Kelengkapan Data:
            </small>

            <div class="d-flex flex-wrap">
                <span class="badge badge-success mr-2 mb-1">
                    {{ $jumlahLengkap }} Lengkap ({{ $persentaseLengkap }}%)
                </span>
                <span class="badge badge-danger mb-1">
                    {{ $jumlahBelumLengkap }} Belum Lengkap ({{ $persentaseBelumLengkap }}%)
                </span>
            </div>
        </div>
        
        @if($siswaList->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Belum ada santri yang terdaftar di kelas ini.
            </div>
        @else
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center text-nowrap" style="width: 50px;">No</th>
                            <th class="text-center text-nowrap">Nama Lengkap</th>
                            <th class="text-center text-nowrap">Status Data</th>
                            <th class="text-center text-nowrap" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaList as $key => $item)
                            <tr>
                                <td class="text-center text-nowrap">{{ $key + 1 }}</td>
                                <td class="text-nowrap">{{ $item->nama }}</td>
                                <td class="text-center text-nowrap">
                                    <div class="progress" style="height: 22px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $dataKelengkapan[$item->id]['persentase'] > 0 ? $dataKelengkapan[$item->id]['persentase'] . '%' : '35px' }}; background-color: {{ $dataKelengkapan[$item->id]['persentase'] > 0 ? '#28a745' : '#dc3545' }}; color: white;" 
                                             aria-valuenow="{{ $dataKelengkapan[$item->id]['persentase'] }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $dataKelengkapan[$item->id]['persentase'] }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="d-flex justify-content-center align-items-center flex-row">
                                        <a href="{{ route('rapor.show', $item->id) }}" 
                                        class="btn btn-sm btn-info mx-1" 
                                        title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('rapor.cetakCover', $item->id) }}" 
                                        class="btn btn-sm btn-warning mx-1" 
                                        title="Cetak Cover" 
                                        target="_blank">
                                            <i class="fas fa-file-image"></i>
                                        </a>

                                        <a href="{{ route('rapor.cetakRapor', $item->id) }}" 
                                        class="btn btn-sm btn-success mx-1 {{ $dataKelengkapan[$item->id]['lengkap'] ? '' : 'disabled' }}" 
                                        title="Cetak PDF" 
                                        @if(!$dataKelengkapan[$item->id]['lengkap']) 
                                            onclick="event.preventDefault(); alert('Data rapor belum lengkap. Silakan lengkapi semua data terlebih dahulu.');" 
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
        @endif
    </div>
</div>