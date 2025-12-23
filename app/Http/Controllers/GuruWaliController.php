<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GuruController;
use App\Http\Controllers\WaliController;

class GuruWaliController extends Controller
{
    /**
     * Menampilkan dashboard gabungan untuk user dengan peran Guru dan Wali Kelas.
     */
    public function index()
    {
        // Tidak perlu logika di sini, karena semua logika ada di view
        return view('dashboard');
    }

    /**
     * Mendapatkan data dashboard dari GuruController.
     * Method ini dipanggil dari view untuk menghindari duplikasi kode.
     */
    public function getGuruDashboardData()
    {
        $guruController = new GuruController();
        
        // Buat request palsu untuk method dashboard
        $request = new \Illuminate\Http\Request();
        
        // Panggil method dashboard (asumsikan ada method public)
        // Kita harus membuat method dashboard di GuruController menjadi public agar bisa dipanggil dari sini
        // atau, kita bisa menyalin logikanya langsung di sini jika method-nya sederhana
        $guru = auth()->user();
        $guruMapelKelas = $guru->guruMapelKelas()->with('mapel', 'kelas')->get();
        $tahunAjaran = \App\Models\TahunAjaran::where('status', true)->first();

        return compact('guruMapelKelas', 'tahunAjaran');
    }

    /**
     * Mendapatkan data dashboard dari WaliController.
     * Method ini dipanggil dari view.
     */
    public function getWaliDashboardData()
    {
        // Buat request palsu
        $request = new \Illuminate\Http\Request();
        
        // Buat instance controller
        $waliController = new WaliController();
        
        // Panggil method getWaliData (asumsikan ada method public)
        return $waliController->getWaliData($request);
    }
}