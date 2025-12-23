@extends('layouts.app')

@section('title', 'Input Nilai ' . $guruMapelKelas->mapel->nama_mapel . ' - ' . $guruMapelKelas->kelas->nama_kelas)

@section('breadcrumb', 'Input Nilai')

@push('styles')
<style>
    /* Kustomisasi Tabel Desktop agar Lebih Modern */
    .modern-table {
        border-collapse: separate; /* Penting untuk rounded corners */
        border-spacing: 0;
    }
    .modern-table thead th {
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    .modern-table tbody tr {
        transition: background-color 0.2s ease-in-out;
    }
    .modern-table tbody tr:not(:last-child) td {
        border-bottom: 1px solid #f1f3f5;
    }
    .modern-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .modern-table td, .modern-table th {
        vertical-align: middle;
        padding: 1rem 0.75rem; /* Padding lebih lega */
    }
    .modern-table td input.form-control {
        background-color: #e1e3e6 !important; /* ganti sesuai warna */
    }

    /* Style untuk tampilan mobile */
    .student-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .student-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* Kontras Tinggi untuk Form Input */
    .modern-table .form-control,
    .modern-table .form-select,
    .student-card .form-control,
    .student-card .form-select {
        background-color: #f1f3f5; /* Warna abu-abu yang jelas dan kontras */
        border: 1px solid #ced4da; /* Border yang terlihat */
        color: #495057; /* Warna teks yang jelas */
    }

    /* Saat form difokuskan (diklik), latar jadi putih untuk kenyamanan mengetik */
    .modern-table .form-control:focus,
    .modern-table .form-select:focus,
    .student-card .form-control:focus,
    .student-card .form-select:focus {
        background-color: #ffffff; /* Putih bersih saat fokus */
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Input Nilai {{ $guruMapelKelas->mapel->nama_mapel }} - {{ $guruMapelKelas->kelas->nama_kelas }}</h3>
                <div class="card-tools">
                    <a href="{{ route('guru.nilai.template', $guruMapelKelas->id) }}" class="btn btn-default btn-sm">
                        <i class="fas fa-file-download"></i> Template
                    </a>
                    <a href="{{ route('guru.nilai.export', $guruMapelKelas->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalImport">
                        <i class="fas fa-file-upload"></i> Import
                    </button>
                </div>
            </div>
            <div class="card-body p-0 p-md-3">
                <div class="row mb-3 px-3 px-md-0 pt-3 pt-md-0">
                    <div class="col-12">
                        <p class="mb-0">
                            <strong>Tahun Ajaran:</strong> {{ $tahunAjaran->nama_tahun }}<br>
                            <strong>Semester:</strong> {{ $semester }}
                        </p>
                    </div>
                </div>
                
                <!-- Tampilan Desktop (MODERN & ELEGAN) -->
                <div class="d-none d-md-block">
                    <form id="save-all-form" action="{{ route('guru.nilai.store_all') }}" method="POST">
                        @csrf
                        <input type="hidden" name="guru_mapel_kelas_id" value="{{ $guruMapelKelas->id }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">
                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran->nama_tahun }}">
                        <div class="table-responsive bg-white rounded-lg shadow-sm">
                            <table class="table modern-table mb-0">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th>NIS</th>
                                        <th>Nama Santri</th>
                                        <th width="120">Nilai UAS</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswa as $index => $s)
                                    <tr>
                                        <td class="font-weight-bold text-muted">{{ $index + 1 }}</td>
                                        <td>{{ $s->nisn }}</td>
                                        <td>{{ $s->nama }}</td>
                                        <td>
                                            <input type="number" name="nilai_uas[{{ $s->id }}]" class="form-control form-control-sm border-0 bg-light text-center" 
                                                   value="{{ optional($nilai->get($s->id))->nilai_uas ?? '' }}" 
                                                   min="0" max="100">
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill btn-save-individual" data-siswa-id="{{ $s->id }}">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                @if($nilai->has($s->id))
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-delete-individual ml-1" 
                                                        data-nilai-id="{{ $nilai->get($s->id)->id }}"
                                                        data-route="{{ route('guru.nilai.destroy', $nilai->get($s->id)) }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                            <div class="feedback-message mt-1"></div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-4">
                                            <i class="fas fa-user-graduate fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Tidak ada data santri</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                
                <!-- Tampilan Mobile (MODERN & ELEGAN) -->
                <div class="d-md-none">
                    <form id="save-all-form-mobile" action="{{ route('guru.nilai.store_all') }}" method="POST">
                        @csrf
                        <input type="hidden" name="guru_mapel_kelas_id" value="{{ $guruMapelKelas->id }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">
                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran->nama_tahun }}">
                        <div class="p-3">
                            @forelse($siswa as $index => $s)
                            <!-- Kartu Santri -->
                            <div class="student-card mb-4 p-4 bg-white rounded-lg shadow-sm border-0">
                                <!-- Header Nama -->
                                <div class="d-flex align-items-center mb-4">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold text-dark">{{ $s->nama }}</h5>
                                        <small class="text-muted">NIS: {{ $s->nisn }}</small>
                                    </div>
                                </div>

                                <!-- Input Nilai -->
                                <div class="form-group mb-3">
                                    <label class="small text-muted font-weight-light">Nilai UAS</label>
                                    <input type="number" name="nilai_uas[{{ $s->id }}]" class="form-control border-0 bg-light text-center" 
                                           value="{{ optional($nilai->get($s->id))->nilai_uas ?? '' }}" 
                                           min="0" max="100">
                                </div>
                                
                                <!-- Aksi -->
                                <div class="mt-4 d-grid gap-2">
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill btn-save-individual" data-siswa-id="{{ $s->id }}">
                                        <i class="fas fa-save mr-1"></i> Simpan Nilai
                                    </button>
                                    @if($nilai->has($s->id))
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn-delete-individual" 
                                            data-nilai-id="{{ $nilai->get($s->id)->id }}"
                                            data-route="{{ route('guru.nilai.destroy', $nilai->get($s->id)) }}">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                    @endif
                                    <div class="feedback-message"></div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center p-5">
                                <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data santri</p>
                            </div>
                            @endforelse
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-primary" id="save-all-btn">
                    <i class="fas fa-save"></i> Simpan Semua
                </button>
                <button type="button" class="btn btn-default" id="reset-form-btn">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Nilai (Tetap dipertahankan) -->
