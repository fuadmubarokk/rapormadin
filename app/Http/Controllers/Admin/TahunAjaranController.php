<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\SettingSekolah;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $setting = SettingSekolah::first();
        $tahunAjaranList = TahunAjaran::orderBy('nama_tahun', 'desc')->get();

        foreach ($tahunAjaranList as $ta) {
            $ta->is_active = ($setting && $setting->tahun_ajaran_id == $ta->id);
        }

        return view('admin.tahun_ajaran.index', compact('tahunAjaranList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        TahunAjaran::where('status', true)->update(['status' => false]);

        $tahunAjaranBaru = TahunAjaran::create([
            'nama_tahun' => $request->nama_tahun,
            'semester' => $request->semester,
            'status' => true,
        ]);

        $setting = SettingSekolah::first();
        if ($setting) {
            $setting->update([
                'tahun_ajaran_id' => $tahunAjaranBaru->id,
            ]);
        } else {
            SettingSekolah::create([
                'tahun_ajaran_id' => $tahunAjaranBaru->id,
            ]);
        }

        return redirect()->route('admin.tahun_ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan dan diaktifkan.');
    }

    public function aktifkan(Request $request, $id)
    {
        TahunAjaran::where('status', true)->update(['status' => false]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update([
            'status' => true,
            'semester' => $request->semester,
        ]);

        $setting = SettingSekolah::first();
        if ($setting) {
            $setting->update([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester' => $request->semester,
            ]);
        } else {
            SettingSekolah::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester' => $request->semester,
            ]);
        }

        return redirect()->route('admin.tahun_ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diaktifkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tahun' => 'required|string|max:255',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update([
            'nama_tahun' => $request->nama_tahun,
            'semester' => $request->semester,
        ]);

        return redirect()->route('admin.tahun_ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        
        if ($tahunAjaran->status) {
            return redirect()->route('admin.tahun_ajaran.index')
                ->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun_ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus');
    }
}