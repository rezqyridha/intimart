
-- Struktur Database Intimart
-- DROP DATABASE IF EXISTS intimart;
CREATE DATABASE IF NOT EXISTS intimart;
USE intimart;

-- Tabel User
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100),
    role ENUM('admin', 'manajer', 'karyawan', 'sales') NOT NULL
);

-- Tabel Barang
CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    satuan VARCHAR(50),
    harga_beli DECIMAL(12,2),
    harga_jual DECIMAL(12,2),
    stok_minimum INT DEFAULT 0
);

-- Barang Masuk
CREATE TABLE barang_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT,
    tanggal DATE,
    jumlah INT,
    keterangan TEXT,
    FOREIGN KEY (id_barang) REFERENCES barang(id)
);

-- Barang Keluar
CREATE TABLE barang_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT,
    tanggal DATE,
    jumlah INT,
    tujuan VARCHAR(100),
    FOREIGN KEY (id_barang) REFERENCES barang(id)
);

-- Stok Fisik
CREATE TABLE stok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT,
    jumlah INT,
    lokasi VARCHAR(100),
    FOREIGN KEY (id_barang) REFERENCES barang(id)
);

-- Penjualan
CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT,
    id_sales INT,
    tanggal DATE,
    jumlah INT,
    total DECIMAL(12,2),
    FOREIGN KEY (id_barang) REFERENCES barang(id),
    FOREIGN KEY (id_sales) REFERENCES user(id)
);

-- Retur
CREATE TABLE retur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT,
    jumlah INT,
    alasan TEXT,
    tanggal DATE,
    FOREIGN KEY (id_penjualan) REFERENCES penjualan(id)
);

-- Pengiriman
CREATE TABLE pengiriman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT,
    tujuan VARCHAR(100),
    tanggal_kirim DATE,
    status_pengiriman ENUM('dikirim', 'diterima') DEFAULT 'dikirim',
    FOREIGN KEY (id_barang) REFERENCES barang(id)
);

-- Pembayaran
CREATE TABLE pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT,
    metode VARCHAR(50),
    tanggal DATE,
    jumlah DECIMAL(12,2),
    FOREIGN KEY (id_penjualan) REFERENCES penjualan(id)
);

-- Arus Kas
CREATE TABLE arus_kas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE,
    keterangan TEXT,
    debet DECIMAL(12,2),
    kredit DECIMAL(12,2)
);

-- Piutang
CREATE TABLE piutang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sales INT,
    tanggal DATE,
    jumlah DECIMAL(12,2),
    status ENUM('belum lunas','lunas') DEFAULT 'belum lunas',
    FOREIGN KEY (id_sales) REFERENCES user(id)
);

-- Laba
CREATE TABLE laba (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode VARCHAR(7),
    total_pendapatan DECIMAL(12,2),
    total_pengeluaran DECIMAL(12,2),
    laba_bersih DECIMAL(12,2)
);

-- Pemesanan
CREATE TABLE pemesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT,
    jumlah INT,
    tanggal DATE,
    status ENUM('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
    FOREIGN KEY (id_barang) REFERENCES barang(id)
);

-- Target Sales
CREATE TABLE target_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sales INT,
    bulan VARCHAR(7),
    target INT,
    FOREIGN KEY (id_sales) REFERENCES user(id)
);

-- Gudang (opsional multi-gudang)
CREATE TABLE gudang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_gudang VARCHAR(100),
    alamat TEXT
);

-- Dummy Data Awal
INSERT INTO user (username, password, nama_lengkap, role) VALUES
('admin', 'admin123', 'Administrator', 'admin'),
('manajer1', 'manajer123', 'Manager Pusat', 'manajer'),
('karyawan1', 'karyawan123', 'Karyawan Gudang', 'karyawan'),
('sales1', 'sales123', 'Sales Lapangan', 'sales');

INSERT INTO barang (nama_barang, satuan, harga_beli, harga_jual, stok_minimum) VALUES
('Sabun Mandi', 'pcs', 2000, 3000, 50),
('Susu Kotak', 'pak', 5000, 6500, 30);
