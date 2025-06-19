
CREATE DATABASE IF NOT EXISTS intimart_clean;
USE intimart_clean;

CREATE TABLE barang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_barang VARCHAR(100) NOT NULL,
  satuan VARCHAR(50),
  harga_beli DECIMAL(12,2),
  harga_jual DECIMAL(12,2),
  stok_minimum INT DEFAULT 0
);

CREATE TABLE barang_kadaluarsa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_barang INT,
  tanggal_expired DATE NOT NULL,
  jumlah INT NOT NULL,
  lokasi VARCHAR(100)
);

CREATE TABLE barang_keluar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_barang INT NOT NULL,
  id_gudang INT,
  id_user INT NOT NULL,
  tanggal DATE NOT NULL,
  jumlah INT NOT NULL,
  jenis ENUM('internal','rusak','hilang','retur_supplier') NOT NULL,
  tujuan VARCHAR(150),
  keterangan TEXT
);

CREATE TABLE barang_masuk (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  id_barang INT NOT NULL,
  id_gudang INT,
  tanggal DATE NOT NULL,
  jumlah INT NOT NULL,
  keterangan TEXT
);

CREATE TABLE detail_restok (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_restok INT NOT NULL,
  id_barang INT NOT NULL,
  jumlah INT NOT NULL
);

CREATE TABLE gudang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_gudang VARCHAR(100),
  alamat TEXT
);

CREATE TABLE kas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  jenis ENUM('masuk','keluar'),
  keterangan VARCHAR(255),
  jumlah INT,
  tanggal DATE,
  created_by INT
);

CREATE TABLE laba (
  id INT AUTO_INCREMENT PRIMARY KEY,
  periode VARCHAR(7),
  total_pendapatan DECIMAL(12,2),
  total_pengeluaran DECIMAL(12,2),
  laba_bersih DECIMAL(12,2)
);

CREATE TABLE pelanggan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pelanggan VARCHAR(100) NOT NULL,
  no_hp VARCHAR(20),
  alamat TEXT
);

CREATE TABLE pembayaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_penjualan INT,
  metode ENUM('tunai','transfer','qris') NOT NULL,
  keterangan TEXT,
  tanggal DATE,
  jumlah_bayar DECIMAL(12,0)
);

CREATE TABLE pemesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_barang INT NOT NULL,
  id_sales INT NOT NULL,
  jumlah INT NOT NULL,
  catatan TEXT,
  status ENUM('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  tanggal_pemesanan DATETIME DEFAULT CURRENT_TIMESTAMP,
  tanggal_direspon DATETIME
);

CREATE TABLE pengiriman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tujuan VARCHAR(100) NOT NULL,
  tanggal_kirim DATE NOT NULL,
  estimasi_tiba DATE,
  status_pengiriman ENUM('diproses','dikirim','diterima') DEFAULT 'diproses',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pengiriman_detail (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_pengiriman INT NOT NULL,
  id_barang INT NOT NULL,
  jumlah INT DEFAULT 1
);

CREATE TABLE penjualan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_barang INT,
  id_sales INT,
  tanggal DATE,
  jumlah INT,
  harga_total DECIMAL(12,2),
  status_pelunasan ENUM('belum lunas','lunas') DEFAULT 'belum lunas'
);

CREATE TABLE piutang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_sales INT,
  tanggal DATE,
  jumlah DECIMAL(12,2),
  status ENUM('belum lunas','lunas') DEFAULT 'belum lunas'
);

CREATE TABLE produk_tidak_laku (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_barang INT NOT NULL,
  periode_awal DATE NOT NULL,
  periode_akhir DATE NOT NULL,
  jumlah_terjual INT DEFAULT 0,
  keterangan TEXT,
  status ENUM('diperiksa','tindaklanjut','selesai') DEFAULT 'diperiksa',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rekonsiliasi_pembayaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_pembayaran INT NOT NULL,
  status ENUM('sesuai','tidak sesuai') DEFAULT 'sesuai',
  catatan TEXT,
  tanggal_rekonsiliasi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_final TINYINT(1) DEFAULT 0
);

CREATE TABLE restok_supplier (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_supplier INT NOT NULL,
  tgl_pesan DATE NOT NULL,
  status ENUM('diproses','dikirim','selesai','batal') DEFAULT 'diproses',
  catatan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE retur_penjualan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_penjualan INT,
  jumlah INT,
  alasan TEXT,
  tanggal DATE
);

CREATE TABLE stok_fisik (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_barang INT NOT NULL,
  id_gudang INT,
  id_user INT NOT NULL,
  jumlah_fisik INT NOT NULL,
  stok_sistem INT,
  koreksi TINYINT(1) DEFAULT 0,
  tanggal DATE NOT NULL,
  keterangan TEXT
);

CREATE TABLE supplier (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_supplier VARCHAR(100) NOT NULL,
  kontak VARCHAR(50),
  alamat TEXT
);

CREATE TABLE target_sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_sales INT,
  bulan VARCHAR(7),
  target INT,
  realisasi INT DEFAULT 0
);

CREATE TABLE user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100),
  foto VARCHAR(255),
  role ENUM('admin','manajer','karyawan','sales') NOT NULL
);
