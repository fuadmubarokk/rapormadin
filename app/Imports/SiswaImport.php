<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public array $customErrors = [];

    /**
     * @var \Illuminate\Support\Collection
     */
    private $kelasCache;

    public function __construct()
    {
        $this->kelasCache = Kelas::pluck('id', 'nama_kelas');
    }

    /**
     * --- PERUBAHAN PENTING 1: Paksa NISN dan Tanggal menjadi string/tanggal yang benar ---
     * Fungsi ini dijalankan SEBELUM validasi.
     */
    public function prepareForValidation(array $row, int $index)
    {
        // Paksa NISN selalu menjadi string, meskipun di Excel berupa angka
        if (isset($row['nisn'])) {
            $row['nisn'] = (string) $row['nisn'];
        }

        // Coba konversi tanggal_lahir ke format Y-m-d agar validasi 'date' Laravel bisa membacanya
        if (isset($row['tanggal_lahir'])) {
            $row['tanggal_lahir'] = $this->transformDate($row['tanggal_lahir']);
        }

        // --- TAMBAHKAN: Konversi diterima_tanggal ---
        if (isset($row['diterima_tanggal'])) {
            $row['diterima_tanggal'] = $this->transformDate($row['diterima_tanggal']);
        }

        return $row;
    }

    /**
     * Definisikan aturan validasi untuk setiap baris.
     */
    public function rules(): array
    {
        return [
            'nisn'            => 'required|string|unique:siswa,nisn',
            'nama'            => 'required|string|max:255',
            'tempat_lahir'    => 'required|string|max:100',
            'tanggal_lahir'   => 'required|date',
            'nama_kelas'      => 'required|string|max:50',
            'tingkat'         => 'required|string|max:20',
            // --- TAMBAHKAN VALIDASI UNTUK DUA KOLOM BARU INI ---
            'diterima_tanggal' => 'required|date',
            'status_keluarga' => 'required|string|max:255',
        ];
    }

    /**
     * --- PERUBAHAN PENTING 2: Pesan error dalam Bahasa Indonesia ---
     * Ini adalah kamus untuk mengubah pesan error default Laravel.
     */
    public function messages(): array
    {
        return [
            'nisn.required'           => 'NISN wajib diisi.',
            'nisn.string'             => 'NISN harus berupa teks.',
            'nisn.unique'             => 'NISN :input sudah terdaftar.',
            'nama.required'           => 'Nama wajib diisi.',
            'tempat_lahir.required'   => 'Tempat Lahir wajib diisi.',
            'tanggal_lahir.required'  => 'Tanggal Lahir wajib diisi.',
            'tanggal_lahir.date'      => 'Format Tanggal Lahir tidak valid.',
            'nama_kelas.required'     => 'Nama Kelas wajib diisi.',
            'tingkat.required'        => 'Tingkat wajib diisi.',
            // --- TAMBAHKAN PESAN ERROR UNTUK DUA KOLOM BARU INI ---
            'diterima_tanggal.required' => 'Tanggal Diterima wajib diisi.',
            'diterima_tanggal.date' => 'Format Tanggal Diterima tidak valid.',
            'status_keluarga.required' => 'Status Keluarga wajib diisi.',
        ];
    }

    /**
     * --- PERUBAHAN PENTING 3: Nama atribut yang ramah ---
     * Mengubah 'nisn' menjadi 'NISN', 'tanggal_lahir' menjadi 'Tanggal Lahir', dst.
     */
    public function customValidationAttributes()
    {
        return [
            'nisn'           => 'NISN',
            'nama'           => 'Nama',
            'tempat_lahir'   => 'Tempat Lahir',
            'tanggal_lahir'  => 'Tanggal Lahir',
            'nama_kelas'     => 'Nama Kelas',
            'tingkat'        => 'Tingkat',
            // --- TAMBAHKAN ATRIBUT RAMAH UNTUK DUA KOLOM BARU INI ---
            'diterima_tanggal' => 'Tanggal Diterima',
            'status_keluarga' => 'Status Keluarga',
        ];
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $kelasId = $this->findOrCreateKelas($row['nama_kelas'], $row['tingkat']);

        if (!$kelasId) {
            return null;
        }

        return new Siswa([
            'nisn'            => $row['nisn'],
            'nama'            => $row['nama'],
            'tempat_lahir'    => $row['tempat_lahir'],
            'tanggal_lahir'   => $row['tanggal_lahir'],
            'jenis_kelamin'   => $row['jenis_kelamin'] ?? null,
            'agama'           => $row['agama'] ?? null,
            'alamat'          => $row['alamat'] ?? null,
            'nama_ayah'       => $row['nama_ayah'] ?? null,
            'nama_ibu'        => $row['nama_ibu'] ?? null,
            'pekerjaan_ayah'  => $row['pekerjaan_ayah'] ?? null,
            'pekerjaan_ibu'   => $row['pekerjaan_ibu'] ?? null,
            'no_hp_ortu'      => $row['no_hp_ortu'] ?? null,
            'kelas_id'        => $kelasId,
            // --- TAMBAHKAN DUA KOLOM BARU INI KE MODEL ---
            'diterima_tanggal' => $row['diterima_tanggal'],
            'status_keluarga' => $row['status_keluarga'],
        ]);
    }

    /**
     * Fungsi untuk mencari atau membuat kelas baru.
     */
    private function findOrCreateKelas(string $namaKelas, string $tingkat): ?int
    {
        if ($this->kelasCache->has($namaKelas)) {
            return $this->kelasCache->get($namaKelas);
        }

        try {
            $newKelas = Kelas::create([
                'nama_kelas' => $namaKelas,
                'tingkat'    => $tingkat,
            ]);
            $this->kelasCache->put($namaKelas, $newKelas->id);
            return $newKelas->id;
        } catch (\Exception $e) {
            // Mengambil nama dari baris saat ini untuk pesan error
            $currentRowData = request()->all();
            $rowName = $currentRowData['nama'] ?? '(Nama tidak diisi)';

            $this->customErrors[] = [
                'nama' => $rowName,
                'row'  => request()->input('row', 'Tidak diketahui'),
                'errors' => ['Gagal membuat kelas "' . $namaKelas . '": ' . $e->getMessage()]
            ];
            return null;
        }
    }

    /**
     * Transformasi tanggal dari berbagai format ke Y-m-d.
     */
    private function transformDate($value, string $format = 'Y-m-d')
    {
        try {
            if (empty($value)) return null;
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format($format);
            }
            if (is_string($value)) {
                $value = str_replace('/', '-', $value);
                return \Carbon\Carbon::createFromFormat('d-m-Y', $value)->format($format);
            }
        } catch (\Exception $e) {
            return $value;
        }
        return null;
    }

    /**
     * Kumpulkan semua error validasi.
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $f) {
            $row = $f->row();
            $values = $f->values();
            $nama = $values['nama'] ?? '(Nama tidak diisi)';

            $this->customErrors[] = [
                'nama' => $nama,
                'row'  => $row,
                'errors' => $f->errors()
            ];
        }
    }
}