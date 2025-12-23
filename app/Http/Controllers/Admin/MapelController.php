<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Angkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $mapel = Mapel::all();
        $angkatans = Angkatan::orderBy('nama_angkatan', 'asc')->get();
    
        return view('admin.mapel.index', compact('mapel', 'angkatans')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'nama_mapel_ar' => 'required|string|max:100',
            'kategori' => 'required|in:tulis,non-tulis',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        Mapel::create($request->all());

        return redirect()->route('admin.mapel.index')->with('success', 'Data mata pelajaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'nama_mapel_ar' => 'required|string|max:100',
            'kategori' => 'required|in:tulis,non-tulis',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $mapel->update($request->all());

        return redirect()->route('admin.mapel.index')->with('success', 'Data mata pelajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $mapel->delete();

        return redirect()->route('admin.mapel.index')->with('success', 'Data mata pelajaran berhasil dihapus');
    }
    
    /**
     * Menampilkan halaman pengaturan urutan mapel untuk angkatan tertentu.
     */
    public function showMapelUrutan($angkatanId)
    {
        $angkatan = Angkatan::findOrFail($angkatanId);
        
        $mapelDiAngkatan = $angkatan->mapel;
        
        $mapelIdDiAngkatan = $mapelDiAngkatan->pluck('id')->toArray();
        $mapelTersedia = Mapel::whereNotIn('id', $mapelIdDiAngkatan)->get();

        return view('admin.mapel.urutan', compact('angkatan', 'mapelDiAngkatan', 'mapelTersedia'));
    }

    /**
     * Memperbarui urutan mapel atau menambahkan mapel baru ke angkatan.
     */
    public function updateMapelUrutan(Request $request)
    {
        $angkatanId = $request->input('angkatan_id');
        $urutanData = $request->input('urutan', []);

        DB::transaction(function () use ($angkatanId, $urutanData) {
            foreach ($urutanData as $index => $mapelId) {
                DB::table('mapel_angkatan')
                    ->updateOrInsert(
                        ['mapel_id' => $mapelId, 'angkatan_id' => $angkatanId],
                        ['urutan' => $index + 1]
                    );
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Urutan mapel berhasil diperbarui!']);
    }
}