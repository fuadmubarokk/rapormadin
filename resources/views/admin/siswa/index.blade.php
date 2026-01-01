@extends('layouts.app')

@section('title', 'Data Santri')

@section('breadcrumb', 'Data Santri')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Santri</h3>
                <div class="card-tools d-flex flex-wrap gap-2 align-items-center">
                    <!-- Live Search -->
                    <input type="text" id="searchInput" class="form-control form-control-sm"
                        placeholder="Cari siswa..." style="width: 180px;"
                        value="{{ request('search') }}">

                    <!-- Filter Kelas -->
                    <select id="filterKelas" class="form-control form-control-sm" style="width:150px;">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Select Jumlah Data -->
                    <select id="perPageSelect" class="form-control form-control-sm" style="width:100px;">
                        <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="25" {{ request('per_page', 20) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                    </select>

                    <!-- Tombol lainnya -->
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.siswa.template') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-file-download"></i> <span class="d-none d-md-inline">Template</span>
                        </a>

                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                            <i class="fas fa-file-upload"></i> <span class="d-none d-md-inline">Import</span>
                        </button>

                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                            <i class="fas fa-plus"></i> <span class="d-none d-md-inline">Tambah</span>
                        </button>

                        <button type="button" id="btnHapusSemua" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> <span class="d-none d-md-inline">Hapus Semua</span>
                        </button>
                    </div>
                    
                    <form id="formHapusSemua" action="{{ route('admin.siswa.destroyAll') }}" method="POST" style="display:none;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="confirmation" value="HAPUS SEMUA SISWA">
                    </form>
                </div>
            </div>
            
            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="d-none text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat data...</p>
            </div>

            <div class="card-body">
                <div id="siswa-table-container">
                    @include('admin.siswa._table_partial', ['siswa' => $siswa])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
@include('admin.siswa._modals')

@push('scripts')
<script>
// ========== DEKLARASI VARIABEL GLOBAL ==========
let searchTimeout;
let currentPage = 1;
let isLoading = false;
let currentSort = {
    column: '{{ request('sort', 'id') }}',
    direction: '{{ request('dir', 'asc') }}'
};

// ========== MODAL EDIT HANDLER ==========
function openEditModal(id) {
    // Reset form
    const form = document.getElementById('formEdit');
    if (form) form.reset();
    
    // Tampilkan loading
    Swal.fire({
        title: 'Memuat data...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    // Fetch data siswa
    fetch(`/admin/siswa/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success && data.siswa) {
            const s = data.siswa;
            
            // Isi form dengan data
            document.getElementById('edit_nisn').value = s.nisn || '';
            document.getElementById('edit_nama').value = s.nama || '';
            document.getElementById('edit_tempat_lahir').value = s.tempat_lahir || '';
            document.getElementById('edit_tanggal_lahir').value = s.tanggal_lahir || '';
            document.getElementById('edit_jenis_kelamin').value = s.jenis_kelamin || '';
            document.getElementById('edit_diterima_tanggal').value = s.diterima_tanggal || '';
            document.getElementById('edit_status_keluarga').value = s.status_keluarga || '';
            document.getElementById('edit_agama').value = s.agama || '';
            document.getElementById('edit_alamat').value = s.alamat || '';
            document.getElementById('edit_nama_ayah').value = s.nama_ayah || '';
            document.getElementById('edit_pekerjaan_ayah').value = s.pekerjaan_ayah || '';
            document.getElementById('edit_nama_ibu').value = s.nama_ibu || '';
            document.getElementById('edit_pekerjaan_ibu').value = s.pekerjaan_ibu || '';
            document.getElementById('edit_no_hp_ortu').value = s.no_hp_ortu || '';
            document.getElementById('edit_kelas_id').value = s.kelas_id || '';
            
            // Set form action
            form.action = `/admin/siswa/${s.id}`;
            
            // Tampilkan foto preview
            const fotoPreview = document.getElementById('edit_foto_preview');
            if (s.foto) {
                fotoPreview.src = `/img/foto_siswa/${s.foto}`;
                fotoPreview.style.display = 'block';
            } else {
                fotoPreview.src = '/img/logo.png';
                fotoPreview.style.display = 'block';
            }
            
            // Tampilkan modal
            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
            modal.show();
            
        } else {
            Swal.fire('Error', data.message || 'Gagal memuat data', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
        console.error('Error:', error);
    });
}

// ========== FORM SUBMIT HANDLER ==========
function handleFormSubmit(event, formId) {
    event.preventDefault();
    
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const isEdit = formId === 'formEdit';
    
    // Tampilkan loading
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.close();
            
            // Tutup modal
            const modalId = isEdit ? 'modalEdit' : 'modalCreate';
            const modalEl = document.getElementById(modalId);
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            
            // Reset form create
            if (!isEdit) {
                form.reset();
            }
            
            // Tampilkan sukses
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Data berhasil disimpan',
                timer: 1500,
                showConfirmButton: false
            });
            
            // Reload data setelah 1 detik
            setTimeout(() => {
                loadData(currentPage);
            }, 1000);
            
        } else {
            Swal.close();
            
            // Tampilkan error validasi
            if (data.errors) {
                let errorMessage = '<ul class="text-start">';
                Object.values(data.errors).forEach(errors => {
                    errors.forEach(error => {
                        errorMessage += `<li>${error}</li>`;
                    });
                });
                errorMessage += '</ul>';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Error',
                    html: errorMessage,
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire('Error', data.message || 'Gagal menyimpan data', 'error');
            }
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
        console.error('Error:', error);
    });
}

// ========== INITIALIZE FORM HANDLERS ==========
document.addEventListener('DOMContentLoaded', function() {
    // Form Create Submit
    const formCreate = document.getElementById('formCreate');
    if (formCreate) {
        formCreate.addEventListener('submit', function(e) {
            handleFormSubmit(e, 'formCreate');
        });
    }
    
    // Form Edit Submit
    const formEdit = document.getElementById('formEdit');
    if (formEdit) {
        formEdit.addEventListener('submit', function(e) {
            handleFormSubmit(e, 'formEdit');
        });
    }
    
    // Form Import Submit
    const formImport = document.querySelector('#modalImport form');
    if (formImport) {
        formImport.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Mengimport data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: this.method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    // Tutup modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalImport'));
                    if (modal) modal.hide();
                    
                    // Reset form
                    this.reset();
                    
                    // Tampilkan sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Data berhasil diimport',
                        confirmButtonText: 'OK'
                    });
                    
                    // Reload data
                    setTimeout(() => {
                        loadData(1);
                    }, 1000);
                    
                } else {
                    Swal.fire('Error', data.message || 'Gagal mengimport data', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                console.error('Error:', error);
            });
        });
    }
    
    // Reset form create saat modal ditutup
    const modalCreate = document.getElementById('modalCreate');
    if (modalCreate) {
        modalCreate.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('formCreate');
            if (form) form.reset();
        });
    }
});

// ========== FUNGSI SORTING ==========
function updateSortingIcons() {
    document.querySelectorAll('.sortable').forEach(header => {
        const column = header.getAttribute('data-column');
        const icon = header.querySelector('.sort-icon');
        
        // Reset semua
        header.classList.remove('asc', 'desc');
        if (icon) {
            icon.className = 'fas fa-sort ms-1 sort-icon';
        }
        
        // Set state untuk kolom yang aktif
        if (column === currentSort.column) {
            header.classList.add(currentSort.direction === 'asc' ? 'asc' : 'desc');
            if (icon) {
                icon.className = currentSort.direction === 'asc' 
                    ? 'fas fa-sort-up ms-1 sort-icon' 
                    : 'fas fa-sort-down ms-1 sort-icon';
            }
        }
    });
}

function handleSortClick() {
    const column = this.getAttribute('data-column');
    
    // Toggle direction jika kolom sama
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        // Jika kolom berbeda, set ke asc
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    // Load data dengan sorting baru
    loadData(1);
}

function attachSortingListeners() {
    document.querySelectorAll('.sortable').forEach(header => {
        // Gunakan onclick untuk menghindari duplikasi event listener
        header.onclick = handleSortClick;
    });
    
    updateSortingIcons();
}

// ========== FUNGSI PAGINATION ==========
function attachPaginationListeners() {
    document.querySelectorAll('.pagination a').forEach(link => {
        // Hapus event listener lama jika ada
        link.removeEventListener('click', handlePaginationClick);
        
        // Tambah event listener baru
        link.addEventListener('click', handlePaginationClick);
    });
}

// Fungsi khusus untuk handle klik pagination
function handlePaginationClick(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // Ambil page number dari URL
    const url = new URL(this.href);
    const page = url.searchParams.get('page') || 1;
    
    // Load data dengan page yang dipilih
    loadData(page);
    
    return false;
}

// ========== FUNGSI AJAX LOADING ==========
function loadData(page = 1) {
    if (isLoading) return;
    
    isLoading = true;
    currentPage = page;
    
    // Kumpulkan parameter
    const params = new URLSearchParams();
    
    // Pencarian
    const search = document.getElementById("searchInput").value;
    if (search) params.append('search', search);
    
    // Filter kelas
    const kelasId = document.getElementById("filterKelas").value;
    if (kelasId) params.append('kelas_id', kelasId);
    
    // Jumlah per halaman
    const perPage = document.getElementById("perPageSelect").value;
    if (perPage) params.append('per_page', perPage);
    
    // Halaman
    if (page > 1) params.append('page', page);
    
    // Sorting
    if (currentSort.column) {
        params.append('sort', currentSort.column);
        params.append('dir', currentSort.direction);
    }
    
    // Build URL
    const url = '{{ route("admin.siswa.index") }}?' + params.toString();
    
    // Tampilkan loading
    const loadingIndicator = document.getElementById('loadingIndicator');
    const tableContainer = document.getElementById('siswa-table-container');
    
    if (loadingIndicator) loadingIndicator.classList.remove('d-none');
    if (tableContainer) tableContainer.style.opacity = '0.5';
    
    // Fetch data
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
    })
    .then(data => {
        // Update tabel
        if (tableContainer) {
            tableContainer.innerHTML = data.table || '';
        }
        
        // Update URL browser (tanpa reload)
        if (history.pushState) {
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + params.toString();
            window.history.pushState({path: newUrl}, '', newUrl);
        }
        
        // Update state sorting
        if (data.sort && data.dir) {
            currentSort.column = data.sort;
            currentSort.direction = data.dir;
        }
        
        // Pasang ulang SEMUA event listeners
        setTimeout(() => {
            attachDeleteListeners();
            attachPaginationListeners();
            attachSortingListeners();
            updateSortingIcons();
        }, 100);
        
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data'
        });
    })
    .finally(() => {
        isLoading = false;
        if (loadingIndicator) loadingIndicator.classList.add('d-none');
        if (tableContainer) tableContainer.style.opacity = '1';
    });
}

// ========== FUNGSI PENCARIAN ==========
function handleSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadData(1), 500);
}

// ========== FUNGSI TOMBOL HAPUS ==========
function attachDeleteListeners() {
    document.querySelectorAll('.btnHapusSatuan').forEach(button => {
        button.onclick = function() {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Hapus Data Santri?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formHapusSatuan-' + id);
                    if (form) form.submit();
                }
            });
        };
    });
}

// ========== INISIALISASI ==========
// Cegah inisialisasi berkali-kali
if (!window.siswaPageInitialized) {
    document.addEventListener('DOMContentLoaded', function() {
        // Tandai sudah diinisialisasi
        window.siswaPageInitialized = true;
        
        // Event listeners untuk pencarian dan filter
        document.getElementById('searchInput').addEventListener('input', handleSearch);
        document.getElementById('filterKelas').addEventListener('change', handleSearch);
        document.getElementById('perPageSelect').addEventListener('change', () => loadData(1));
        
        // Tombol hapus semua
        document.getElementById('btnHapusSemua').addEventListener('click', function() {
            Swal.fire({
                title: 'Apakah Anda YAKIN?',
                html: "Tindakan ini akan menghapus <strong>SEMUA data santri</strong>...",
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Ketik HAPUS SEMUA SISWA di sini',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                preConfirm: (inputValue) => {
                    if (inputValue !== 'HAPUS SEMUA SISWA') {
                        Swal.showValidationMessage('Konfirmasi tidak sesuai.');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formHapusSemua').submit();
                }
            });
        });
        
        // Inisialisasi pertama
        attachDeleteListeners();
        attachPaginationListeners();
        attachSortingListeners();
        
        // Support browser back/forward
        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const sort = params.get('sort');
            const dir = params.get('dir');
            
            if (sort && dir) {
                currentSort.column = sort;
                currentSort.direction = dir;
            }
            
            loadData();
        });
    });
}

// ========== NOTIFIKASI SUKSES ==========
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: '{{ session('success') }}',
    showConfirmButton: false,
    timer: 1800,
    toast: true,
    position: 'top-end'
});
@endif
</script>

<style>
/* Style untuk sorting */
.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
    transition: background-color 0.2s ease;
}

.sortable:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.sortable.asc .sort-icon {
    color: #007bff;
}

.sortable.desc .sort-icon {
    color: #007bff;
    transform: rotate(180deg);
}

.sortable .sort-icon {
    transition: transform 0.2s ease, color 0.2s ease;
    font-size: 0.8em;
}

/* Dark mode */
.dark-mode .sortable:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

.dark-mode .sortable.asc .sort-icon,
.dark-mode .sortable.desc .sort-icon {
    color: #4dabf7;
}

/* Loading dan transisi */
#loadingIndicator {
    z-index: 1050;
}

#siswa-table-container {
    transition: opacity 0.3s ease;
    position: relative;
    min-height: 200px;
}

/* Responsif untuk card tools */
@media (max-width: 768px) {
    .card-tools {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    .card-tools input,
    .card-tools select {
        width: 100% !important;
        margin-bottom: 8px !important;
    }
    
    .card-tools .d-flex {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .card-tools .btn {
        flex: 1 1 calc(50% - 4px) !important;
        margin: 2px !important;
        min-width: unset !important;
    }
}

/* Pagination */
.page-item.active .page-link {
    background-color: var(--bs-primary) !important;
    border-color: var(--bs-primary) !important;
}
</style>
@endpush
@endsection