<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingSekolahController extends Controller
{
    use HasFileUploads;
    
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $setting = SettingSekolah::first();
        return view('admin.setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = SettingSekolah::first();
    
        $request->validate([
            'nama_madrasah' => 'required|string|max:255',
            'nama_madrasah_ar' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'sekretariat' => 'nullable|string|max:255',
            'tempat_ttd' => 'nullable|string|max:255',
            'tanggal_rapor' => 'nullable|date',
            'npsn' => 'string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_kepala_madrasah' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kepala_madrasah' => 'required|string|max:255',
        ]);

         $data = $request->all();
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->handleFileUpload($request->file('logo'), 'img/logo', $setting ? $setting->logo : null);
        }
    
        if ($request->hasFile('ttd_kepala_madrasah')) {
            if ($setting && $setting->ttd_kepala_madrasah) {
                File::delete(public_path('img/ttd/' . $setting->ttd_kepala_madrasah));
            }
            
            $ttd = $request->file('ttd_kepala_madrasah');
            $filename = 'ttd_kepala_madrasah_' . time() . '.' . $ttd->getClientOriginalExtension();
            
            $path = public_path('img/ttd');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            
            $ttd->move($path, $filename);
            $data['ttd_kepala_madrasah'] = $filename;
        }
    
        if ($setting) {
            $setting->update($data);
        } else {
            SettingSekolah::create($data);
        }
    
        return redirect()->route('admin.setting.index')
            ->with('success', 'Pengaturan sekolah berhasil diperbarui');
    }
    
    /**
     * Upload tanda tangan kepala madrasah.
     */
    public function uploadTtdKepalaMadrasah(Request $request)
    {
        $request->validate([
            'ttd_kepala_madrasah' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        try {
            $setting = SettingSekolah::first();
    
            if ($request->hasFile('ttd_kepala_madrasah')) {
                $ttd = $request->file('ttd_kepala_madrasah');
                $filename = 'ttd_kepala_madrasah_' . time() . '.' . $ttd->getClientOriginalExtension();
                
                $path = public_path('img/ttd');
                
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }
    
                if ($setting && $setting->ttd_kepala_madrasah) {
                    File::delete(public_path('img/ttd/' . $setting->ttd_kepala_madrasah));
                }
                
                $ttd->move($path, $filename);
                
                if ($setting) {
                    $setting->update(['ttd_kepala_madrasah' => $filename]);
                } else {
                    SettingSekolah::create(['ttd_kepala_madrasah' => $filename]);
                }
            }
    
            return redirect()->route('admin.setting.index')
                ->with('success', 'Tanda tangan kepala madrasah berhasil diupload.');
    
        } catch (\Exception $e) {
            return redirect()->route('admin.setting.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}