# Layanan Administrasi Kelurahan (Laravel 12 + MySQL)

Aplikasi pelayanan surat kelurahan secara online. Warga mendaftar dengan NIK, mengajukan surat keterangan dari HP, memantau prosesnya, lalu mengunduh surat PDF bernomor resmi dengan kode QR verifikasi. Petugas memproses permohonan dari dasbor yang sederhana, melayani warga di loket, mengelola data penduduk, kabar, dan pengaduan.

## Fitur

**Situs publik**
- Landing page modern: layanan, alur pelayanan, statistik, kabar, jam layanan, peta, tanya jawab
- Daftar jenis surat beserta syarat dan perkiraan waktu selesai
- Cek status permohonan & pengaduan memakai kode lacak (tanpa login)
- Halaman verifikasi keaslian surat (tujuan kode QR di surat)
- Formulir pengaduan warga dengan foto (anti-spam)
- Kabar & pengumuman kelurahan

**Akun warga** (masuk dengan NIK)
- Daftar mandiri: NIK + tanggal lahir dicocokkan dengan data penduduk kelurahan
- Ajukan surat dengan isian sesuai jenis surat + unggah berkas (JPG/PNG/PDF)
- Pantau status (diajukan, diverifikasi, diproses, selesai/ditolak) beserta riwayatnya
- Unduh surat PDF begitu selesai, batalkan permohonan yang belum diperiksa

**Panel petugas & admin**
- Dasbor: antrean yang perlu ditangani, grafik 14 hari, layanan terbanyak, pengaduan baru
- Proses permohonan dengan alur status yang jelas; status Selesai otomatis membuat nomor surat (urut per tahun, tidak bisa ganda) dan surat PDF
- Pratinjau surat sebelum terbit, kop surat dengan logo, tanda tangan lurah, dan QR verifikasi
- Layanan loket: buat permohonan untuk warga yang datang langsung, surat bisa langsung terbit
- Data penduduk: tambah/ubah, cari, filter RW/status, lihat anggota keluarga (KK), impor dari Excel (CSV)
- Jenis layanan bisa ditambah sendiri: syarat, isian tambahan, dan isi surat dengan kode seperti `{nama}`, `{nik}`, `{keperluan}`
- Kabar & pengumuman (bisa dijadwalkan), tanggapan pengaduan
- Laporan bulanan per jenis layanan + buku register surat keluar (cetak / CSV)
- Pengguna: tambah petugas, reset kata sandi, blokir akun warga; peran admin & petugas
- Pengaturan identitas kelurahan, logo kop, nama & NIP lurah, kode nomor surat, jam layanan, peta

**Keamanan**: kata sandi di-hash, CSRF di semua form, batas percobaan masuk & kiriman, berkas lampiran disimpan privat (hanya pemohon & petugas yang bisa membuka), validasi jenis & ukuran file, isi surat di-escape, CSV aman dari formula injection, folder internal & `.env` terkunci dari browser.

Tampilan responsif (HP, tablet, desktop), font Manrope + DM Serif Display, ikon garis, tanpa CDN.

## Kebutuhan

- PHP 8.2 atau lebih baru (XAMPP 8.2+ / Laragon) dengan ekstensi pdo_mysql, mbstring, fileinfo, dom, gd (gd untuk logo PNG/JPG di surat)
- MySQL 5.7+ / MariaDB 10.4+
- [Composer](https://getcomposer.org)

## Cara install (XAMPP / Laragon di Windows)

1. Ekstrak ZIP lalu pindahkan folder `layanan-kelurahan` ke `C:\xampp\htdocs\` (atau `C:\laragon\www\`).
2. Nyalakan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Klik dua kali **`setup.bat`** (atau buka *Shell* XAMPP, masuk ke folder proyek, ketik `setup.bat`). Script ini memasang dependensi, membuat database `layanan_kelurahan`, tabel, dan data contoh.
4. Buka **http://localhost/layanan-kelurahan** lalu klik **Ajukan surat** / buka `/masuk`:
   - Admin: `admin@kelurahan.test` / `admin123`
   - Petugas: `petugas@kelurahan.test` / `petugas123`
   - Warga: NIK `3273010101900001` / `warga123`

Linux/macOS: `bash setup.sh`, lalu `php artisan serve` dan buka http://127.0.0.1:8000.

User/password MySQL berbeda? Ubah `DB_USERNAME` dan `DB_PASSWORD` di file `.env` lalu jalankan setup lagi.

## Mulai dipakai

1. Masuk sebagai admin, buka **Pengaturan kelurahan**: isi nama kelurahan, kecamatan, kota, logo, nama & NIP lurah, kode nomor surat.
2. Ganti kata sandi admin di **Ganti kata sandi**, lalu tambahkan akun petugas di **Pengguna**.
3. Hapus data contoh lalu impor data penduduk asli lewat **Data penduduk > Impor dari Excel (CSV)** (template tersedia di halaman itu).
4. Sesuaikan **Jenis layanan** (syarat dan isi surat) dengan format kelurahan Anda.

## Pasang di hosting

Arahkan document root ke folder `public/` (atau unggah seperti di XAMPP), ubah `APP_URL` di `.env`, pastikan `APP_ENV=production` dan `APP_DEBUG=false`. Folder `storage/`, `bootstrap/cache/`, dan `public/uploads/` harus bisa ditulis. Untuk instalasi tanpa data contoh jalankan `php artisan migrate --force` lalu buat akun admin pertama lewat `php artisan tinker`.

## Struktur singkat

- `app/Support/Surat.php`: nomor surat otomatis, isi surat dari template, PDF & QR verifikasi
- `app/Support/Kelurahan.php`: pengaturan identitas kelurahan
- `app/Http/Controllers/PublikController.php` (situs), `WargaController.php` (akun warga), `Admin/*` (panel petugas)
- `resources/views/publik`, `warga`, `admin`, `surat/pdf.blade.php` (tata letak surat)
- `database/seeders`: data contoh Kelurahan Sukamaju (fiktif)
- `index.php` + `.htaccess` di folder utama: agar bisa dibuka langsung dari `htdocs` tanpa `/public`
