<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rapor | {{ $siswa->nama }}</title>
    <style>
        body {
            font-family: 'amiri', 'dejavusans', sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }
        .table-ttd, .table-ttd td {
            border: none !important;
        }
        .table-nilai {
            border-collapse: collapse;
            width: 100%;
            font-size:13px;
        }
        .table-nilai th, .table-nilai td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .table-nilai th {
            background-color: #f2f2f2;
        }
        .table-nilai .rtl {
            direction: rtl;
            text-align: right;
        }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background: #f0f0f0;
        }
        .judul-section {
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
        }
        .text-right { text-align: right; }
        .rtl {
            direction: rtl;
            unicode-bidi: isolate-override;
            font-family: 'traditionalarabic';
            font-size: 16px;
            line-height: 1.6;
        }
        hr { border: 5px solid #000000; margin-top: 0px; margin-bottom: 0px; }
        
        /* CSS untuk Biodata */
        .biodata-container {
            padding: 3px;
        }

        /* CSS untuk memastikan header rata tengah */
        .table-nilai th.rtl {
            text-align: center !important;
        }

        /* TAMBAHKAN CSS BARU INI */
        .judul-utama {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        
        .terbilang-kecil {
            font-size: 11px;
            font-style: italic;
            text-align: left;
        }
        
        /* CSS untuk nilai di bawah KKM */
        .nilai-bawah-kkm {
            color: red;
        }

        /* ==================== */
        /* CSS UNTUK TABEL CATATAN UTAMA (DIPERBAIKI) */
        /* ==================== */
        
        .table-unified-catatan {
            margin-top: 20px;
            font-size: 12px;
            table-layout: fixed;
            border-collapse: collapse;
            border: none;
        }
        
        
        /* --- Gaya umum untuk sel --- */
        .table-unified-catatan .label {
            font-weight: bold;
            text-align: left;
            white-space: nowrap;
        }
        
        .table-unified-catatan .content {
            text-align: left;
        }
    </style>
</head>
<body>
    {{-- ========================= --}}
    {{-- DATA SANTRI --}}
    {{-- ========================= --}}
    <div class="biodata-container">
        <table style="width: 100%; border: none; border-collapse: collapse; font-family: sans-serif; font-size: 12px;">
            <tr>
                <!-- Kolom Kiri -->
                <td style="width: 130px; padding: 2px 4px; padding-right: 8px; border: none; text-align: left;"><strong>Nama</strong></td>
                <td style="width: 15px; padding: 2px 4px; border: none; text-align: left;">:</td>
                <td style="padding: 2px 4px; border: none; text-align: left;">{{ $siswa->nama }}</td>
                
                <!-- Kolom Kanan -->
                <td style="width: 130px; padding: 2px 4px; padding-right: 8px; border: none; text-align: left;"><strong>Kelas</strong></td>
                <td style="width: 15px; padding: 2px 4px; border: none; text-align: left;">:</td>
                <td style="padding: 2px 4px; border: none; text-align: left;">{{ $waliData['kelas']->nama_kelas }}</td>
            </tr>
            <tr>
                <td style="width: 130px; padding: 2px 4px; padding-right: 8px; border: none; text-align: left;"><strong>NIS</strong></td>
                <td style="width: 15px; padding: 2px 4px; border: none; text-align: left;">:</td>
                <td style="padding: 2px 4px; border: none; text-align: left;">{{ $siswa->nisn ?? '-' }}</td>
                
                <td style="width: 130px; padding: 2px 4px; padding-right: 8px; border: none; text-align: left;"><strong>Semester</strong></td>
                <td style="width: 15px; padding: 2px 4px; border: none; text-align: left;">:</td>
                <td style="padding: 2px 4px; border: none; text-align: left;">{{ ucfirst($waliData['semester']) }}</td>
            </tr>
            <tr>
                <td style="width: 130px; padding: 2px 4px; padding-right: 8px; border: none; text-align: left;"><strong>Nama Madrasah</strong></td>
                <td style="width: 15px; padding: 2px 4px; border: none; text-align: left;">:</td>
                <td style="padding: 2px 4px; border: none; text-align: left;">{{ $settingSekolah->nama_madrasah }}</td>
                
                <td style="width: 130px; padding: 2px 4px; padding-right: 8px; border: none; text-align: left;"><strong>Tahun Ajaran</strong></td>
                <td style="width: 15px; padding: 2px 4px; border: none; text-align: left;">:</td>
                <td style="padding: 2px 4px; border: none; text-align: left;">{{ $waliData['tahunAjaran']->nama_tahun }}</td>
            </tr>
            <tr>
                {{-- colspan="6" membuat baris ini melebar menutupi semua kolom --}}
                <td style="width: 130px; padding: 2px 4px; padding-right: 8px; border: none; text-align: left;"><strong>Alamat Madrasah</strong></td>
                <td style="width: 15px; padding: 2px 4px; border: none; text-align: left;">:</td>
                <td colspan="4" style="padding: 2px 4px; border: none; text-align: left;">{{ trim(($settingSekolah->sekretariat ?? '')) }}</td>
            </tr>
        </table>
        <div style="width: 100%; height: 1px; background-color: #000000; margin: 0px;"></div>
    </div>
    
    <div class="judul-utama">LAPORAN HASIL BELAJAR SANTRI</div>

    {{-- ========================= --}}
    {{-- NILAI MAPEL --}}
    {{-- ========================= --}}

    <table class="table-nilai" border="1" cellspacing="0" cellpadding="5" width="100%">
        <thead>
            <tr>
                <th rowspan="2" width="5%" class="text-center">No</th>
                <th rowspan="2" width="20%" class="text-center">Mata Pelajaran</th>

                <th colspan="2" width="25%" class="text-center">Hasil Tes</th>

                <th colspan="2" width="50%" class="text-center rtl">نتائج التمرين الأول</th>

                <th rowspan="2" width="20%" class="text-center rtl">الفنون</th>
                <th rowspan="2" width="3%" class="text-center rtl">الرقم</th>
            </tr>

            <tr>
                <th width="8%" class="text-center">Angka</th>
                <th width="20%" class="text-center">Huruf</th>

                <th width="20%" class="text-center rtl">اللفظ</th>
                <th width="8%" class="text-center rtl">الرقم</th>
            </tr>
        </thead>


        <tbody>

            {{-- ======= TES TULIS ======= --}}
            <tr>
                <td colspan="8" class="text-left fw-bold">A&nbsp;&nbsp;TES TULIS</td>
            </tr>

            @php $no = 1; @endphp
            @forelse($nilaiTulis as $nilai)
                @php $nilaiClass = $nilai->is_below_kkm ? 'nilai-bawah-kkm' : ''; @endphp
                <tr>
                    <td>{{ $no }}</td>
                    <td class="text-left">{{ $nilai->guruMapelKelas->mapel->nama_mapel ?? '-' }}</td>
                    <td class="{{ $nilaiClass }}">{{ $nilai->nilai_uas ?? '-' }}</td>
                    <td class="terbilang-kecil {{ $nilaiClass }}">{{ is_numeric($nilai->nilai_uas) ? terbilang($nilai->nilai_uas) : '-' }}</td>
                    <td class="rtl {{ $nilaiClass }}">{{ is_numeric($nilai->nilai_uas) ? terbilangArab($nilai->nilai_uas) : '-' }}</td>
                    <td class="{{ $nilaiClass }}">{{ is_numeric($nilai->nilai_uas) ? angkaArab($nilai->nilai_uas) : '-' }}</td>
                    <td class="rtl">{{ $nilai->guruMapelKelas->mapel->nama_mapel_ar ?? '-' }}</td>
                    <td class="text-center">{{ angkaArab($no) }}</td>
                </tr>
                @php $no++; @endphp
            @empty
                <tr><td colspan="8" class="text-center">Belum ada nilai tulis</td></tr>
            @endforelse

            {{-- ======= TES NON-TULIS ======= --}}
            <tr>
                <td colspan="8" class="text-left fw-bold">B&nbsp;&nbsp;TES NON-TULIS</td>
            </tr>
            @if($nilaiNonTulis)
                {{-- MUHAFADZHOH --}}
                @php $nilaiClassMuhafadzhoh = $nilaiNonTulis->is_muhafadzhoh_baik ? '' : 'nilai-bawah-kkm'; @endphp
                <tr>
                    <td>{{ $no }}</td>
                    <td class="text-left">Muhafadzhoh</td>
                    <td class="{{ $nilaiClassMuhafadzhoh }}">{{ $nilaiNonTulis->muhafadzhoh ?? '-' }}</td>
                    <td class="terbilang-kecil {{ $nilaiClassMuhafadzhoh }}">{{ konversiMuhafadzhoh($nilaiNonTulis->muhafadzhoh) ?? '-' }}</td>
                    <td>-</td>
                    <td class="{{ $nilaiClassMuhafadzhoh }}">{{ is_numeric($nilaiNonTulis->muhafadzhoh) ? angkaArab($nilaiNonTulis->muhafadzhoh) : $nilaiNonTulis->muhafadzhoh }}</td>
                    <td class="rtl">المحافظة</td>
                    <td class="text-center">{{ angkaArab($no) }}</td>
                </tr>

                {{-- QIROATUL KUTUB --}}
                @php $nilaiClassQiroatul = $nilaiNonTulis->is_qiroatul_below_kkm ? 'nilai-bawah-kkm' : ''; @endphp
                <tr>
                    <td>{{ ++$no }}</td>
                    <td class="text-left">Qiroatul Kutub</td>
                    <td class="{{ $nilaiClassQiroatul }}">{{ $nilaiNonTulis->qiroatul_kutub ?? '-' }}</td>
                    <td class="terbilang-kecil {{ $nilaiClassQiroatul }}">{{ is_numeric($nilaiNonTulis->qiroatul_kutub) ? terbilang($nilaiNonTulis->qiroatul_kutub) : '-' }}</td>
                    <td class="rtl {{ $nilaiClassQiroatul }}">{{ terbilangArab($nilaiNonTulis->qiroatul_kutub) }}</td>
                    <td class="{{ $nilaiClassQiroatul }}">{{ is_numeric($nilaiNonTulis->qiroatul_kutub) ? angkaArab($nilaiNonTulis->qiroatul_kutub) : '-' }}</td>
                    <td class="rtl">قراءة الكتب</td>
                    <td class="text-center">{{ angkaArab($no) }}</td>
                </tr>

                {{-- TAFTISYUL KUTUB --}}
                @php $nilaiClassTaftisyul = $nilaiNonTulis->is_taftisyul_below_kkm ? 'nilai-bawah-kkm' : ''; @endphp
                <tr>
                    <td>{{ ++$no }}</td>
                    <td class="text-left">Tafisyul Kutub</td>
                    <td class="{{ $nilaiClassTaftisyul }}">{{ $nilaiNonTulis->taftisyul_kutub ?? '-' }}</td>
                    <td class="terbilang-kecil {{ $nilaiClassTaftisyul }}">{{ is_numeric($nilaiNonTulis->taftisyul_kutub) ? terbilang($nilaiNonTulis->taftisyul_kutub) : '-' }}</td>
                    <td class="rtl {{ $nilaiClassTaftisyul }}">{{ terbilangArab($nilaiNonTulis->taftisyul_kutub) }}</td>
                    <td class="{{ $nilaiClassTaftisyul }}">{{ is_numeric($nilaiNonTulis->taftisyul_kutub) ? angkaArab($nilaiNonTulis->taftisyul_kutub) : '-' }}</td>
                    <td class="rtl">تفتيش الكتب</td>
                    <td class="text-center">{{ angkaArab($no) }}</td>
                </tr>
            @endif

        </tbody>
    </table>

{{-- ========================= --}}
{{-- CATATAN DAN INFORMASI TAMBAHAN --}}
{{-- ========================= --}}

<table class="table-unified-catatan" style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th colspan="3" style="width:38%; font-weight:bold; text-align:left; border:none; background: transparent;">
                Hasil Akhir dan Catatan
            </th>
            <th colspan="3" style="width:29%; font-weight:bold; text-align:left; border:none; background: transparent;">
                Penilaian Karakter
            </th>
            <th colspan="3" style="width:33.33%; font-weight:bold; text-align:left; border:none; background: transparent;">
                Rekapitulasi Absensi
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td style="width:5%; font-weight:bold; text-align:left; border:none;">Rata-rata</td>
            <td style="width:3%; text-align:left; border:none;">:</td>
            <td style="width:30%; text-align:left; border:none;">{{ $rataRata }}</td>

            <td style="width:12%; font-weight:bold; text-align:left; border:none;">Kelakuan</td>
            <td style="width:3%; text-align:left; border:none;">:</td>
            <td style="width:14%; text-align:left; border:none;">{{ $penilaianKarakter->kelakuan ?? '-' }}</td>

            <td style="width:12%; font-weight:bold; text-align:left; border:none;">Alpa</td>
            <td style="width:3%; text-align:left; border:none;">:</td>
            <td style="width:18%; text-align:left; border:none;">{{ $absensi?->alpa ?? 0 }} hari</td>
        </tr>

        <tr>
            <td style="font-weight:bold; text-align:left; border:none;">Peringkat</td>
            <td style="text-align:left; border:none;">:</td>
            <td style="text-align:left; border:none;">{{ $ranking }} dari {{ $jumlahSiswaDiKelas }} santri</td>

            <td style="font-weight:bold; text-align:left; border:none;">Kerajinan</td>
            <td style="text-align:left; border:none;">:</td>
            <td style="text-align:left; border:none;">{{ $penilaianKarakter->kerajinan ?? '-' }}</td>

            <td style="font-weight:bold; text-align:left; border:none;">Izin</td>
            <td style="text-align:left; border:none;">:</td>
            <td style="text-align:left; border:none;">{{ $absensi?->izin ?? 0 }} hari</td>
        </tr>

        <tr>
            <td style="width:10%; font-weight:bold; text-align:left; vertical-align:top; border:none;">
                Catatan
            </td>
            <td style="width:2%; text-align:left; vertical-align:top; border:none;">:</td>
            <td style="
                width:26%;
                text-align:left;
                vertical-align:top;
                border:none;
                white-space:normal;
                word-break:break-word;
            ">
                {{ $catatanRapor->catatan ?? '-' }}
            </td>
        
            <td style="font-weight:bold; text-align:left; vertical-align:top; border:none;">
                Kerapihan
            </td>
            <td style="text-align:left; vertical-align:top; border:none;">:</td>
            <td style="text-align:left; vertical-align:top; border:none;">
                {{ $penilaianKarakter->kerapihan ?? '-' }}
            </td>
        
            <td style="font-weight:bold; text-align:left; vertical-align:top; border:none;">
                Sakit
            </td>
            <td style="text-align:left; vertical-align:top; border:none;">:</td>
            <td style="text-align:left; vertical-align:top; border:none;">
                {{ $absensi?->sakit ?? 0 }} hari
            </td>
        </tr>

    </tbody>
</table>

{{-- ========================= --}}
{{-- TANDA TANGAN --}}
{{-- ========================= --}}
<br>
<div style="width:100%; text-align:center; margin-top:10px;">
    <table class="table-ttd" style="width:100%; text-align:center;">
        <tr>
            <!-- Baris 1: Label Wali Santri & Mustahiq -->
            <td style="width:33%; vertical-align: bottom; padding-bottom: 5px;">
                Wali Santri,
            </td>
            <td style="width:33%;"></td>
            <td style="width:33%; vertical-align: bottom; padding-bottom: 5px;">
                Mustahiq,
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold; text-align: center; vertical-align: top;">
                @if(isset($waliKelasUser) && $waliKelasUser->ttd_wali_kelas)
                    <img src="{{ public_path('img/ttd.jpg') }}" alt="TTD Murid" style="max-height: 50px;">
                    <br>
                    <u>......................................</u>
                @else
                    <u>......................................</u>
                @endif
            </td>
            <td></td>
            <td style="font-weight:bold; text-align: center; vertical-align: top;">
                @if(isset($waliKelasUser) && $waliKelasUser->ttd_wali_kelas)
                    <img src="{{ public_path('img/ttd/' . $waliKelasUser->ttd_wali_kelas) }}" 
                         alt="TTD Wali Kelas" 
                         style="max-height: 50px; max-width: 120px; display: block; margin-left: auto; margin-right: auto;">
                    <br>
                    <u>{{ $waliKelasUser->name }}</u>
                @else
                    <u>{{ $waliKelasUser->name ?? '......................................' }}</u>
                @endif
            </td>
        </tr>
        <tr>
            <!-- Baris 3: Spasi sebelum Kepala Madrasah -->
            <td colspan="3" style="text-align:center; padding-top: 5px;">
                {{ $settingSekolah->tempat_ttd ?? '...........................' }},
                {{ \Carbon\Carbon::parse($settingSekolah->tanggal_rapor ?? now())->translatedFormat('d F Y') }}
                <br>
                Kepala Madrasah,
            </td>
        </tr>
        <tr>
            <!-- Baris 4: TTD Kepala Madrasah -->
            <td colspan="3" style="text-align:center; font-weight:bold; vertical-align: top;">
                @if($settingSekolah->ttd_kepala_madrasah)
                    <img src="{{ public_path('img/ttd/' . $settingSekolah->ttd_kepala_madrasah) }}" 
                         alt="TTD Kepala Madrasah" 
                         style="max-height: 60px; max-width: 144px; display: block; margin-left: auto; margin-right: auto;">
                    <br>
                    <u>{{ $settingSekolah->kepala_madrasah }}</u>
                @else
                    <u>{{ $settingSekolah->kepala_madrasah ?? '......................................' }}</u>
                @endif
            </td>
        </tr>
    </table>
</div>
</body>
</html>