<div class="modal fade" id="modalImport">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import Nilai</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('guru.nilai.import') }}" method="post" enctype="multipart/form-data">
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
                        <small class="form-text text-muted">
                            Format file: .xlsx atau .xls, maksimal 2MB
                        </small>
                    </div>
                    <input type="hidden" name="guru_mapel_kelas_id" value="{{ $guruMapelKelas->id }}">
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran->nama_tahun }}">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
 $(document).ready(function() {
    console.log('DEBUG: Script untuk input nilai guru dimuat.');

    const routeStore = "{{ route('guru.nilai.store') }}";
    const routeStoreAll = "{{ route('guru.nilai.store_all') }}";

    // --- FITUR SIMPAN PER BARIS (AJAX) ---
    $(document).on('click', '.btn-save-individual', function() {
        const btn = $(this);
        const siswaId = btn.data('siswa-id');
        const feedbackDiv = btn.closest('td').find('.feedback-message'); // Penyesuaian selector untuk desktop
        const container = btn.closest('tr, .student-card'); 

        const formData = {
            _token: '{{ csrf_token() }}',
            siswa_id: siswaId,
            guru_mapel_kelas_id: '{{ $guruMapelKelas->id }}',
            semester: '{{ $semester }}',
            tahun_ajaran: '{{ $tahunAjaran->nama_tahun }}',
            nilai_uas: container.find('input[name="nilai_uas[' + siswaId + ']"]').val(),
        };

        // Validasi sederhana di sisi klien
        if (formData.nilai_uas === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Data Kosong',
                text: 'Nilai UAS harus diisi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107'
            });
            return; 
        }

        // Simpan state tombol asli untuk dikembalikan nanti
        const originalBtnHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        feedbackDiv.html('');

        $.ajax({
            url: routeStore,
            type: 'POST',
            data: formData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    if (xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessage
                });
                btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

    // --- FITUR SIMPAN SEMUA ---
    $('#save-all-btn').click(function(e) {
        e.preventDefault();
        const btn = $(this);

        // Tentukan form yang digunakan berdasarkan ukuran layar
        const isMobile = window.innerWidth < 768;
        const form = isMobile ? $('#save-all-form-mobile') : $('#save-all-form');

        let hasAnyData = false;
        form.find('input[type="number"]').each(function() {
            if ($(this).val() !== '') {
                hasAnyData = true;
                return false; 
            }
        });

        if (!hasAnyData) {
            Swal.fire({
                icon: 'info',
                title: 'Tidak Ada Perubahan',
                text: 'Tidak ada nilai yang diisi. Tidak ada yang akan disimpan.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#17a2b8'
            });
            return; 
        }

        Swal.fire({
            title: 'Simpan Semua?',
            text: "Apakah Anda yakin ingin menyimpan semua perubahan?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan Semua...');

                $.ajax({
                    url: routeStoreAll,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan semua data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Semua');
                    }
                });
            }
        });
    });

    // --- TAMBAHKAN INI: FITUR HAPUS PER BARIS (AJAX) ---
     $(document).on('click', '.btn-delete-individual', function() {
        const btn = $(this);
        const route = btn.data('route');
        const feedbackDiv = btn.closest('td').find('.feedback-message'); // Penyesuaian selector untuk desktop
        const container = btn.closest('tr, .student-card'); 
    
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const originalBtnHtml = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
                $.ajax({
                    url: route,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Kosongkan input nilai dan sembunyikan tombol hapus
                        container.find('input[name="nilai_uas[' + btn.data('siswa-id') + ']"]').val('');
                        btn.fadeOut();
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal menghapus data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                        btn.prop('disabled', false).html(originalBtnHtml);
                    }
                });
            }
        });
    });

    // --- FITUR RESET FORM ---
    $('#reset-form-btn').click(function() {
        Swal.fire({
            title: 'Reset Form?',
            text: "Semua perubahan yang belum disimpan akan hilang!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tentukan form yang digunakan berdasarkan ukuran layar
                const isMobile = window.innerWidth < 768;
                const form = isMobile ? $('#save-all-form-mobile') : $('#save-all-form');
                
                form[0].reset();
                $('.feedback-message').html('');
                Swal.fire({
                    icon: 'success',
                    title: 'Direset!',
                    text: 'Form telah dikembalikan ke kondisi awal.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
});
</script>
@endpush