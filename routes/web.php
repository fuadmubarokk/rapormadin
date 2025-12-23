<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaliController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
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
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Route untuk Guru
        Route::get('/guru', [AdminController::class, 'guruIndex'])->name('guru.index');
        Route::get('/guru/create', [AdminController::class, 'guruCreate'])->name('guru.create');
        Route::post('/guru', [AdminController::class, 'guruStore'])->name('guru.store');
        Route::get('/guru/{id}/edit', [AdminController::class, 'guruEdit'])->name('guru.edit');
        Route::put('/guru/{id}', [AdminController::class, 'guruUpdate'])->name('guru.update');
        Route::delete('/guru/{id}', [AdminController::class, 'guruDestroy'])->name('guru.destroy');
        Route::get('/guru/template', [AdminController::class, 'guruTemplate'])->name('guru.template');
        Route::post('/guru/import', [AdminController::class, 'guruImport'])->name('guru.import');
        
        // Route untuk Kelas
        Route::get('/kelas', [AdminController::class, 'kelasIndex'])->name('kelas.index');
        Route::post('/kelas', [AdminController::class, 'kelasStore'])->name('kelas.store');
        Route::put('/kelas/{id}', [AdminController::class, 'kelasUpdate'])->name('kelas.update');
        Route::delete('/kelas/{id}', [AdminController::class, 'kelasDestroy'])->name('kelas.destroy');
        
        // Route untuk Siswa
        Route::get('/siswa', [AdminController::class, 'siswaIndex'])->name('siswa.index');
        Route::post('/siswa', [AdminController::class, 'siswaStore'])->name('siswa.store');
        Route::delete('/siswa/destroy-all', [AdminController::class, 'siswaDestroyAll'])->name('siswa.destroyAll');
        Route::get('/siswa/{id}/edit', [AdminController::class, 'siswaEdit'])->name('siswa.edit');
        Route::put('/siswa/{id}', [AdminController::class, 'siswaUpdate'])->name('siswa.update');
        Route::delete('/siswa/{id}', [AdminController::class, 'siswaDestroy'])->name('siswa.destroy');
        Route::get('/siswa/template', [AdminController::class, 'siswaTemplate'])->name('siswa.template');
        Route::post('/siswa/import', [AdminController::class, 'siswaImport'])->name('siswa.import');
 
        // Route untuk Mapel
        Route::get('/mapel', [AdminController::class, 'mapelIndex'])->name('mapel.index');
        Route::post('/mapel', [AdminController::class, 'mapelStore'])->name('mapel.store');
        Route::put('/mapel/{id}', [AdminController::class, 'mapelUpdate'])->name('mapel.update');
        Route::delete('/mapel/{id}', [AdminController::class, 'mapelDestroy'])->name('mapel.destroy');
        
        Route::get('/mapel/urutan/{angkatanId}', [AdminController::class, 'showMapelUrutan'])->name('mapel.urutan');
        Route::post('/mapel/urutan/update', [AdminController::class, 'updateMapelUrutan'])->name('mapel.urutan.update');
        
        // --- ROUTE UNTUK PENUGASAN GURU (YANG SUDAH DIPERBAIKI) ---
        Route::get('/guru-mapel-kelas', [AdminController::class, 'guruMapelKelasIndex'])->name('guru_mapel_kelas.index');
        Route::post('/guru-mapel-kelas', [AdminController::class, 'guruMapelKelasStore'])->name('guru_mapel_kelas.store');
        Route::get('/guru-mapel-kelas/{id}/edit', [AdminController::class, 'guruMapelKelasEdit'])->name('guru_mapel_kelas.edit');
        Route::put('/guru-mapel-kelas/{id}', [AdminController::class, 'guruMapelKelasUpdate'])->name('guru_mapel_kelas.update');
        Route::delete('/guru-mapel-kelas/{id}', [AdminController::class, 'guruMapelKelasDestroy'])->name('guru_mapel_kelas.destroy');
        
        // Route untuk hapus semua dan export (URL dan nama sudah konsisten)
        Route::delete('/guru-mapel-kelas/delete-all', [AdminController::class, 'guruMapelKelasDeleteAll'])->name('guru_mapel_kelas.delete_all');
        Route::get('/guru-mapel-kelas/export', [AdminController::class, 'guruMapelKelasExport'])->name('guru_mapel_kelas.export');
        
        // Route untuk Tahun Ajaran
        Route::get('/tahun-ajaran', [AdminController::class, 'tahunAjaranIndex'])->name('tahun_ajaran.index');
        Route::post('/tahun-ajaran', [AdminController::class, 'tahunAjaranStore'])->name('tahun_ajaran.store');
        Route::post('/tahun-ajaran/{id}/aktifkan', [AdminController::class, 'tahunAjaranAktifkan'])->name('tahun_ajaran.aktifkan');
        Route::delete('/tahun-ajaran/{id}', [AdminController::class, 'tahunAjaranDestroy'])->name('tahun_ajaran.destroy');
        Route::put('/tahun-ajaran/{id}', [AdminController::class, 'tahunAjaranUpdate'])->name('tahun_ajaran.update');

        // Route untuk Setting Sekolah
        Route::get('/setting', [AdminController::class, 'settingSekolahIndex'])->name('setting.index');
        Route::put('/setting', [AdminController::class, 'settingSekolahUpdate'])->name('setting.update');
        
        // Route untuk Upload Tanda Tangan (TAMBAHAN BARU)
        Route::post('/upload-ttd-kepala-madrasah', [AdminController::class, 'uploadTtdKepalaMadrasah'])->name('upload.ttd.kepala');
        Route::post('/upload-ttd-wali-kelas', [AdminController::class, 'uploadTtdWaliKelas'])->name('upload.ttd.wali');

        // Route untuk Backup
        Route::get('/backup', [AdminController::class, 'listBackup'])->name('backup.index');
        Route::post('/backup', [AdminController::class, 'backupDatabase'])->name('backup.create');
        Route::get('/backup/download/{filename}', [AdminController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{filename}', [AdminController::class, 'deleteBackup'])->name('backup.delete');
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