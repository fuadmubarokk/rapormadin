@extends('layouts.app')

@section('title', 'Data Santri')

@section('breadcrumb', 'Data Santri')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Santri</h3>
                <div class="card-tools d-flex">
                    <!-- Live Search -->
                    <input type="text" id="searchInput" class="form-control form-control-sm"
                        placeholder="Cari siswa..." style="width: 180px; margin-right:10px;">

                    <!-- Filter Kelas -->
                    <select id="filterKelas" class="form-control form-control-sm"
                        style="width:150px; margin-right:10px;">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <a href="{{ route('admin.siswa.template') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-file-download"></i> Template
                    </a>

                    <button type="button" class="btn btn-info btn-sm ml-1" data-toggle="modal" data-target="#modalImport">
                        <i class="fas fa-file-upload"></i> Import
                    </button>

                    <button type="button" class="btn btn-primary btn-sm ml-1" data-toggle="modal" data-target="#modalCreate">
                        <i class="fas fa-plus"></i> Tambah Santri
                    </button>

                    <button type="button" id="btnHapusSemua" class="btn btn-danger btn-sm ml-1">
                        <i class="fas fa-trash"></i> Hapus Semua
                    </button>
                    
                    <form id="formHapusSemua" action="{{ route('admin.siswa.destroyAll') }}" method="POST" style="display:none;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="confirmation" value="HAPUS SEMUA SISWA">
                    </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:50px;">No</th>
                            {{-- TAMBAHKAN KOLOM FOTO --}}
                            <th style="width:50px;">Foto</th>
                            <th class="sortable" data-column="1">
                                NISN
                                <i class="fas fa-sort ml-1 sort-icon"></i>
                            </th>
                            <th class="sortable" data-column="2">
                                Nama
                                <i class="fas fa-sort ml-1 sort-icon"></i>
                            </th>
                            <th class="sortable" data-column="3">
                                Kelas
                                <i class="fas fa-sort ml-1 sort-icon"></i>
                            </th>
                            <th style="width:130px;">Aksi</th>
                        </tr>
                    </thead>
                    <!-- EDIT: Beri ID agar bisa diganti dengan AJAX -->
                    <tbody id="siswa-table-body">
                        @foreach($siswa as $s)
                        <tr>
                            <!-- EDIT: Perbaiki penomoran -->
                            <td>{{ ($siswa->currentPage() - 1) * $siswa->perPage() + $loop->iteration }}</td>
                            {{-- TAMBAHKAN SEL FOTO --}}
                            <td style="text-align: center; vertical-align: middle;">
                                {{-- 
                                    PERUBAHAN STYLING FOTO
                                    - Menghapus class 'img-circle' agar foto 3x4 tidak terpotong.
                                    - Menggunakan 'max-height' untuk menjaga rasio asli.
                                    - Menambahkan border dan border-radius agar lebih rapi.
                                --}}
                                <img src="{{ !empty($s->foto) ? asset('img/foto_siswa/' . $s->foto) : asset('img/logo.png') }}" style="max-height: 40px; width: auto; border-radius:5px; border: 1px solid #ddd;" alt="Foto Siswa">
                            </td>
                            <td>{{ $s->nisn }}</td>
                            <td>{{ $s->nama }}</td>
                            <td>{{ $s->kelas->nama_kelas }}</td>
                            <td>
                                <a href="{{ route('admin.siswa.edit', $s->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-danger btn-sm btnHapusSatuan"
                                        data-id="{{ $s->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <form id="formHapusSatuan-{{ $s->id }}" 
                                    action="{{ route('admin.siswa.destroy', $s->id) }}" 
                                    method="POST" 
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- EDIT: Beri ID agar bisa diganti dengan AJAX -->
                <div id="pagination-container" class="mt-3 d-flex justify-content-end">
                    {{ $siswa->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal dan lain-lain tidak berubah -->
@include('admin.siswa._modals')

<script>
// EDIT: Fungsi global untuk memuat halaman baru via AJAX
function loadPage(url) {
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Update isi tabel
            document.getElementById('siswa-table-body').innerHTML = data.table;
            // Update isi pagination
            document.getElementById('pagination-container').innerHTML = data.pagination;
            // Update URL di browser
            history.pushState({}, '', url);
            // Pasang kembali event listener untuk tombol hapus yang baru
            attachDeleteListeners();
        })
        .catch(error => console.error('Error:', error));
}

// EDIT: Fungsi untuk menempelkan event listener ke tombol hapus
function attachDeleteListeners() {
    document.querySelectorAll('.btnHapusSatuan').forEach(button => {
        // Hapus listener lama untuk mencegah duplikasi
        button.removeEventListener('click', handleDeleteClick);
        // Tambahkan listener baru
        button.addEventListener('click', handleDeleteClick);
    });
}

// EDIT: Fungsi untuk menangani klik tombol hapus
function handleDeleteClick() {
    let id = this.getAttribute('data-id');
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
            document.getElementById('formHapusSatuan-' + id).submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi listener untuk tombol hapus saat pertama kali load
    attachDeleteListeners();

    // EDIT: Event listener untuk pagination (menggunakan event delegation)
    document.getElementById('pagination-container').addEventListener('click', function(e) {
        // Cek apakah yang diklik adalah link (<a>) di dalam div pagination
        if (e.target.closest('a.pagination-link')) { // atau cukup 'a' jika tidak ada class lain
            e.preventDefault();
            loadPage(e.target.closest('a').href);
        }
    });

    // Fungsi untuk filter tabel (tidak berubah)
    function filterTable() {
        let search = document.getElementById("searchInput").value.toLowerCase();
        let kelasFilter = document.getElementById("filterKelas").value;
        let rows = document.querySelectorAll("#siswa-table-body tr");

        rows.forEach(row => {
            let nisn = row.children[2].textContent.toLowerCase(); // Perhatikan indeksnya berubah
            let nama = row.children[3].textContent.toLowerCase(); // Perhatikan indeksnya berubah
            let kelas = row.children[4].textContent.toLowerCase(); // Perhatikan indeksnya berubah

            let matchSearch = (nisn.includes(search) || nama.includes(search));
            let matchKelas = (kelasFilter === "" || kelas == kelasFilter);

            if (matchSearch && matchKelas) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Event listener untuk sorting tabel (tidak berubah)
    document.querySelectorAll("th.sortable").forEach(header => {
        header.addEventListener("click", function () {
            let columnIndex = this.getAttribute("data-column");
            let tableBody = document.querySelector("tbody");
            let rows = Array.from(tableBody.querySelectorAll("tr"));

            let ascending = this.classList.toggle("asc");

            rows.sort((rowA, rowB) => {
                let cellA = rowA.children[columnIndex].textContent.trim().toLowerCase();
                let cellB = rowB.children[columnIndex].textContent.trim().toLowerCase();

                if (!isNaN(cellA) && !isNaN(cellB)) {
                    return ascending ? cellA - cellB : cellB - cellA;
                }
                return ascending
                    ? cellA.localeCompare(cellB)
                    : cellB.localeCompare(cellA);
            });

            rows.forEach(row => tableBody.appendChild(row));
        });
    });

    // Event listener untuk tombol hapus semua (tidak berubah)
    document.getElementById('btnHapusSemua').addEventListener('click', function () {
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

    // Jalankan filter saat input berubah
    document.getElementById("searchInput").addEventListener("keyup", filterTable);
    document.getElementById("filterKelas").addEventListener("change", filterTable);

    // Event listener untuk menampilkan nama file
    document.querySelectorAll('.custom-file-input').forEach(input => {
        input.addEventListener('change', function() {
            const label = this.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                const fileName = this.files[0] ? this.files[0].name : 'Pilih file...';
                label.textContent = fileName;
            }
        });
    });
});

// Tampilkan pesan sukses jika ada
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: '{{ session('success') }}',
    showConfirmButton: false,
    timer: 1800
});
@endif
</script>
@endsection