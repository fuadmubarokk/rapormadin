[1.0.0] – 2025-01-15
Added
- Fitur CRUD siswa (Create, Read, Update, Delete).
- Fitur upload foto siswa.
- Fitur import data siswa dari Excel (XLSX).
- Fitur pencarian siswa (live search).
- Filter siswa berdasarkan kelas.
- Validasi otomatis untuk NISN, nama, dan kelas.
- Halaman detail siswa.
- Halaman dashboard dengan ringkasan jumlah siswa per kelas.

Changed
- Tampilan tabel siswa diperbarui menggunakan template UI terbaru.
- Perbaikan pengalaman pengguna (UX) pada form tambah/edit siswa.
- Struktur folder Controllers/Models/Views dibuat lebih rapi dan modular.

Fixed
- Bug validasi NISN yang gagal ketika input angka depan 0.
- Error 500 pada form tambah siswa akibat field yang tidak terisi.
- Typo pada label “Nama Lengkap”.
- Perbaikan handling ketika upload foto gagal (file corrupt / ukuran terlalu besar).
- Query yang lambat pada halaman list karena pagination belum diaktifkan.