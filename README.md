# Rapor Madin

<p align="center">
  <a href="https://github.com/fuadmubarokk/rapormadin" target="_blank">
    <img src="https://raw.githubusercontent.com/fuadmubarokk/rapormadin/main/public/images/logo-madrasah.png" width="400" alt="Rapor Madin Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/fuadmubarokk/rapormadin/actions">
    <img src="https://github.com/fuadmubarokk/rapormadin/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://github.com/fuadmubarokk/rapormadin">
    <img src="https://img.shields.io/github/stars/fuadmubarokk/rapormadin?style=social" alt="GitHub Stars">
  </a>
  <a href="https://github.com/fuadmubarokk/rapormadin">
    <img src="https://img.shields.io/github/license/fuadmubarokk/rapormadin" alt="License">
  </a>
</p>

---

## 📘 Tentang Rapor Madin

**Rapor Madin** adalah aplikasi web berbasis **Laravel** yang dirancang khusus untuk mengelola sistem penilaian dan pembuatan rapor di **Madrasah Diniyah**.  
Aplikasi ini menyediakan solusi komprehensif untuk pengelolaan data siswa, nilai, dan pembuatan rapor secara efisien dan terstruktur.

Rapor Madin hadir untuk menyederhanakan administrasi penilaian yang selama ini cukup kompleks, dengan menyediakan fitur-fitur utama seperti:

- Sistem manajemen pengguna dengan peran berbeda
- Pembuatan rapor PDF dengan template khusus
- Impor dan ekspor nilai menggunakan Excel
- Manajemen foto siswa dengan pengolahan gambar
- Database yang terstruktur dan rapi
- Pemrosesan laporan di latar belakang
- Antarmuka yang responsif dan modern

Rapor Madin mudah digunakan, powerful, dan dirancang untuk mendukung kebutuhan penilaian madrasah secara optimal.

---

## ✨ Fitur Utama

- **Manajemen Pengguna**  
  Sistem multi-peran (Admin, Guru, Wali) dengan hak akses berbeda

- **Manajemen Siswa**  
  Kelola data siswa, kelas, dan informasi pendukung lainnya

- **Input Nilai**  
  Sistem input nilai yang mudah, cepat, dan terstruktur

- **Pembuatan Rapor**  
  Generate rapor otomatis dalam format PDF profesional

- **Dashboard Informatif**  
  Tampilan dashboard berbeda sesuai peran pengguna

- **Backup Data**  
  Menjaga keamanan dan integritas data aplikasi

---

## 🚀 Memulai Rapor Madin

Rapor Madin dilengkapi dengan dokumentasi lengkap serta panduan instalasi yang tersedia di repositori GitHub.  
Dokumentasi ini membantu pengguna memahami alur penggunaan aplikasi dari awal hingga lanjutan.

---

## 🛠 Teknologi yang Digunakan

### Backend
- **Laravel Framework** – Framework PHP yang elegan dan powerful
- **Laravel Breeze** – Autentikasi sederhana
- **Laravel Sanctum** – API authentication yang aman
- **Barryvdh DomPDF** – Pembuatan dokumen PDF
- **Maatwebsite Excel** – Impor & ekspor file Excel
- **Intervention Image** – Manipulasi dan pengolahan gambar

### Frontend
- **Tailwind CSS** – Framework CSS modern
- **Alpine.js** – Framework JavaScript ringan
- **Vite** – Build tool cepat dan modern

---

## ⚙️ Cara Instalasi

### Persyaratan Sistem
- PHP >= 8.2
- Composer
- Node.js & NPM
- Database (MySQL / PostgreSQL)

### Langkah-langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/fuadmubarokk/rapormadin.git
   cd rapormadin

2. **Install dependencies**
   ```bash
   composer install
   npm install
   
3. **Install dependencies**
   ```bash
   cp .env.example .env
   php artisan key:generate
   
4. **Konfigurasi database dan jalankan migrasi**
   Pastikan pengaturan database di file .env sudah benar, kemudian jalankan:
   ```bash
   php artisan migrate

5. **Jalankan aplikasi**
   ```bash
   php artisan serve

Akses aplikasi melalui browser:
   ```bash
   http://127.0.0.1:8000


## 🤝 Kontribusi
1. Fork repository ini
2. Buat branch fitur baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## 📜 Kode Etik
Seluruh kontributor diharapkan menjaga sikap profesional dan saling menghormati dalam pengembangan Rapor Madin.

## 🔐 Kerentanan Keamanan
Jika Anda menemukan kerentanan keamanan pada aplikasi ini, silakan laporkan melalui email:
📧 fuadmubarok1998@gmail.com
Setiap laporan akan ditangani dengan serius dan secepat mungkin.

## 📄 Lisensi
MIT License
```text
MIT License

Copyright (c) 2025 Muhammad Fuad Mubarok

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
