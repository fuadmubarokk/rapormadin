@extends('layouts.app')

@section('title', 'Penugasan Guru')

@section('breadcrumb', 'Penugasan Guru')

@section('styles')
<style>
    .card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-info {
        background-color: #17a2b8;
        border: none;
    }
    
    .btn-info:hover {
        background-color: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #fc5c7d 0%, #6a82fb 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border: none;
    }
    
    .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(106, 17, 203, 0.05);
        transform: scale(1.01);
    }
    
    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        border: none;
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }
    
    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        color: #ddd;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    
    .action-buttons .btn {
        margin-right: 5px;
    }
    
    .action-buttons .btn:last-child {
        margin-right: 0;
    }
    
    .search-box {
        position: relative;
    }
    
    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        z-index: 10;
    }
    
    .search-box input {
        padding-left: 40px;
        border-radius: 25px;
    }
</style>
@endsection

@section('content')
<div class="row fade-in">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title m-0">
                    <i class="fas fa-chalkboard-teacher mr-2"></i>
                    Penugasan Guru
                </h3>
                <div>
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalAdd">
                        <i class="fas fa-plus mr-1"></i> Tambah Penugasan
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="hapusSemuaBtn">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Semua Data
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group search-box">
                            <label for="searchInput">Cari Data:</label>
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan nama, kelas, atau mapel..." value="{{ request()->get('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterKelas">Filter Kelas:</label>
                            <select name="filterKelas" id="filterKelas" class="form-control">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ request()->get('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="showEntries">Tampilkan:</label>
                            <select name="showEntries" id="showEntries" class="form-control">
                                <option value="10" {{ request()->get('showEntries', 10) == '10' ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request()->get('showEntries') == '25' ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request()->get('showEntries') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request()->get('showEntries') == '100' ? 'selected' : '' }}>100</option>
                                <option value="-1" {{ request()->get('showEntries') == '-1' ? 'selected' : '' }}>Semua</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="assignmentTable">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Guru</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data awal akan dimuat di sini oleh server -->
                            @include('admin.guru_mapel_kelas._table_body')
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-muted info-text">
                            Menampilkan {{ $guruMapelKelas->firstItem() ?? 0 }} hingga {{ $guruMapelKelas->lastItem() ?? 0 }} dari {{ $guruMapelKelas->total() }} data
                        </div>
                        <div class="pagination-container">
                            {{ $guruMapelKelas->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Penugasan -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.guru_mapel_kelas.store') }}" method="post" id="addForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Penugasan Guru
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="guru_id"><i class="fas fa-user-tie mr-1"></i> Guru</label>
                        <select name="guru_id" id="guru_id" class="form-control" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($guru as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mapel_id"><i class="fas fa-book mr-1"></i> Mapel</label>
                        <select name="mapel_id" id="mapel_id" class="form-control" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kelas_id"><i class="fas fa-school mr-1"></i> Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Penugasan (dinamis via AJAX) -->
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Penugasan
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form akan dimuat via AJAX -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Fungsi untuk menunda pencarian (agar tidak setiap ketik ada request)
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Fungsi untuk memuat modal edit
        function loadEditModal(id) {
            console.log('Loading edit modal for ID:', id);
            
            $.ajax({
                url: `{{ route('admin.guru_mapel_kelas.edit', ':id') }}`.replace(':id', id),
                type: 'GET',
                success: function(response) {
                    console.log('Response received:', response);
                    
                    // Update form action dengan ID yang benar
                    $('#editForm').attr('action', `{{ route('admin.guru_mapel_kelas.update', ':id') }}`.replace(':id', id));
                    // Isi modal body dengan form edit
                    $('#modalEdit .modal-body').html(response.form);
                    $('#modalEdit').modal('show');
                },
                error: function(xhr) {
                    console.error('Error loading edit form:', xhr);
                    Swal.fire('Error!', 'Gagal memuat form edit.', 'error');
                }
            });
        }
        
        // Event listener untuk tombol edit
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('Edit button clicked for ID:', id);
            loadEditModal(id);
        });

        // Fungsi utama untuk mengambil data via AJAX
        function fetchData(page = 1) {
            // Tampilkan indikator loading
            $('#assignmentTable tbody').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></td></tr>');

            const searchValue = $('#searchInput').val();
            const kelasId = $('#filterKelas').val();
            const showEntries = $('#showEntries').val();

            // Buat URL dengan parameter
            let url = `{{ route('admin.guru_mapel_kelas.index') }}?page=${page}`;
            if (searchValue) url += `&search=${searchValue}`;
            if (kelasId) url += `&kelas_id=${kelasId}`;
            if (showEntries) url += `&showEntries=${showEntries}`;

            // Muat ulang halaman dengan parameter baru
            window.location.href = url;
        }

        // Buat versi debounced dari fungsi pencarian
        const debouncedSearch = debounce(function() {
            fetchData(1); // Kembali ke halaman 1 saat melakukan pencarian baru
        }, 500); // Tunggu 500ms setelah user berhenti mengetik

        // Event listener untuk filter
        $('#searchInput').on('input', debouncedSearch); // Gunakan 'input' untuk live search
        $('#filterKelas').on('change', () => fetchData(1));
        $('#showEntries').on('change', () => fetchData(1));
        
        // --- FUNGSI HAPUS SATU DATA ---
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Penugasan ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        url: '{{ route("admin.guru_mapel_kelas.destroy", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                location.reload(); // Muat ulang halaman
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
        
        // --- FUNGSI HAPUS SEMUA DATA ---
        $('#hapusSemuaBtn').on('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Hapus Semua Data?',
                text: "Semua data penugasan akan dihapus secara permanen! Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        url: '{{ route("admin.guru_mapel_kelas.delete_all") }}',
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                location.reload(); // Muat ulang halaman
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
        
        // --- FORM SUBMIT HANDLERS ---
        // Handle submit form tambah
        $('#addForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#modalAdd').modal('hide');
                        $('#addForm')[0].reset();
                        location.reload(); // Muat ulang halaman
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Gagal menyimpan data.', 'error');
                    console.error(xhr.responseText);
                }
            });
        });
        
        // Handle submit form edit
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#modalEdit').modal('hide');
                        location.reload(); // Muat ulang halaman
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Gagal menyimpan data.', 'error');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection