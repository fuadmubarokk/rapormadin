<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Rapor Madrasah Diniyah')</title>
    <link rel="icon" type="image/png" href="/img/favicon.png">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AdminLTE 3.2 (Support Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Arab -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Responsive CSS (BARU) -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    <style>
        .font-arab {
            font-family: 'Amiri', serif;
            direction: rtl;
        }

        /* ==================== PERBAIKAN UTAMA ==================== */
        /* PERBAIKAN TATA LETAK NAVBAR - MEMAKSA SEJAJAR DALAM SATU BARIS */
        .main-header.navbar {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
        }
        .main-header.navbar .navbar-nav {
            flex-wrap: nowrap !important;
        }

        /* Style untuk mode gelap. AdminLTE 3.2 sudah memiliki dukungan dark-mode yang baik */
        .dark-mode .main-header .navbar {
            background-color: #343a40;
            border-bottom-color: #4b5563;
        }
        .dark-mode .main-sidebar {
            background-color: #343a40;
        }
        .dark-mode .content-wrapper, .dark-mode .content-header {
            background-color: #4b5563;
            color: #e9ecef;
        }
        body.dark-mode {
            background-color: #1a1a1a;
            color: #e9ecef;
        }
        /* Tambahan style untuk mode gelap agar lebih konsisten */
        body.dark-mode .card {
            background-color: #343a40;
            border-color: #495057;
        }
        body.dark-mode .card-header {
            background-color: #495057;
            border-bottom-color: #6c757d;
        }
        body.dark-mode .card-title {
            color: #f8f9fa;
        }
        body.dark-mode .table {
            color: #e9ecef;
        }
        body.dark-mode .table-bordered,
        body.dark-mode .table-bordered td,
        body.dark-mode .table-bordered th {
            border-color: #495057;
        }
        body.dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.1);
        }
        body.dark-mode .table thead th {
            background-color: #495057;
            border-color: #6c757d;
            color: #f8f9fa;
        }
        body.dark-mode .text-muted {
            color: #adb5bd !important;
        }
        body.dark-mode .breadcrumb {
            background-color: transparent;
        }
        body.dark-mode .main-footer {
            background-color: #343a40;
            border-top-color: #4b5563;
            color: #e9ecef;
        }
        /* ========================================================= */

        /* Animasi untuk dropdown */
        .dropdown-menu {
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity:0; transform: translateY(-10px); }
            to { opacity:1; transform: translateY(0); }
        }
        /* Sembunyikan panah tapi tetap biarkan elemennya ada */
        .nav-sidebar .nav-item .right {
            opacity:0;
            pointer-events: none;
        }
        /* Style untuk user-header (PERBAIKAN WARNA GRADIENT) */
        .user-header {
            background: linear-gradient(to right, #007bff, #0056b3) !important; /* Warna yang lebih standar */
        }
        
        /* PERBAIKAN: Style untuk responsive table */
        @media (min-width:768px) {
            .table-responsive thead {
                display: table-header-group !important;
            }
        }
        
        /* Background transparan dengan latar belakang putih semi-transparan */
        .custom-preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.85);
        }

        .dark-mode .custom-preloader {
            background: rgba(0,0,0,.7);
        }

        .ring-loader {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(0,0,0,.2);
            border-top-color: #000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .dark-mode .ring-loader {
            border-color: rgba(255,255,255,.2);
            border-top-color: #fff;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ========================================================= */
    </style>
    @stack('styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<!-- PERBAIKAN: MENAMBAHKAN KELAS DARK MODE DARI SERVER -->
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed @if(auth()->check() && auth()->user()->isDarkMode()) dark-mode @endif">
<div class="wrapper">
    <!-- PERUBAHAN: HTML Preloader -->
    <div class="custom-preloader flex-column justify-content-center align-items-center">
        <div class="ring-loader"></div>
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ms-auto"> <!-- PERUBAHAN: ml-auto menjadi ms-auto (Bootstrap 5) -->

            <!-- User Menu Dropdown -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false"> <!-- PERUBAHAN: data-toggle -> data-bs-toggle -->
                    <!-- Menggunakan URL placeholder untuk gambar default agar tidak error jika file tidak ada -->
                    <img src="{{ auth()->user()->foto ? asset('img/foto_guru/' . auth()->user()->foto) : asset('img/profil.png') }}" class="user-image img-circle elevation-2" alt="User Image">
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <!-- PERUBAHAN: dropdown-menu-right -> dropdown-menu-end -->
                    <!-- User image -->
                    <li class="user-header bg-gradient-primary">
                        <img src="{{ auth()->user()->foto ? asset('img/foto_guru/' . auth()->user()->foto) : asset('img/profil.png') }}" class="img-circle elevation-2" alt="User Image">
                        <p>
                            {{ auth()->user()->name }}
                            <small>{{ ucfirst(auth()->user()->role) }}</small>
                        </p>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <a href="{{ route('profile.edit') }}" class="btn btn-default btn-flat btn-block">
                                    <i class="fas fa-user me-1"></i> Profil Saya <!-- PERUBAHAN: mr-1 -> me-1 -->
                                </a>
                            </div>
                        </div>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-default btn-flat">Keluar</button>
                        </form>
                    </li>
                </ul>
            </li>
            
            <!-- Dark Mode Toggle -->
            <li class="nav-item">
                <a class="nav-link" href="#" id="dark-mode-toggle" role="button">
                    <i class="fas fa-moon"></i>
                </a>
            </li>

            <!-- Fullscreen -->
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('img/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Rapor Madrasah</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column"
                    data-widget="treeview"
                    role="menu"
                    data-accordion="false">

                    <!-- Menu Admin -->
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Data Ustadz/ah -->
                    <li class="nav-item">
                        <a href="{{ route('admin.guru.index') }}"
                           class="nav-link {{
                               request()->routeIs('admin.guru.index')
                               || request()->routeIs('admin.guru.create')
                               || request()->routeIs('admin.guru.edit')
                               ? 'active' : ''
                           }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data Ustadz/ah</p>
                        </a>
                    </li>

                    <!-- Data Kelas -->
                    <li class="nav-item">
                        <a href="{{ route('admin.kelas.index') }}"
                           class="nav-link {{ request()->routeIs('admin.kelas*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-school"></i>
                            <p>Data Kelas</p>
                        </a>
                    </li>

                    <!-- Data Santri -->
                    <li class="nav-item">
                        <a href="{{ route('admin.siswa.index') }}"
                           class="nav-link {{ request()->routeIs('admin.siswa*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-graduate"></i>
                            <p>Data Santri</p>
                        </a>
                    </li>
                    
                    <!-- Data Mapel -->
                    <li class="nav-item">
                        <a href="{{ route('admin.mapel.index') }}"
                           class="nav-link {{ request()->routeIs('admin.mapel*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Data Mapel</p>
                        </a>
                    </li>
                    
                    <!-- Penugasan Ustadz/ah -->
                    <li class="nav-item">
                        <a href="{{ route('admin.guru_mapel_kelas.index') }}"
                           class="nav-link {{ request()->routeIs('admin.guru_mapel_kelas*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-link"></i>
                            <p>Penugasan Ustadz/ah</p>
                        </a>
                    </li>
                    
                    <!-- Tahun Ajaran -->
                    <li class="nav-item">
                        <a href="{{ route('admin.tahun_ajaran.index') }}"
                           class="nav-link {{ request()->routeIs('admin.tahun_ajaran*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Tahun Ajaran</p>
                        </a>
                    </li>
                    
                    <!-- Pengaturan -->
                    <li class="nav-item">
                        <a href="{{ route('admin.setting.index') }}"
                           class="nav-link {{ request()->routeIs('admin.setting*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan</p>
                        </a>
                    </li>

                    @endif

                    <!-- Menu Gabungan untuk Guru dan Wali Kelas -->
                    @if(auth()->user()->isGuru() || auth()->user()->isWaliKelas())
                    
                        <!-- Menu Dashboard (Satu untuk Keduanya) -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Menu Khusus Guru -->
                        @if(auth()->user()->isGuru())
                        <li class="nav-item has-treeview {{ request()->routeIs('guru.nilai*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('guru.nilai*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-edit"></i>
                                <p>
                                    Input Nilai
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @foreach(auth()->user()->guruMapelKelas as $gmk)
                                <li class="nav-item">
                                    <a href="{{ route('guru.nilai.index', $gmk->id) }}" class="nav-link {{ request()->is('guru/nilai/'.$gmk->id) ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ $gmk->mapel->nama_mapel }} - {{ $gmk->kelas->nama_kelas }}</p>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @endif

                        <!-- Menu Khusus Wali Kelas -->
                        @if(auth()->user()->isWaliKelas())
                            @foreach ([
                                'wali.nilai_non_tulis' => ['icon' => 'fas fa-book-open', 'label' => 'Nilai Non Tulis'],
                                'wali.karakter'       => ['icon' => 'fas fa-user-check', 'label' => 'Penilaian Karakter'],
                                'wali.absensi'        => ['icon' => 'fas fa-calendar-check', 'label' => 'Absensi'],
                                'wali.catatan'        => ['icon' => 'fas fa-sticky-note', 'label' => 'Catatan Rapor']
                            ] as $key => $menu)
                        
                                <li class="nav-item">
                                    <a href="{{ route($key.'.index') }}"
                                       class="nav-link {{ request()->routeIs($key.'*') ? 'active' : '' }}">
                                        <i class="nav-icon {{ $menu['icon'] }}"></i>
                                        <p>{{ $menu['label'] }}</p>
                                    </a>
                                </li>
                        
                            @endforeach
                        @endif
                    @endif

                    <!-- ========================================================= -->
                    <!-- MENU RAPOR UNTUK ADMIN & WALI KELAS -->
                    <!-- ========================================================= -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isWaliKelas())
                    <li class="nav-item">
                        <a href="{{ route('rapor.index') }}" class="nav-link {{ request()->routeIs('rapor*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-pdf"></i>
                            <p>Rapor</p>
                        </a>
                    </li>
                    @endif
                    <!-- ========================================================= -->

                    <!-- Logout -->
                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end"> <!-- PERUBAHAN: float-sm-right -> float-sm-end -->
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
        
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert"> <!-- PERUBAHAN: tambah fade show -->
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> <!-- PERUBAHAN: data-dismiss -> data-bs-dismiss -->
                    <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                    {{ session('success') }}
                </div>
                @endif
        
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    {{ session('error') }}
                </div>
                @endif
        
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    <ul class="mb-0"> <!-- PERUBAHAN: tambah mb-0 -->
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
        
                {{-- Tambahan: Error Import Excel --}}
                @if(session('import_errors'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <h5><i class="icon fas fa-ban"></i> Kesalahan Import! ({{ count(session('import_errors')) }} kesalahan)</h5>
                        <ul class="mt-2 mb-0">
                            @foreach(session('import_errors') as $err)
                                <li>
                                    @if(isset($err['nama']) && isset($err['row']))
                                        <b>{{ $err['nama'] }}</b> (Baris {{ $err['row'] }}) :
                                    @else
                                        <b>Baris {{ $err['row'] ?? 'Tidak diketahui' }}</b>:
                                    @endif
                                    
                                    @if(isset($err['errors']) && is_array($err['errors']))
                                        <ul>
                                            @foreach($err['errors'] as $e)
                                                <li>{{ $e }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>{{ $err['errors'] ?? 'Error tidak diketahui' }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
        
                @yield('content')
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <h5>Control Sidebar</h5>
            <p>Sidebar content</p>
        </div>
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2025 <a href="#">Rapor Madrasah Diniyah</a>.</strong>
        All rights reserved.
        <div class="float-end d-none d-sm-inline-block"> <!-- PERUBAHAN: float-right -> float-end -->
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery (tetap dipertahankan untuk kompatibilitas) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap 5 JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- SweetAlert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Skrip mode gelap & notifikasi sholat -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- SKRIP MODE GELAP ---
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const body = document.body;
        const moonIcon = darkModeToggle.querySelector('i');
        
        if (localStorage.getItem('darkMode') === 'true') {
            body.classList.add('dark-mode');
            moonIcon.classList.replace('fa-moon', 'fa-sun');
        }

        darkModeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            
            if (body.classList.contains('dark-mode')) {
                moonIcon.classList.replace('fa-moon', 'fa-sun');
                localStorage.setItem('darkMode', 'true');
            } else {
                moonIcon.classList.replace('fa-sun', 'fa-moon');
                localStorage.setItem('darkMode', 'false');
            }
        });

        // --- SKRIP NOTIFIKASI SHOLAT ---
        const prayerCity = 'Jakarta'; 
        const prayerCountry = 'Indonesia';
        const prayerMethod = 11;

        const prayerNames = {
            Fajr: 'Subuh', Dhuhr: 'Dzuhur', Asr: 'Ashar', Maghrib: 'Maghrib', Isha: 'Isya'
        };

        // --- FUNGSI UTAMA ---
        async function getPrayerTimes() {
            const today = new Date().toISOString().split('T')[0];
            const storageKey = `prayerTimes_${prayerCity}_${today}`;
            const storedData = localStorage.getItem(storageKey);
            if (storedData) {
                return JSON.parse(storedData);
            }
            try {
                const response = await fetch(`https://api.aladhan.com/v1/timingsByCity?city=${prayerCity}&country=${prayerCountry}&method=${prayerMethod}`);
                const data = await response.json();
                if (data.code === 200 && data.status === 'OK') {
                    localStorage.setItem(storageKey, JSON.stringify(data.data.timings));
                    return data.data.timings;
                }
            } catch (error) {
                console.error('Gagal mengambil jadwal sholat:', error);
            }
            return null;
        }

        function showPrayerNotification(prayerName, type) {
            const today = new Date().toISOString().split('T')[0];
            const storageKey = `notif_${type}_shown_${prayerName}_${today}`;

            if (localStorage.getItem(storageKey)) {
                return;
            }

            let title, text, icon;

            switch (type) {
                case 'time':
                    title = `Waktu Sholat ${prayerName} Telah Tiba`;
                    text = 'Mari kita tunaikan ibadah sholat tepat pada waktunya.';
                    icon = 'info';
                    break;
                case 'reminder_5min':
                case 'reminder_10min':
                    title = 'Pengingat Sholat';
                    text = `Apakah Anda sudah sholat ${prayerName}?`;
                    icon = 'question';
                    break;
                default:
                    return;
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonText: 'Baik, terima kasih',
                allowOutsideClick: false,
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });

            localStorage.setItem(storageKey, 'true');
        }

        function checkPrayerTimes(timings) {
            if (!timings) return;

            const now = new Date();
            const currentHour = String(now.getHours()).padStart(2, '0');
            const currentMin = String(now.getMinutes()).padStart(2, '0');
            const currentTime = `${currentHour}:${currentMin}`;

            for (const key in timings) {
                const prayerName = prayerNames[key];
                if (!prayerName) continue;

                const prayerTime = timings[key];

                if (currentTime === prayerTime) {
                    showPrayerNotification(prayerName, 'time');
                }

                const [hour, min] = prayerTime.split(':').map(Number);
                const prayerTimePlus5 = new Date();
                prayerTimePlus5.setHours(hour, min + 5, 0, 0);
                const timePlus5Str = `${String(prayerTimePlus5.getHours()).padStart(2, '0')}:${String(prayerTimePlus5.getMinutes()).padStart(2, '0')}`;
                
                if (currentTime === timePlus5Str) {
                    showPrayerNotification(prayerName, 'reminder_5min');
                }

                const prayerTimePlus10 = new Date();
                prayerTimePlus10.setHours(hour, min + 10, 0, 0);
                const timePlus10Str = `${String(prayerTimePlus10.getHours()).padStart(2, '0')}:${String(prayerTimePlus10.getMinutes()).padStart(2, '0')}`;

                if (currentTime === timePlus10Str) {
                    showPrayerNotification(prayerName, 'reminder_10min');
                }
            }
        }

        // --- EKSEKUSI ---
        getPrayerTimes().then(timings => {
            if (timings) {
                setInterval(() => checkPrayerTimes(timings), 30000); 
                checkPrayerTimes(timings);
            }
        });
    });
</script>

<!-- SweetAlert untuk Notifikasi Flash Session -->
<script>
 $(document).ready(function() {
    // Cek dan tampilkan notifikasi sukses
    @if(session()->has('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    @endif

    // Cek dan tampilkan notifikasi error
    @if(session()->has('error'))
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: '{{ session('error') }}'
        });
    @endif

    // Cek dan tampilkan notifikasi peringatan
    @if(session()->has('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: '{{ session('warning') }}'
        });
    @endif

    // Cek dan tampilkan notifikasi informasi
    @if(session()->has('info'))
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: '{{ session('info') }}'
        });
    @endif
});
</script>

<script>
$(window).on('load', function() {
    setTimeout(function() {
        $('.custom-preloader').fadeOut('slow');
    }, 500);
});
</script>

<!-- Stack untuk skrip dari halaman lain -->
@stack('scripts')

</body>
</html>