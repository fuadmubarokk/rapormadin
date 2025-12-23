@extends('layouts.app')

@section('title', 'Absensi')

@section('breadcrumb', 'Absensi')

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

    .modern-table input.form-control {
        background-color: #e1e3e6 !important; /* cream */
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
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Input Absensi</h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="dropdown-item">Export PDF</a>
                            <a href="#" class="dropdown-item">Export Excel</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 p-md-3">
                <div class="row mb-3 px-3 px-md-0 pt-3 pt-md-0">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tahun_ajaran">Tahun Ajaran</label>
                            <input type="text" class="form-control" value="{{ $tahunAjaran->nama_tahun ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <input type="text" class="form-control" value="{{ $semester }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kelas">Kelas</label>
                            <input type="text" class="form-control" value="{{ $kelas->nama_kelas ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
                
                <!-- Tampilan Desktop (MODERN & ELEGAN) -->
                <div class="d-none d-md-block">
                    <form id="save-all-form" action="{{ route('wali.absensi.store_all') }}" method="POST">
                        @csrf
                        <div class="table-responsive bg-white rounded-lg shadow-sm">
                            <table class="table modern-table mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Santri</th>
                                        <th>Sakit</th>
                                        <th>Izin</th>
                                        <th>Alpa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswa as $index => $item)
                                    <tr>
                                        <td class="font-weight-bold text-muted">{{ $index + 1 }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>
                                            <input type="number" name="sakit[{{ $item->id }}]" class="form-control form-control-sm border-0 bg-light text-center" 
                                                   value="{{ optional($absensi->get($item->id))->sakit ?? '' }}" min="0">
                                        </td>
                                        <td>
                                            <input type="number" name="izin[{{ $item->id }}]" class="form-control form-control-sm border-0 bg-light text-center" 
                                                   value="{{ optional($absensi->get($item->id))->izin ?? '' }}" min="0">
                                        </td>
                                        <td>
                                            <input type="number" name="alpa[{{ $item->id }}]" class="form-control form-control-sm border-0 bg-light text-center" 
                                                   value="{{ optional($absensi->get($item->id))->alpa ?? '' }}" min="0">
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill btn-save-individual" data-siswa-id="{{ $item->id }}">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                @if($absensi->has($item->id))
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-delete-individual ml-1" 
                                                        data-absensi-id="{{ $absensi->get($item->id)->id }}"
                                                        data-route="{{ route('wali.absensi.destroy', $absensi->get($item->id)) }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                            <div class="feedback-message mt-1"></div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center p-4">
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
                    <form id="save-all-form-mobile" action="{{ route('wali.absensi.store_all') }}" method="POST">
                        @csrf
                        <div class="p-3">
                            @forelse($siswa as $index => $item)
                            <!-- Kartu Santri -->
                            <div class="student-card mb-4 p-4 bg-white rounded-lg shadow-sm border-0">
                                <!-- Header Nama -->
                                <div class="d-flex align-items-center mb-4">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;">
                                        {{ $index + 1 }}
                                    </div>
                                    <h5 class="mb-0 font-weight-bold text-dark">{{ $item->nama }}</h5>
                                </div>

                                <!-- Input Nilai -->
                                <div class="row">
                                    <div class="col-4 pr-1">
                                        <div class="form-group">
                                            <label class="small text-muted font-weight-light">Sakit</label>
                                            <input type="number" name="sakit[{{ $item->id }}]" class="form-control border-0 bg-light text-center" 
                                                   value="{{ optional($absensi->get($item->id))->sakit ?? '' }}" 
                                                   min="0">
                                        </div>
                                    </div>
                                    <div class="col-4 px-1">
                                        <div class="form-group">
                                            <label class="small text-muted font-weight-light">Izin</label>
                                            <input type="number" name="izin[{{ $item->id }}]" class="form-control border-0 bg-light text-center" 
                                                   value="{{ optional($absensi->get($item->id))->izin ?? '' }}" 
                                                   min="0">
                                        </div>
                                    </div>
                                    <div class="col-4 pl-1">
                                        <div class="form-group">
                                            <label class="small text-muted font-weight-light">Alpa</label>
                                            <input type="number" name="alpa[{{ $item->id }}]" class="form-control border-0 bg-light text-center" 
                                                   value="{{ optional($absensi->get($item->id))->alpa ?? '' }}" 
                                                   min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Aksi -->
                                <div class="mt-4 d-grid gap-2">
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill btn-save-individual" data-siswa-id="{{ $item->id }}">
                                        <i class="fas fa-save mr-1"></i> Simpan Nilai
                                    </button>
                                    @if($absensi->has($item->id))
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn-delete-individual" 
                                            data-absensi-id="{{ $absensi->get($item->id)->id }}"
                                            data-route="{{ route('wali.absensi.destroy', $absensi->get($item->id)) }}">
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
@endsection

@push('scripts')
<script>
 $(document).ready(function() {
    const routeStore = "{{ route('wali.absensi.store') }}";

    // --- FITUR SIMPAN PER BARIS (AJAX) ---
    $(document).on('click', '.btn-save-individual', function() {
        const btn = $(this);
        const siswaId = btn.data('siswa-id');
        const feedbackDiv = btn.closest('td').find('.feedback-message'); // Penyesuaian selector untuk desktop
        const container = btn.closest('tr, .student-card'); 

        const sakit = container.find('input[name="sakit[' + siswaId + ']"]').val();
        const izin = container.find('input[name="izin[' + siswaId + ']"]').val();
        const alpa = container.find('input[name="alpa[' + siswaId + ']"]').val();

        // Validasi: Semua field absensi harus diisi
        if (sakit === '' || izin === '' || alpa === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Semua field (Sakit, Izin, Alpa) harus diisi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107'
            });
            return; 
        }

        // Simpan state tombol asli untuk dikembalikan nanti
        const originalBtnHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        feedbackDiv.html('');

        const formData = {
            _token: '{{ csrf_token() }}',
            siswa_id: siswaId,
            sakit: sakit,
            izin: izin,
            alpa: alpa,
        };

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

    // --- FITUR HAPUS PER BARIS (AJAX) ---
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
                        
                        container.find('input').val('');
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

    // --- FITUR SIMPAN SEMUA (MENGGUNAKAN AJAX) ---
    $('#save-all-btn').click(function(e) {
        e.preventDefault();
        const btn = $(this);

        // Tentukan form yang digunakan berdasarkan ukuran layar
        const isMobile = window.innerWidth < 768;
        const form = isMobile ? $('#save-all-form-mobile') : $('#save-all-form');

        // Validasi di frontend, pastikan ada data yang diisi
        let hasAnyData = false;
        form.find('input').each(function() {
            if ($(this).val() !== '') {
                hasAnyData = true;
                return false; 
            }
        });

        if (!hasAnyData) {
            Swal.fire({
                icon: 'info',
                title: 'Tidak Ada Perubahan',
                text: 'Tidak ada data absensi yang diisi. Tidak ada yang akan disimpan.',
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
                    url: form.attr('action'),
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