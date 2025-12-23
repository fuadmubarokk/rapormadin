<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Imports\GuruImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    use HasFileUploads;
    
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $guru = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['guru', 'wali']);
            })
            ->orderBy('name', 'asc')
            ->get();
    
        return view('admin.guru.index', compact('guru'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(StoreGuruRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
            
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_guru');
        }

        $user = User::create($data);
        $roleNames = $request->input('roles', []);
        $roleIds = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $user->roles()->attach($roleIds);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan');
    }

    public function edit($id)
    {
        $guru = User::findOrFail($id);
        $guru->load('roles');
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(UpdateGuruRequest $request, $id)
    {
        $guru = User::findOrFail($id);
        $data = $request->all();
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleFileUpload($request->file('foto'), 'img/foto_guru', $guru->foto);
        }
        
        if ($request->hasFile('ttd_wali_kelas')) {
            $data['ttd_wali_kelas'] = $this->handleTtdUpload($request->file('ttd_wali_kelas'), $guru->ttd_wali_kelas, $guru->id);
        }
    
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        
        unset($data['roles']);
        
        $guru->update($data);
        
        $roleNames = $request->input('roles', []);
        $roleIds = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $guru->roles()->sync($roleIds);
        
        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui');
    }

    public function destroy($id)
    {
        if (DB::table('guru_mapel_kelas')->where('guru_id', $id)->exists()) {
            return redirect()->route('admin.guru.index')
                            ->with('error', 'Tidak dapat menghapus Guru ini karena masih memiliki penugasan mengajar.');
        }

        $guru = User::findOrFail($id);
        $guru->delete();

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data Guru berhasil dihapus.');
    }
    
    /**
     * Import data guru dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file'));
            
            return redirect()->route('admin.guru.index')
                ->with('success', 'Data guru berhasil diimpor');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            $errorMessages = '';
            foreach ($failures as $failure) {
                $errorMessages .= 'Baris ke-' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
            }

            return redirect()->route('admin.guru.index')
                ->with('error', 'Import gagal. Periksa kembali file Anda.<br>' . $errorMessages);
        } catch (\Exception $e) {
            return redirect()->route('admin.guru.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
    
    /**
     * Download template untuk import guru
     */
    public function template()
    {
        $fileName = 'template_import_guru.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        
        return response()->download(public_path('templates/' . $fileName), $fileName, $headers);
    }

    /**
     * Upload tanda tangan wali kelas
     */
    public function uploadTtdWaliKelas(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ttd_wali_kelas' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($request->hasFile('ttd_wali_kelas')) {
            if ($user->ttd_wali_kelas) {
                File::delete(public_path('img/ttd/' . $user->ttd_wali_kelas));
            }
            
            $ttd = $request->file('ttd_wali_kelas');
            $filename = 'ttd_wali_kelas_' . $user->id . '_' . time() . '.' . $ttd->getClientOriginalExtension();
            
            $path = public_path('img/ttd');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            
            $ttd->move($path, $filename);
            
            $user->update(['ttd_wali_kelas' => $filename]);
        }

        return redirect()->route('admin.guru.index')
            ->with('success', 'Tanda tangan wali kelas berhasil diupload.');
    }
}