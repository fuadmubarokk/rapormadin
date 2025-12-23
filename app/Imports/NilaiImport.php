<?php

namespace App\Imports;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\GuruMapelKelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class NilaiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    // Gunakan trait ini agar bisa dipanggil dengan Excel::import()
    use \Maatwebsite\Excel\Concerns\Importable;

    protected $guruMapelKelasId;
    protected $semester;
    protected $tahunAjaran;

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cari siswa berdasarkan NISN dari file Excel
        $siswa = Siswa::where('nisn', $row['nisn'])->first();
        if (!$siswa) {
            return null; // Lewati baris jika siswa tidak ditemukan
        }

        // Cek apakah nilai untuk siswa ini di mapel ini sudah ada
        // Jika ada, update data yang lama. Jika belum, buat baru.
        $nilai = Nilai::firstOrNew([
            'siswa_id' => $siswa->id,
            'guru_mapel_kelas_id' => $this->guruMapelKelasId,
            'semester' => $this->semester,
            'tahun_ajaran' => $this->tahunAjaran,
        ]);

        // Isi atau perbarui data nilai
        $nilai->nilai_uas = $row['nilai_uas'];
        $nilai->predikat = $row['predikat'] ?? null; // Gunakan null jika tidak ada
        $nilai->deskripsi = $row['deskripsi'] ?? null;

        $nilai->save(); // Simpan data (create atau update)
        return $nilai;
    }

    /**
     * Mendefinisikan aturan validasi untuk setiap baris
     */
    public function rules(): array
    {
        return [
            'nisn' => 'required|exists:siswa,nisn',
            'nilai_uas' => 'required|integer|min:0|max:100',
            'predikat' => 'nullable|string|in:A,B,C,D,E',
            'deskripsi' => 'nullable|string',
        ];
    }

    /**
     * Custom message untuk validasi
     */
    public function customValidationMessages()
    {
        return [
            'nisn.required' => 'Kolom NISN wajib diisi.',
            'nisn.exists' => 'NISN :input tidak ditemukan di database siswa.',
            'nilai_uas.required' => 'Kolom Nilai UAS wajib diisi.',
            'nilai_uas.integer' => 'Nilai UAS harus berupa angka.',
            'nilai_uas.min' => 'Nilai UAS tidak boleh kurang dari 0.',
            'nilai_uas.max' => 'Nilai UAS tidak boleh lebih dari 100.',
        ];
    }

    /**
     * Jika terjadi error saat proses import (misal database error)
     */
    public function onError(\Throwable $e)
    {
        // Anda bisa log error disini
        // Log::error($e->getMessage());
    }
    
    /**
     * Jika ada baris yang gagal validasi
     */
    public function onFailure(Failure ...$failures)
    {
        // Anda bisa menangani kegagalan validasi di sini
        // Misalnya, simpan ke log untuk ditampilkan ke user
        // Log::error('Import failed on row ' . $failure->row() . ': ' . $failure->errors());
    }
}