<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cover | {{ $siswa->nama }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .cover-page {
            width: 21.5cm;
            min-height: 33cm;
            box-sizing: border-box;
            position: relative;
            padding: 0 2cm;
        }

        .header {
            text-align: center;
            margin-bottom: 0.5cm;
            padding-top: 100px;
        }

        .logo-wrapper {
            width: 400px;
            height: 400px;
            padding-top: 50px;
            padding-bottom: 20px;
            margin: 0 auto;
            overflow: hidden;
        }

        .logo-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .judul-utama {
            font-size: 30px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 40px;
        }

        .student-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex-grow: 1;
            padding-top: 20px;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 25px;
        }

        .info-row .label {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
            text-align: center;
        }

        .info-row .value {
            height: 35px;
            border: 1.5px solid #000;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            line-height: 35px;
        }

        .school-address {
            position: absolute;
            padding-top: 170px;
            bottom: 1.5cm;
            left: 1.5cm;
            right: 1.5cm;
            height: 80px;
            text-align: center;
            font-size: 16px;
        }

        .school-address-content {
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
        }

        .identitas-santri {
            width: 21.5cm;
            height: 33cm;
            box-sizing: border-box;
            position: relative;
            padding: 0 2cm;
        }

        .judul-identitas {
            text-align: center;
            font-weight: bold;
            padding-top: 50px;
            font-size: 20px;
            padding-bottom: 50px;
        }

        tr.judul-grup td {
            padding-bottom: 10px;
        }

        .judul-tabel {
            font-weight: bold;
        }

        .anak-tabel {
            padding-left: 20px;
        }

        tr.space-kosong {
            padding-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="cover-page">
        <div class="header">
            <h1>{{ $settingSekolah->nama_madrasah }}</h1>
            <div class="logo-wrapper">
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo Madrasah">
                @endif
            </div>
        </div>

        <div class="judul-utama">LAPORAN HASIL BELAJAR SANTRI</div>

        <div class="student-info">
            <div class="info-row">
                <div class="label">Nama Santri</div>
                <div class="value">{{ $siswa->nama }}</div>
            </div>
            <div class="info-row">
                <div class="label">NIS</div>
                <div class="value">{{ $siswa->nisn }}</div>
            </div>
        </div>

        <div class="school-address">
            <div class="school-address-content">
                <div>Alamat:</div>
                {{ $settingSekolah->sekretariat }}<br>
                Desa {{ $settingSekolah->desa }}, Kecamatan {{ $settingSekolah->kecamatan }}, Kabupaten {{ $settingSekolah->kabupaten }}
            </div>
        </div>
    </div>

    <div class="identitas-santri">
        <div class="judul-identitas">
            <div>IDENTITAS SANTRI</div>
        </div>
        
        <div class="id-santri">
            <table width="100%">
                <tr class="judul-grup">
                    <td style="width: 5%;" class="judul-tabel">1.</td>
                    <td style="width: 30%;" class="judul-tabel">Nama</td>
                    <td style="width: 2%;">:</td>
                    <td> {{ $siswa->nama }} </td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;" class="judul-tabel">2.</td>
                    <td style="width: 30%;" class="judul-tabel">NIS</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->nisn}} </td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;" class="judul-tabel">3.</td>
                    <td style="width: 30%;" class="judul-tabel">Tempat, Tanggal Lahir</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->tempat_lahir ?? '-'}}, {{$siswa->tanggal_lahir->translatedFormat('d F Y') ?? '-'}} </td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;" class="judul-tabel">4.</td>
                    <td style="width: 30%;" class="judul-tabel">Jenis Kelamin</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->jenis_kelamin_text}}</td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;" class="judul-tabel">5.</td>
                    <td style="width: 30%;" class="judul-tabel">Agama</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->agama}}</td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;" class="judul-tabel">6.</td>
                    <td style="width: 30%;" class="judul-tabel">Status dalam Keluarga</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->status_keluarga}}</td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;" class="judul-tabel">7.</td>
                    <td style="width: 30%;" class="judul-tabel">Alamat</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->alamat ?? '-'}}</td>
                </tr>

                <tr>
                    <td style="width: 5%;" class="judul-tabel">8.</td>
                    <td style="width: 30%;" class="judul-tabel">Diterima di Madrasah ini</td>
                    <td style="width: 2%;">:</td>
                    <td></td>
                </tr>

                <tr>
                    <td style="width: 5%;"></td>
                    <td style="width: 30%;" class="anak-tabel">Kelas</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->kelas->nama_kelas}}</td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;"></td>
                    <td style="width: 30%;" class="anak-tabel">Pada tanggal</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->diterima_tanggal->translatedFormat('d F Y')}}</td>
                </tr>

                <tr>
                    <td style="width: 5%;" class="judul-tabel">9.</td>
                    <td style="width: 30%;" class="judul-tabel">Orang Tua</td>
                    <td style="width: 2%;">:</td>
                    <td></td>
                </tr>

                <tr>
                    <td style="width: 5%;"></td>
                    <td style="width: 30%;" class="anak-tabel">Nama Ayah</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->nama_ayah ?? '-'}}</td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;"></td>
                    <td style="width: 30%;" class="anak-tabel">Nama Ibu</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->nama_ibu ?? '-'}}</td>
                </tr>

                <tr>
                    <td style="width: 5%;" class="judul-tabel">10.</td>
                    <td style="width: 30%;" class="judul-tabel">Pekerjaan Orang Tua</td>
                    <td style="width: 2%;">:</td>
                    <td></td>
                </tr>

                <tr>
                    <td style="width: 5%;" class="judul-tabel"></td>
                    <td style="width: 30%;" class="anak-tabel">Pekerjaan Ayah</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->pekerjaan_ayah ?? '-'}}</td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;"></td>
                    <td style="width: 30%;" class="anak-tabel">Pekerjaan Ibu</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->pekerjaan_ibu ?? '-'}}</td>
                </tr>

                <tr class="judul-grup">
                    <td style="width: 5%;" class="judul-tabel">11.</td>
                    <td style="width: 30%;" class="judul-tabel">No. HP Orang Tua</td>
                    <td style="width: 2%;">:</td>
                    <td> {{$siswa->no_hp_ortu ?? '-'}}</td>
                </tr>
            </table>
        </div>

        <!-- Gunakan tabel untuk penjajaran yang pasti -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 50px;">
            <tr>
                <!-- Kolom Kiri: Kosong untuk memberi jarak -->
                <td style="width: 15%;"></td>
                
                <!-- Kolom Tengah: Foto Siswa -->
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <img src="{{ asset('img/foto_siswa.png') }}" 
                        alt="Foto Santri"
                        style="height: 4cm; width: 3cm;">
                </td>

                <!-- Kolom Kanan: TTD Kepala Madrasah -->
                <td style="width: 35%; text-align: center; vertical-align: bottom;">
                    {{ $settingSekolah->tempat_ttd ?? '...........................' }},
                    {{ \Carbon\Carbon::parse($settingSekolah->tanggal_rapor ?? now())->translatedFormat('d F Y') }}
                    <br>
                    Kepala Madrasah,
                    <br><br>

                    @if($settingSekolah->ttd_kepala_madrasah)
                        <img src="{{ asset('img/ttd/' . $settingSekolah->ttd_kepala_madrasah) }}" 
                            alt="TTD Kepala Madrasah"
                            style="max-height:90px; max-width:216px; display:block; margin: 0 auto;">
                        <br>
                        <u>{{ $settingSekolah->kepala_madrasah }}</u>
                    @else
                        <br><br><br>
                        <u>{{ $settingSekolah->kepala_madrasah ?? '......................................' }}</u>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>