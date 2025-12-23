<?php

namespace App\Exports;

use App\Models\Nilai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NilaiExport implements FromCollection, WithHeadings, WithMapping
{
    // Gunakan trait ini agar bisa dipanggil dengan Excel::download()
    use \Maatwebsite\Excel\Concerns\Exportable;

    protected $guruMapelKelasId;
    protected $semester;
    protected $tahunAjaran;

    /**
     * Constructor untuk menerima data dari controller
     */
    public function __construct($guruMapelKelasId, $semester, $tahunAjaran)
    {
        $this->guruMapelKelasId = $guruMapelKelasId;
        $this->semester = $semester;
        $this->tahunAjaran = $tahunAjaran;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil data nilai berdasarkan parameter yang diberikan
        // Include relasi agar bisa mengakses data siswa dan mapel
        return Nilai::with('siswa', 'guruMapelKelas.mapel')
            ->where('guru_mapel_kelas_id', $this->guruMapelKelasId)
            ->where('semester', $this->semester)
            ->where('tahun_ajaran', $this->tahunAjaran)
            ->get();
    }

    /**
     * @param Nilai $nilai
     * @return array
     */
    public function map($nilai): array
    {
        // Memetakan data dari model ke kolom Excel yang diinginkan
        return [
            $nilai->siswa->nisn,
            $nilai->siswa->nama,
            $nilai->nilai_uas,
            $nilai->predikat,
            $nilai->deskripsi,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Mendefinisikan header untuk setiap kolom di Excel
        return [
            'NISN',
            'Nama Siswa',
            'Nilai UAS',
            'Predikat',
            'Deskripsi',
        ];
    }
}