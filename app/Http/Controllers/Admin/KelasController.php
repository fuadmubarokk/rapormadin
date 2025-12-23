<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $kelas = Kelas::with('waliKelas')->get();
        $wali = User::whereHas('roles', function($query) {
            $query->where('name', 'wali');
        })->get();
        return view('admin.kelas.index', compact('kelas', 'wali'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'wali_id' => 'nullable|exists:users,id',
            'tingkat' => 'required|string|max:20',
        ]);

        Kelas::create($request->all());

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'wali_id' => 'nullable|exists:users,id',
            'tingkat' => 'required|string|max:20',
        ]);

        $kelas->update($request->all());

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui');
    }

    public function destroy($id)
    {
        if (DB::table('guru_mapel_kelas')->where('kelas_id', $id)->exists()) {
            return redirect()->route('admin.kelas.index')
                            ->with('error', 'Tidak dapat menghapus kelas ini karena masih terdapat data Guru Mapel yang terkait.');
        }

        if (DB::table('siswa')->where('kelas_id', $id)->exists()) {
            return redirect()->route('admin.kelas.index')
                            ->with('error', 'Tidak dapat menghapus kelas ini karena masih terdapat Siswa di dalamnya.');
        }

        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Data kelas berhasil dihapus.');
    }
}