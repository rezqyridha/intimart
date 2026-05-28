# 🏢 Intimart — Sistem Manajemen PT Inti Boga Mandiri

Aplikasi web komprehensif untuk mengelola penjualan, persediaan (stok), gudang, dan laporan keuangan secara terintegrasi dan efisien. Dirancang khusus untuk PT Inti Boga Mandiri dengan fitur manajemen end-to-end.

## 📋 Deskripsi Proyek

**Intimart** adalah sistem informasi terintegrasi yang memudahkan pengelolaan operasional distribusi produk makanan (Indomie, Pop Mie, dan produk sejenis). Sistem ini mendukung multi-role dengan hak akses berbeda untuk Admin, Manajer, Karyawan, dan Sales. Aplikasi membantu proses pengadaan, penyimpanan di gudang, distribusi, penjualan, pembayaran, hingga pelaporan keuangan dengan dukungan cetak PDF.

## 🎯 Fitur Utama

- **Autentikasi & Manajemen User** — Sistem login dengan 4 role (admin, manajer, karyawan, sales) dan dashboard per role
- **Manajemen Produk & Barang** — Tambah, edit, hapus data barang dengan harga beli/jual dan stok minimum
- **Manajemen Persediaan (Stok)** — Pencatatan barang masuk (pengadaan), barang keluar (distribusi, rusak, hilang), dan koreksi stok fisik
- **Manajemen Gudang** — Kelola gudang pusat dan cabang dengan lokasi barang
- **Supplier & Pelanggan** — Kelola data supplier dan pelanggan (toko retail)
- **Pemesanan & Restok** — Proses pemesanan barang dan restok dari supplier
- **Pengiriman** — Pencatatan pengiriman dengan status (diproses, dikirim, diterima)
- **Penjualan & Pembayaran** — Pencatatan transaksi penjualan dengan metode pembayaran (tunai, transfer, QRIS)
- **Retur Penjualan** — Penanganan retur barang dari pelanggan
- **Rekonsiliasi Pembayaran** — Verifikasi dan pencocokan pembayaran
- **Laporan Keuangan** — Laporan penjualan, stok, piutang, dan laba rugi dengan cetak PDF
- **Notifikasi Stok** — Peringatan barang yang kadaluarsa atau stok minimum tercapai
- **Dashboard & Monitoring** — Visualisasi data real-time dengan chart ApexCharts

## 🛠️ Teknologi yang Digunakan

### Backend

- **PHP** (Procedural, native) — Tanpa framework, menggunakan MySQLi untuk database
- **MySQL 8.0** — Database relasional

### Frontend

- **HTML5** — Struktur halaman
- **CSS3 & SCSS** — Styling dan layout
- **Bootstrap 5** — Framework CSS responsive
- **JavaScript (Vanilla)** — Interaktivitas dan validasi form

### Library & Plugin

- **FPDF** — Cetak laporan ke format PDF
- **ApexCharts** — Visualisasi chart (area, bar, pie, line, dll.)
- **DataTables** — Tabel interaktif dengan sort, filter, pagination
- **Bootstrap Icons** — Icon set profesional
- **Swiper** — Carousel/slider
- **Flatpickr** — Date & time picker
- **Sweetalert2** — Modal notifikasi yang menarik
- **jQuery, Select2, Masonry Layout** — Utility libraries
- **Moment.js** — Manajemen tanggal dan waktu

## 📁 Struktur Folder

```
intimart/
├── index.php                     # Entry point aplikasi
├── unauthorized.php              # Halaman akses ditolak
├── README.md                     # Dokumentasi (file ini)
│
├── auth/                         # Modul autentikasi
│   ├── login.php                # Halaman login
│   ├── register.php             # Halaman registrasi user
│   ├── logout.php               # Proses logout
│   ├── forgot-password.php      # Reset password
│   └── session.php              # Manajemen session & autentikasi
│
├── config/                       # Konfigurasi aplikasi
│   ├── koneksi.php              # Koneksi database MySQL
│   └── constants.php            # Konstanta aplikasi (BASE_URL, PATHS, timezone)
│
├── database/                     # File SQL & struktur database
│   ├── intimart.sql             # Dump database lengkap dengan data
│   ├── intimart_clean_structure.sql  # Struktur saja (tanpa data)
│   └── struktur.sql             # Struktur legacy
│
├── modules/                      # Modul sistem (per-role)
│   ├── admin/                   # Dashboard & fitur admin
│   │   ├── dashboard.php        # Dashboard admin
│   │   ├── keuangan/            # Laporan keuangan
│   │   ├── laba/                # Laporan laba rugi
│   │   ├── laporan/             # Laporan umum
│   │   ├── piutang/             # Manajemen piutang
│   │   ├── target_sales/        # Target penjualan
│   │   └── user/                # Manajemen user (add, edit, delete, reset password)
│   │
│   ├── manajer/                 # Dashboard & fitur manajer
│   │   └── dashboard.php
│   │
│   ├── karyawan/                # Dashboard & fitur karyawan
│   │   └── dashboard.php
│   │
│   ├── sales/                   # Dashboard & fitur sales
│   │   └── dashboard.php
│   │
│   └── shared/                  # Modul bersama (diakses oleh beberapa role)
│       ├── barang/              # Manajemen barang & produk
│       ├── barang_masuk/        # Pencatatan barang masuk
│       ├── barang_keluar/       # Pencatatan barang keluar
│       ├── gudang/              # Manajemen gudang
│       ├── kadaluarsa/          # Tracking barang kadaluarsa
│       ├── laporan/             # Laporan berbagai jenis
│       ├── notifikasi_stok/     # Notifikasi stok minimum
│       ├── pelanggan/           # Data pelanggan
│       ├── pembayaran/          # Manajemen pembayaran
│       ├── pemesanan/           # Proses pemesanan barang
│       ├── pengiriman/          # Manajemen pengiriman
│       ├── penjualan/           # Transaksi penjualan
│       ├── produk_tidak_laku/   # Analisis produk tidak laku
│       ├── rekonsiliasi_pembayaran/  # Rekonsiliasi pembayaran
│       ├── restock_supplier/    # Restok dari supplier
│       ├── retur_penjualan/     # Manajemen retur penjualan
│       ├── stok/                # Lihat stok barang
│       ├── stok_fisik/          # Koreksi stok fisik
│       ├── supplier/            # Data supplier
│       └── profile.php          # Edit profil user
│
├── layouts/                      # Template & layout umum
│   ├── head.php                 # Meta tag, CSS, title
│   ├── header.php               # Header bar
│   ├── topbar.php               # Top navigation bar
│   ├── sidebar.php              # Sidebar navigation
│   ├── footer.php               # Footer
│   ├── scripts.php              # Script dan JS dependencies
│   ├── menu_admin.php           # Menu untuk admin
│   ├── menu_manajer.php         # Menu untuk manajer
│   ├── menu_karyawan.php        # Menu untuk karyawan
│   └── menu_sales.php           # Menu untuk sales
│
├── assets/                       # Aset frontend
│   ├── css/                     # File CSS custom
│   │   ├── styles.min.css
│   │   ├── login-custom.css
│   │   └── icons.css
│   ├── scss/                    # Source SCSS
│   ├── js/                      # JavaScript custom & vendor
│   │   ├── main.js
│   │   ├── apexcharts-*.js      # Chart configurations
│   │   ├── datatables.js        # DataTables config
│   │   └── [50+ file JS lainnya]
│   ├── images/                  # Logo, icon, gambar UI
│   ├── img/                     # Gambar konten
│   ├── video/                   # Video (jika ada)
│   ├── landing/                 # Landing page assets
│   ├── icon-fonts/              # Font icon (fontawesome, dll)
│   └── libs/                    # Library pihak ketiga
│       ├── bootstrap/           # Bootstrap 5
│       ├── apexcharts/
│       ├── datatables.net-bs5/
│       ├── sweetalert2/
│       ├── flatpickr/
│       ├── swiper/
│       └── [30+ library lainnya]
│
├── fpdf/                         # FPDF Library untuk cetak PDF
│   ├── fpdf.php                 # Main FPDF class
│   ├── changelog.htm
│   ├── FAQ.htm
│   ├── font/                    # Font untuk PDF
│   ├── doc/                     # Dokumentasi
│   ├── makefont/
│   └── tutorial/
│
├── html/                         # HTML template statis (UI reference)
│   ├── 404.html, 500.html
│   ├── blog.html, blog-post.html
│   ├── ecommerce-*.html
│   ├── data-tables.html
│   ├── charts (apex, chartjs, echarts).html
│   └── [70+ template referensi lainnya]
│
├── uploads/                      # Direktori upload file user
│   ├── barang/                  # Gambar barang/produk
│   ├── pelanggan/
│   ├── supplier/
│   └── user/                    # Foto profil user
│
└── [file konfigurasi lainnya]
```

## 🗄️ Struktur Database Utama

### Tabel-tabel Kunci:

| Tabel               | Deskripsi                                                          |
| ------------------- | ------------------------------------------------------------------ |
| `user`              | Data pengguna dengan role (admin, manajer, karyawan, sales)        |
| `barang`            | Master produk/barang dengan harga beli-jual dan stok minimum       |
| `gudang`            | Master gudang pusat dan cabang                                     |
| `barang_masuk`      | Pencatatan pengadaan barang masuk ke gudang                        |
| `barang_keluar`     | Pencatatan barang keluar (internal, rusak, hilang, retur supplier) |
| `stok`              | Tabel relasi barang-gudang untuk tracking stok real-time           |
| `stok_fisik`        | Koreksi stok dari hasil stock opname fisik                         |
| `barang_kadaluarsa` | Tracking barang dengan tanggal expired                             |
| `pelanggan`         | Data pelanggan (toko retail)                                       |
| `supplier`          | Data supplier pemasok                                              |
| `pemesanan`         | Permintaan order barang dari sales                                 |
| `restok_supplier`   | Restok/pembelian dari supplier                                     |
| `pengiriman`        | Pengiriman ke pelanggan                                            |
| `penjualan`         | Transaksi penjualan                                                |
| `pembayaran`        | Metode & pencatatan pembayaran                                     |
| `retur_penjualan`   | Barang yang diretur oleh pelanggan                                 |
| `kas`               | Arus kas masuk/keluar                                              |
| `laba`              | Laporan laba rugi per periode                                      |
| `piutang`           | Tracking piutang pelanggan                                         |

## 🚀 Cara Menjalankan

### Prasyarat

- **Web Server**: Apache atau Nginx dengan PHP >= 7.4
- **Database**: MySQL 8.0 atau MariaDB (versi terbaru)
- **Environment**: Laragon, XAMPP, WAMP, atau VPS/Cloud Server
- **Browser**: Chrome, Firefox, Safari, Edge (versi terbaru)

### Instalasi & Setup Lokal

#### 1. **Siapkan Environment Lokal**

Gunakan **Laragon** (rekomendasi untuk Windows):

- Download & install dari https://laragon.org/
- Jalankan Laragon, pastikan Apache dan MySQL aktif

Atau gunakan **XAMPP** jika sudah familiar.

#### 2. **Clone/Copy Project ke Web Root**

```bash
# Jika menggunakan Laragon
cd C:\laragon\www
# Copy folder project ke sini atau clone dari repository

# Struktur akhir harus:
# C:\laragon\www\intimart\
```

#### 3. **Import Database**

**Opsi A: Menggunakan phpMyAdmin** (GUI)

```
1. Buka phpMyAdmin: http://localhost/phpmyadmin
2. Create database baru: "intimart"
3. Pilih database "intimart"
4. Klik tab "Import"
5. Pilih file: database/intimart.sql
6. Klik "Go"
```

**Opsi B: Menggunakan MySQL CLI** (Terminal/Command Prompt)

```bash
mysql -u root -p < database\intimart.sql
# Tekan Enter, bila minta password, kosongkan saja (default Laragon/XAMPP)
```

**Opsi C: Menggunakan Laragon Terminal**

```bash
laragon terminal
mysql -u root < www\intimart\database\intimart.sql
```

#### 4. **Konfigurasi Koneksi Database**

Edit file `config/koneksi.php`:

```php
<?php
$host     = 'localhost';      // Host MySQL (default: localhost)
$username = 'root';           // Username MySQL (default: root)
$password = '';               // Password MySQL (default: kosong untuk Laragon/XAMPP)
$database = 'intimart';       // Nama database

$koneksi = new mysqli($host, $username, $password, $database);
// ...
?>
```

Sesuaikan jika:

- MySQL berjalan di port berbeda (misal: 3307)
- Ada password untuk user MySQL
- Nama database berbeda

#### 5. **Konfigurasi BASE_URL (Opsional)**

Edit file `config/constants.php` jika folder proyek berbeda:

```php
// Jika project di: http://localhost/intimart/
define('BASE_URL', '/intimart');

// Jika project di subdomain: http://intimart.local/
define('BASE_URL', '');
```

#### 6. **Akses Aplikasi**

Buka browser dan kunjungi:

```
http://localhost/intimart/auth/login.php
```

atau langsung:

```
http://localhost/intimart/
```

(akan redirect ke halaman login)

### Setup di Production Server (VPS/Cloud)

1. **Upload project** ke `/home/username/public_html/intimart` via FTP/SFTP
2. **Create database** di cPanel atau via SSH:
    ```bash
    mysql -u db_user -p db_name < intimart.sql
    ```
3. **Update config/koneksi.php** dengan kredensial production
4. **Set permission folder uploads**:
    ```bash
    chmod -R 755 uploads/
    ```
5. **Pastikan .htaccess ada** (untuk URL rewriting jika diperlukan)

## 🔑 Akun Login Default

Sistem sudah memiliki 5 user default untuk testing. Password di-hash dengan bcrypt, namun untuk keperluan demo, berikut default credentials:

| Username    | Password    | Nama Lengkap        | Role     | Akses                           |
| ----------- | ----------- | ------------------- | -------- | ------------------------------- |
| `admin`     | admin123    | Administrator Utama | Admin    | Semua fitur                     |
| `sales1`    | sales123    | Budi Sales          | Sales    | Dashboard, Penjualan, Pemesanan |
| `sales2`    | sales123    | Ani Sales           | Sales    | Dashboard, Penjualan, Pemesanan |
| `manajer1`  | manajer123  | Rudi Manajer        | Manajer  | Dashboard, Laporan, Monitoring  |
| `karyawan1` | karyawan123 | Siti Karyawan       | Karyawan | Manajemen Barang, Stok, Gudang  |

**⚠️ Catatan Keamanan:**

- **Ganti password** semua user setelah pertama kali login di production
- Jangan bagikan default password ini di production environment
- Gunakan password yang kuat (kombinasi huruf, angka, spesial)

### Cara Mengganti Password

1. Login ke aplikasi
2. Klik **Profile** di sidebar
3. Klik **Edit Profil** atau **Ganti Password**
4. Masukkan password baru dan konfirmasi

## 👤 Role & Hak Akses

### 1. **Admin**

- Akses penuh ke semua fitur
- Manajemen user (create, edit, delete, reset password)
- Laporan keuangan lengkap
- Konfigurasi sistem

### 2. **Manajer**

- Dashboard monitoring
- Laporan penjualan, stok, piutang
- Approval pemesanan
- Monitoring kinerja sales dan karyawan
- Tidak bisa mengubah master data

### 3. **Karyawan**

- Manajemen barang/produk
- Pencatatan barang masuk/keluar
- Manajemen stok dan gudang
- Koreksi stok fisik
- Tracking barang kadaluarsa
- Tidak bisa akses laporan keuangan

### 4. **Sales**

- Dashboard penjualan pribadi
- Pencatatan transaksi penjualan
- Submit pemesanan/restok
- Lihat data pelanggan
- Tidak bisa edit data master

## 📊 Data Master yang Harus Disiapkan

Sebelum operasional, siapkan data master:

1. **Barang/Produk** — Nama, satuan (karton, box), harga beli, harga jual, stok minimum
2. **Gudang** — Nama gudang, alamat lokasi
3. **Supplier** — Nama, kontak, alamat pengiriman
4. **Pelanggan** — Nama toko, nomor HP, alamat
5. **User/Karyawan** — Username, password, role sesuai divisi

## 🔒 Fitur Keamanan

- **Password Hashing** — Menggunakan bcrypt (`password_hash()`, `password_verify()`)
- **Session Management** — Session-based authentication dengan timeout
- **Role-Based Access Control (RBAC)** — Pembatasan akses berdasarkan role
- **XSS Protection** — Input validation dan output escaping
- **CSRF Prevention** — Token validation (jika diimplementasikan)
- **Prepared Statements** — MySQLi prepared statements untuk SQL injection prevention

## 🐛 Troubleshooting

### Masalah Umum & Solusi

| Masalah                | Penyebab                      | Solusi                                                    |
| ---------------------- | ----------------------------- | --------------------------------------------------------- |
| Login gagal            | Database tidak terkoneksi     | Periksa config/koneksi.php, pastikan MySQL running        |
| Halaman blank          | PHP error                     | Check error log, enable error reporting di constants.php  |
| Upload foto gagal      | Folder uploads tidak writable | `chmod 755 uploads/` (Linux) atau ubah permission via FTP |
| Chart tidak tampil     | ApexCharts library tidak load | Periksa koneksi internet, reload browser                  |
| PDF tidak bisa dicetak | FPDF library error            | Pastikan font folder ada di fpdf/font/                    |
| Session timeout        | Session expired               | Login ulang, atau extend session timeout di session.php   |

### Enable Debug Mode (Development Only)

Edit `config/constants.php`:

```php
// Set ke true untuk development, false untuk production
define('DEBUG_MODE', true);
```

## 📦 Dependensi & Library

### Frontend Libraries (di assets/libs/)

- **Bootstrap 5.3** — Framework CSS
- **ApexCharts** — Chart library
- **DataTables** — Table plugin
- **Sweetalert2** — Modal notifications
- **Flatpickr** — Date picker
- **Swiper** — Carousel
- **jQuery** — DOM manipulation (jika diperlukan)
- **Select2** — Enhanced select dropdown
- **Masonry Layout** — Grid layout

### Backend

- **PHP MySQLi** — Native MySQL driver (built-in PHP)
- **FPDF** — PDF generation (included di folder fpdf/)
- **password_hash/verify** — Password encryption (built-in PHP)

Tidak ada composer.json atau npm, semua library sudah di-bundle di folder assets/libs/.

## 📞 Developer & Support

| Aspek                 | Informasi                              |
| --------------------- | -------------------------------------- |
| **Dikembangkan oleh** | M.Rezqy Noor Ridha                     |
| **Untuk**             | PT Inti Boga Mandiri                   |
| **Support Teknis**    | Hubungi developer atau tim IT internal |

## 📝 Changelog & Maintenance

### Versi Saat Ini

- **Release**: Mei 2026
- **Status**: Stable/Production Ready
- **Database**: MySQL 8.0+

### Fitur yang Mungkin Ditambahkan

- Export data ke Excel
- SMS notification untuk pengiriman
- Integration dengan payment gateway (midtrans, doku, dll)
- Mobile app (Android/iOS)
- API untuk integrasi dengan sistem lain
- Dashboard analytics yang lebih advanced

### Backup Database

**Rekomendasi**: Backup harian otomatis

```bash
# Manual backup via command line:
mysqldump -u root -p intimart > backup_intimart_$(date +%Y%m%d).sql

# Restore dari backup:
mysql -u root -p intimart < backup_intimart_20260528.sql
```

Di production server, gunakan cron job untuk auto backup:

```bash
0 2 * * * mysqldump -u user -p'password' intimart > /backup/intimart_$(date +\%Y\%m\%d).sql
```

## 📚 Kontak & Support

Untuk pertanyaan teknis, perbaikan bug, atau request fitur:

- **Developer**: M.Rezqy Noor Ridha
- **Email**: Hubungi via team IT internal
- **Issue Tracking**: Gunakan sistem internal perusahaan

---

**⚠️ Penting:**

- Jangan modifikasi file di folder `fpdf/` kecuali jika tahu apa yang dilakukan
- Backup database secara berkala
- Update password default setelah setup production
- Jangan commit database dump ke version control (add `database/` ke `.gitignore`)

---

**Last Update**: Mei 2026  
**Status**: ✅ Production Ready
