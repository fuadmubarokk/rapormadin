<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaliController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\DashboardController;
// Tambahkan controller baru yang sudah kita pecah
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\GuruMapelKelasController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\SettingSekolahController;
use App\Http\Controllers\Admin\BackupController;

Route::get('/', function () {
    return view('auth.login');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | ROUTE DASHBOARD UTAMA
    |--------------------------------------------------------------------------
    | Semua user (kecuali admin) akan diarahkan ke sini.
    | Controller DashboardController akan menangani logika menampilkan konten
    | yang sesuai (guru, wali, atau keduanya).
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- ROUTE PROFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- ROUTE GROUP ADMIN ---
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Guru Routes
        Route::get('/guru', [AdminGuruController::class, 'index'])->name('guru.index');
        Route::get('/guru/create', [AdminGuruController::class, 'create'])->name('guru.create');
        Route::post('/guru', [AdminGuruController::class, 'store'])->name('guru.store');
        Route::get('/guru/{id}/edit', [AdminGuruController::class, 'edit'])->name('guru.edit');
        Route::put('/guru/{id}', [AdminGuruController::class, 'update'])->name('guru.update');
        Route::delete('/guru/{id}', [AdminGuruController::class, 'destroy'])->name('guru.destroy');
        Route::get('/guru/template', [AdminGuruController::class, 'template'])->name('guru.template');
        Route::post('/guru/import', [AdminGuruController::class, 'import'])->name('guru.import');
        Route::post('/upload-ttd-wali-kelas', [AdminGuruController::class, 'uploadTtdWaliKelas'])->name('upload.ttd.wali');
        
        // Kelas Routes
        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');
        
        // Siswa Routes (DIPERBAIKI)
        Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
        Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
        Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
        Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
        Route::delete('/siswa/destroy-all', [SiswaController::class, 'destroyAll'])->name('siswa.destroyAll');
        Route::get('/siswa/template', [SiswaController::class, 'template'])->name('siswa.template');
        Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
        
        // Mapel Routes
        Route::get('/mapel', [MapelController::class, 'index'])->name('mapel.index');
        Route::post('/mapel', [MapelController::class, 'store'])->name('mapel.store');
        Route::put('/mapel/{id}', [MapelController::class, 'update'])->name('mapel.update');
        Route::delete('/mapel/{id}', [MapelController::class, 'destroy'])->name('mapel.destroy');
        Route::get('/mapel/urutan/{angkatanId}', [MapelController::class, 'showMapelUrutan'])->name('mapel.urutan');
        Route::post('/mapel/urutan/update', [MapelController::class, 'updateMapelUrutan'])->name('mapel.urutan.update');
        
        // Guru Mapel Kelas Routes
        Route::get('/guru-mapel-kelas', [GuruMapelKelasController::class, 'index'])->name('guru_mapel_kelas.index');
        Route::post('/guru-mapel-kelas', [GuruMapelKelasController::class, 'store'])->name('guru_mapel_kelas.store');
        Route::get('/guru-mapel-kelas/{id}/edit', [GuruMapelKelasController::class, 'edit'])->name('guru_mapel_kelas.edit');
        Route::put('/guru-mapel-kelas/{id}', [GuruMapelKelasController::class, 'update'])->name('guru_mapel_kelas.update');
        Route::delete('/guru-mapel-kelas/{id}', [GuruMapelKelasController::class, 'destroy'])->name('guru_mapel_kelas.destroy');
        Route::delete('/guru-mapel-kelas/delete-all', [GuruMapelKelasController::class, 'deleteAll'])->name('guru_mapel_kelas.delete_all');
        Route::get('/guru-mapel-kelas/export', [GuruMapelKelasController::class, 'export'])->name('guru_mapel_kelas.export');
        
        // Tahun Ajaran Routes
        Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun_ajaran.index');
        Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun_ajaran.store');
        Route::post('/tahun-ajaran/{id}/aktifkan', [TahunAjaranController::class, 'aktifkan'])->name('tahun_ajaran.aktifkan');
        Route::delete('/tahun-ajaran/{id}', [TahunAjaranController::class, 'destroy'])->name('tahun_ajaran.destroy');
        Route::put('/tahun-ajaran/{id}', [TahunAjaranController::class, 'update'])->name('tahun_ajaran.update');
        
        // Setting Sekolah Routes
        Route::get('/setting', [SettingSekolahController::class, 'index'])->name('setting.index');
        Route::put('/setting', [SettingSekolahController::class, 'update'])->name('setting.update');
        Route::post('/upload-ttd-kepala-madrasah', [SettingSekolahController::class, 'uploadTtdKepalaMadrasah'])->name('upload.ttd.kepala');
        
        // Backup Routes
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup', [BackupController::class, 'create'])->name('backup.create');
        Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
        Route::delete('/backup/{filename}', [BackupController::class, 'delete'])->name('backup.delete');
    });

    // --- ROUTE GROUP GURU ---
    Route::prefix('guru')->name('guru.')->middleware('guru')->group(function () {
        // Rute dashboard lama dialihkan ke dashboard baru
        Route::get('/dashboard', function () {
            return redirect()->route('dashboard');
        })->name('guru.dashboard');
        
        // Route untuk Nilai
        Route::get('/nilai/{guruMapelKelasId}', [GuruController::class, 'nilaiIndex'])->name('nilai.index');
        Route::post('/nilai', [GuruController::class, 'nilaiStore'])->name('nilai.store');
        Route::post('/nilai/store-all', [GuruController::class, 'nilaiStoreAll'])->name('nilai.store_all');
        Route::put('/nilai/{id}', [GuruController::class, 'nilaiUpdate'])->name('nilai.update');
        Route::delete('/nilai/{nilai}', [GuruController::class, 'nilaiDestroy'])->name('nilai.destroy');
        Route::get('/nilai/{guruMapelKelasId}/export', [GuruController::class, 'nilaiExport'])->name('nilai.export');
        Route::get('/nilai/{guruMapelKelasId}/template', [GuruController::class, 'nilaiTemplate'])->name('nilai.template');
        Route::post('/nilai/import', [GuruController::class, 'nilaiImport'])->name('nilai.import');
    });

    // --- ROUTE GROUP WALI KELAS ---
    Route::prefix('wali')->name('wali.')->middleware('wali')->group(function () {
        // Rute dashboard lama dialihkan ke dashboard baru
        Route::get('/dashboard', function () {
            return redirect()->route('dashboard');
        })->name('wali.dashboard');
        
        // Route untuk Nilai Non Tulis
        Route::get('/nilai-non-tulis', [WaliController::class, 'nilaiNonTulisIndex'])->name('nilai_non_tulis.index');
        Route::post('/nilai-non-tulis', [WaliController::class, 'nilaiNonTulisStore'])->name('nilai_non_tulis.store');
        Route::post('/nilai-non-tulis/store-all', [WaliController::class, 'nilaiNonTulisStoreAll'])->name('nilai_non_tulis.store_all');
        Route::delete('/nilai-non-tulis/{nilaiNonTulis}', [WaliController::class, 'nilaiNonTulisDestroy'])->name('nilai_non_tulis.destroy');
        
        // Route untuk Penilaian Karakter
        Route::get('/karakter', [WaliController::class, 'karakterIndex'])->name('karakter.index');
        Route::post('/karakter', [WaliController::class, 'karakterStore'])->name('karakter.store');
        Route::post('/karakter/store-all', [WaliController::class, 'karakterStoreAll'])->name('karakter.store_all');
        Route::delete('/karakter/{penilaianKarakter}', [WaliController::class, 'karakterDestroy'])->name('karakter.destroy');
        
        // Route untuk Absensi
        Route::get('/absensi', [WaliController::class, 'absensiIndex'])->name('absensi.index');
        Route::post('/absensi', [WaliController::class, 'absensiStore'])->name('absensi.store');
        Route::post('/absensi/store-all', [WaliController::class, 'absensiStoreAll'])->name('absensi.store_all');
        Route::delete('/absensi/{absensi}', [WaliController::class, 'absensiDestroy'])->name('absensi.destroy');

        // Route untuk Catatan Rapor
        Route::get('/catatan', [WaliController::class, 'catatanIndex'])->name('catatan.index');
        Route::post('/catatan', [WaliController::class, 'catatanStore'])->name('catatan.store');
        Route::post('/catatan/store-all', [WaliController::class, 'catatanStoreAll'])->name('catatan.store_all');
        Route::delete('/catatan/{catatanRapor}', [WaliController::class, 'catatanDestroy'])->name('catatan.destroy');
    });

    // --- ROUTE GROUP RAPOR ---
    Route::prefix('rapor')->name('rapor.')->middleware('wali.or.admin')->group(function () {
        Route::get('/', [RaporController::class, 'pilihKelasView'])->name('index');
        Route::get('/data/{kelasId}', [RaporController::class, 'getSiswaData'])->name('data');
        Route::get('/show/{siswaId}', [RaporController::class, 'show'])->name('show');
        Route::get('/cetak/{siswaId}', [RaporController::class, 'cetakRapor'])->name('cetakRapor');
        Route::get('/cetak-cover/{siswaId}', [RaporController::class, 'cetakCover'])->name('cetakCover');
        Route::get('/cetak-semua/{kelasId}', [RaporController::class, 'cetakSemuaRapor'])->name('cetakSemuaRapor');
        Route::get('/cetak-semua-cover/{kelasId}', [RaporController::class, 'cetakSemuaCover'])->name('cetakSemuaCover');
    });
});