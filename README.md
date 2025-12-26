# Intimart — Sistem Manajemen Intimart

**Nama:** M.Rezqy Noor Ridha

## Deskripsi Singkat

Intimart adalah aplikasi web untuk manajemen penjualan, persediaan, dan laporan keuangan (digunakan oleh PT Inti Boga Mandiri). Aplikasi ini membantu mengelola produk, stok, penjualan, pengiriman, pembayaran, retur, dan menghasilkan laporan PDF.

## Fitur Utama

-   Autentikasi dengan role (admin, manajer, karyawan, sales)
-   Manajemen produk, stok masuk/keluar, dan gudang
-   Pengelolaan pemasok dan pelanggan
-   Proses pemesanan, pengiriman, dan pembayaran
-   Penanganan retur dan rekonsiliasi pembayaran
-   Laporan (penjualan, stok, piutang) dan cetak PDF via FPDF

## Teknologi

-   Bahasa: PHP (procedural, mysqli)
-   Database: MySQL
-   Frontend: HTML, CSS, SCSS, Bootstrap, JavaScript
-   Library: FPDF, Swiper, berbagai library JS/CSS pada folder `assets`

## Cara Menjalankan (singkat)

1. Siapkan environment: Laragon / XAMPP / webserver dengan PHP dan MySQL.
2. Letakkan folder proyek di direktori web server (mis. `www` atau `htdocs`).
3. Import database dari `database/intimart.sql` menggunakan phpMyAdmin atau MySQL CLI.
4. Sesuaikan koneksi database di `config/koneksi.php` (host, username, password, database).
5. Sesuaikan `BASE_URL` di `config/constants.php` bila perlu.
6. Akses aplikasi melalui browser: `http://localhost/intimart/` atau `http://localhost/intimart/auth/login.php`.

## Struktur Proyek

-   `auth/` — Halaman autentikasi (login, register, session).
-   `config/` — Konfigurasi aplikasi dan koneksi database.
-   `modules/` — Modul fungsional terpisah berdasarkan role (admin, manajer, karyawan, sales) dan shared.
-   `assets/` — CSS, SCSS, JS, gambar, dan pustaka pihak ketiga.
-   `database/` — Skrip SQL untuk membuat dan mengisi database (`intimart.sql`).
-   `fpdf/` — Library FPDF untuk fitur cetak PDF.
-   `layouts/` — Template tampilan (header, sidebar, footer).

## Instalasi & Konfigurasi (singkat)

1. Siapkan environment lokal (Laragon/XAMPP) dengan PHP dan MySQL.
2. Letakkan folder proyek di web root, mis. `C:\laragon\www\intimart`.
3. Import `database/intimart.sql` ke MySQL (phpMyAdmin atau CLI).
4. Ubah kredensial database di `config/koneksi.php` jika perlu.
5. Sesuaikan `BASE_URL` di `config/constants.php` bila folder berbeda.
6. Akses aplikasi: `http://localhost/intimart/auth/login.php`.

## Kontribusi

Perbaikan bug atau fitur baru dapat dikirim melalui pull request atau beri tahu pemilik proyek. Jangan lupa membuat backup database sebelum pengujian fitur yang memodifikasi data.

## Kontak

-   Nama: M.Rezqy Noor Ridha

## Lisensi

Tidak ada file lisensi standar pada repo ini; tanyakan kepada pemilik proyek untuk informasi lisensi.
