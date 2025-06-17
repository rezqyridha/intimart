-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for intimart
CREATE DATABASE IF NOT EXISTS `intimart` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `intimart`;

-- Dumping structure for table intimart.barang
CREATE TABLE IF NOT EXISTS `barang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(100) NOT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `harga_beli` decimal(12,2) DEFAULT NULL,
  `harga_jual` decimal(12,2) DEFAULT NULL,
  `stok_minimum` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang: ~3 rows (approximately)
INSERT INTO `barang` (`id`, `nama_barang`, `satuan`, `harga_beli`, `harga_jual`, `stok_minimum`) VALUES
	(1, 'Sabun Mandi', 'pcs', 2000.00, 3000.00, 20),
	(2, 'Susu Kotak', 'pak', 5000.00, 6500.00, 10),
	(3, 'Hydrococo', 'pak', 7000.00, 10000.00, 5);

-- Dumping structure for table intimart.barang_kadaluarsa
CREATE TABLE IF NOT EXISTS `barang_kadaluarsa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NOT NULL,
  `tanggal_expired` date NOT NULL,
  `jumlah` int NOT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `barang_kadaluarsa_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_kadaluarsa: ~2 rows (approximately)
INSERT INTO `barang_kadaluarsa` (`id`, `id_barang`, `tanggal_expired`, `jumlah`, `lokasi`) VALUES
	(1, 1, '2025-07-01', 5, 'Gudang Pusat'),
	(2, 2, '2025-07-15', 3, 'Gudang Cabang');

-- Dumping structure for table intimart.barang_keluar
CREATE TABLE IF NOT EXISTS `barang_keluar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NOT NULL,
  `id_user` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `jenis` enum('internal','rusak','hilang','retur_supplier') NOT NULL,
  `tujuan` varchar(150) DEFAULT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_keluar_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_keluar: ~4 rows (approximately)
INSERT INTO `barang_keluar` (`id`, `id_barang`, `id_user`, `tanggal`, `jumlah`, `jenis`, `tujuan`, `keterangan`) VALUES
	(1, 1, 1, '2025-06-10', 5, 'internal', 'Kantor Cabang A', 'Digunakan untuk demo produk'),
	(2, 2, 2, '2025-06-09', 2, 'rusak', 'Gudang Pusat', 'Botol pecah saat unloading'),
	(3, 3, 3, '2025-06-08', 1, 'hilang', NULL, 'Hilang saat transit'),
	(4, 2, 1, '2025-06-07', 10, 'retur_supplier', 'CV Sumber Jaya', 'Barang expired, retur nota #RS102');

-- Dumping structure for table intimart.barang_masuk
CREATE TABLE IF NOT EXISTS `barang_masuk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `id_barang` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_masuk_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_masuk: ~3 rows (approximately)
INSERT INTO `barang_masuk` (`id`, `id_user`, `id_barang`, `tanggal`, `jumlah`, `keterangan`) VALUES
	(1, 1, 1, '2025-06-01', 100, 'Pengadaan Awal'),
	(2, 1, 2, '2025-06-02', 190, 'Restok Bulanan'),
	(5, 1, 3, '2025-06-11', 20, 'test masuk');

-- Dumping structure for table intimart.detail_restok
CREATE TABLE IF NOT EXISTS `detail_restok` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_restok` int NOT NULL,
  `id_barang` int NOT NULL,
  `jumlah` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_restok` (`id_restok`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `detail_restok_ibfk_1` FOREIGN KEY (`id_restok`) REFERENCES `restok_supplier` (`id`),
  CONSTRAINT `detail_restok_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.detail_restok: ~2 rows (approximately)
INSERT INTO `detail_restok` (`id`, `id_restok`, `id_barang`, `jumlah`) VALUES
	(1, 1, 1, 100),
	(2, 2, 2, 50);

-- Dumping structure for table intimart.gudang
CREATE TABLE IF NOT EXISTS `gudang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_gudang` varchar(100) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.gudang: ~2 rows (approximately)
INSERT INTO `gudang` (`id`, `nama_gudang`, `alamat`) VALUES
	(1, 'Gudang Pusat', 'Jl. Intiboga No. 1'),
	(2, 'Gudang Cabang', 'Jl. Raya Banjarmasin No. 2');

-- Dumping structure for table intimart.kas
CREATE TABLE IF NOT EXISTS `kas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis` enum('masuk','keluar') DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.kas: ~9 rows (approximately)
INSERT INTO `kas` (`id`, `jenis`, `keterangan`, `jumlah`, `tanggal`, `created_by`) VALUES
	(1, 'masuk', 'Penjualan produk A', 1500000, '2025-06-01', 1),
	(2, 'masuk', 'Penjualan produk B', 2750000, '2025-06-03', 1),
	(3, 'keluar', 'Pembayaran supplier A', 1000000, '2025-06-04', 1),
	(4, 'keluar', 'Biaya operasional toko', 500000, '2025-06-05', 1),
	(5, 'masuk', 'Pembayaran dari reseller', 2200000, '2025-06-07', 1),
	(6, 'keluar', 'Gaji karyawan', 3000000, '2025-06-10', 1),
	(7, 'masuk', 'Penjualan produk C', 1800000, '2025-06-11', 1),
	(8, 'keluar', 'Transport pengiriman', 400000, '2025-06-12', 1),
	(9, 'masuk', 'Pembayaran dari reseller', 250000, '2025-06-12', 1);

-- Dumping structure for table intimart.laba
CREATE TABLE IF NOT EXISTS `laba` (
  `id` int NOT NULL AUTO_INCREMENT,
  `periode` varchar(7) DEFAULT NULL,
  `total_pendapatan` decimal(12,2) DEFAULT NULL,
  `total_pengeluaran` decimal(12,2) DEFAULT NULL,
  `laba_bersih` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.laba: ~1 rows (approximately)
INSERT INTO `laba` (`id`, `periode`, `total_pendapatan`, `total_pengeluaran`, `laba_bersih`) VALUES
	(1, '2025-06', 1200000.00, 600000.00, 600000.00);

-- Dumping structure for table intimart.pelanggan
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pelanggan: ~4 rows (approximately)
INSERT INTO `pelanggan` (`id`, `nama_pelanggan`, `no_hp`, `alamat`) VALUES
	(1, 'Budi Santoso', '081234567890', 'Jl. Dahlia No.10'),
	(2, 'Siti Aminah', '081298765432', 'Jl. Melati No.25'),
	(3, 'Andi Wijaya', '081345678901', 'Jl. Kenanga No.5'),
	(4, 'Test', '4556658', 'Jl. Test No.22');

-- Dumping structure for table intimart.pembayaran
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int DEFAULT NULL,
  `metode` enum('tunai','transfer','qris') NOT NULL,
  `keterangan` text,
  `tanggal` date DEFAULT NULL,
  `jumlah_bayar` decimal(12,0) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pembayaran_penjualan` (`id_penjualan`),
  CONSTRAINT `fk_pembayaran_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pembayaran: ~2 rows (approximately)
INSERT INTO `pembayaran` (`id`, `id_penjualan`, `metode`, `keterangan`, `tanggal`, `jumlah_bayar`) VALUES
	(1, 1, 'tunai', NULL, '2025-06-06', 30000),
	(3, 2, 'transfer', 'test edit dari belum lunas ke lunas', '2025-06-10', 32500);

-- Dumping structure for table intimart.pemesanan
CREATE TABLE IF NOT EXISTS `pemesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NOT NULL,
  `id_sales` int NOT NULL,
  `jumlah` int NOT NULL,
  `catatan` text,
  `status` enum('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  `tanggal_pemesanan` datetime DEFAULT CURRENT_TIMESTAMP,
  `tanggal_direspon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  KEY `id_sales` (`id_sales`),
  CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`),
  CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_sales`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pemesanan: ~2 rows (approximately)
INSERT INTO `pemesanan` (`id`, `id_barang`, `id_sales`, `jumlah`, `catatan`, `status`, `tanggal_pemesanan`, `tanggal_direspon`) VALUES
	(3, 1, 4, 10, 'Permintaan awal untuk stok outlet A', 'ditolak', '2025-06-13 05:52:19', '2025-06-13 05:53:56'),
	(4, 2, 4, 10, 'Permintaan awal untuk stok outlet B', 'disetujui', '2025-06-13 05:53:01', '2025-06-13 05:53:52');

-- Dumping structure for table intimart.pengiriman
CREATE TABLE IF NOT EXISTS `pengiriman` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tujuan` varchar(100) NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `estimasi_tiba` date DEFAULT NULL,
  `status_pengiriman` enum('diproses','dikirim','diterima') DEFAULT 'diproses',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pengiriman: ~3 rows (approximately)
INSERT INTO `pengiriman` (`id`, `tujuan`, `tanggal_kirim`, `estimasi_tiba`, `status_pengiriman`, `created_at`) VALUES
	(1, 'Cabang A', '2025-06-07', '2025-06-09', 'diproses', '2025-06-09 03:13:07'),
	(2, 'Cabang B', '2025-06-08', '2025-06-10', 'dikirim', '2025-06-09 03:13:07'),
	(5, 'TOKO A Banjarbaru', '2025-06-05', '2025-06-06', 'diproses', '2025-06-10 16:10:46');

-- Dumping structure for table intimart.pengiriman_detail
CREATE TABLE IF NOT EXISTS `pengiriman_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pengiriman` int NOT NULL,
  `id_barang` int NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_pengiriman` (`id_pengiriman`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `pengiriman_detail_ibfk_1` FOREIGN KEY (`id_pengiriman`) REFERENCES `pengiriman` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pengiriman_detail_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pengiriman_detail: ~5 rows (approximately)
INSERT INTO `pengiriman_detail` (`id`, `id_pengiriman`, `id_barang`, `jumlah`) VALUES
	(5, 5, 3, 19),
	(8, 2, 2, 5),
	(9, 2, 1, 1),
	(10, 2, 3, 1),
	(11, 1, 1, 11);

-- Dumping structure for table intimart.penjualan
CREATE TABLE IF NOT EXISTS `penjualan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int DEFAULT NULL,
  `id_sales` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `harga_total` decimal(12,2) DEFAULT NULL,
  `status_pelunasan` enum('belum lunas','lunas') DEFAULT 'belum lunas',
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  KEY `id_sales` (`id_sales`),
  CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`),
  CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_sales`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.penjualan: ~3 rows (approximately)
INSERT INTO `penjualan` (`id`, `id_barang`, `id_sales`, `tanggal`, `jumlah`, `harga_total`, `status_pelunasan`) VALUES
	(1, 1, 4, '2025-06-05', 10, 30000.00, 'lunas'),
	(2, 2, 4, '2025-06-06', 5, 32500.00, 'lunas'),
	(7, 3, 4, '2025-06-12', 15, 150000.00, 'belum lunas');

-- Dumping structure for table intimart.piutang
CREATE TABLE IF NOT EXISTS `piutang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_sales` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah` decimal(12,2) DEFAULT NULL,
  `status` enum('belum lunas','lunas') DEFAULT 'belum lunas',
  PRIMARY KEY (`id`),
  KEY `id_sales` (`id_sales`),
  CONSTRAINT `piutang_ibfk_1` FOREIGN KEY (`id_sales`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.piutang: ~2 rows (approximately)
INSERT INTO `piutang` (`id`, `id_sales`, `tanggal`, `jumlah`, `status`) VALUES
	(1, 4, '2025-06-07', 100000.00, 'belum lunas'),
	(2, 4, '2025-06-08', 120000.00, 'lunas');

-- Dumping structure for table intimart.produk_tidak_laku
CREATE TABLE IF NOT EXISTS `produk_tidak_laku` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NOT NULL,
  `periode_awal` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `jumlah_terjual` int DEFAULT '0',
  `keterangan` text,
  `status` enum('diperiksa','tindaklanjut','selesai') DEFAULT 'diperiksa',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `produk_tidak_laku_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.produk_tidak_laku: ~3 rows (approximately)
INSERT INTO `produk_tidak_laku` (`id`, `id_barang`, `periode_awal`, `periode_akhir`, `jumlah_terjual`, `keterangan`, `status`, `created_at`) VALUES
	(1, 1, '2025-05-01', '2025-05-31', 0, 'Tidak ada permintaan', 'diperiksa', '2025-06-09 03:13:07'),
	(2, 2, '2025-05-01', '2025-05-31', 1, 'Penjualan rendah', 'tindaklanjut', '2025-06-09 03:13:07'),
	(3, 3, '2025-05-01', '2025-05-31', 0, 'Perlu diskon besar', 'selesai', '2025-06-09 03:13:07');

-- Dumping structure for table intimart.rekonsiliasi_pembayaran
CREATE TABLE IF NOT EXISTS `rekonsiliasi_pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pembayaran` int NOT NULL,
  `status` enum('sesuai','tidak sesuai') DEFAULT 'sesuai',
  `catatan` text,
  `tanggal_rekonsiliasi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_final` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_pembayaran` (`id_pembayaran`),
  CONSTRAINT `rekonsiliasi_pembayaran_ibfk_1` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.rekonsiliasi_pembayaran: ~2 rows (approximately)
INSERT INTO `rekonsiliasi_pembayaran` (`id`, `id_pembayaran`, `status`, `catatan`, `tanggal_rekonsiliasi`, `is_final`) VALUES
	(5, 3, 'sesuai', 'sesuai invoice #123', '2025-06-12 03:45:34', 1),
	(7, 1, 'tidak sesuai', 'minus cek ulang pembayran', '2025-06-12 22:00:37', 1);

-- Dumping structure for table intimart.restok_supplier
CREATE TABLE IF NOT EXISTS `restok_supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_supplier` int NOT NULL,
  `tgl_pesan` date NOT NULL,
  `status` enum('diproses','dikirim','selesai','batal') DEFAULT 'diproses',
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_supplier` (`id_supplier`),
  CONSTRAINT `restok_supplier_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.restok_supplier: ~4 rows (approximately)
INSERT INTO `restok_supplier` (`id`, `id_supplier`, `tgl_pesan`, `status`, `catatan`, `created_at`) VALUES
	(1, 1, '2025-06-01', 'diproses', 'Pengadaan reguler', '2025-06-09 03:13:07'),
	(2, 2, '2025-06-02', 'dikirim', 'Pesanan mendesak', '2025-06-09 03:13:07'),
	(3, 1, '2025-06-03', 'selesai', 'Sudah diterima', '2025-06-09 03:13:07'),
	(4, 2, '2025-06-04', 'batal', 'Kesalahan input', '2025-06-09 03:13:07');

-- Dumping structure for table intimart.retur_penjualan
CREATE TABLE IF NOT EXISTS `retur_penjualan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `alasan` text,
  `tanggal` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_penjualan` (`id_penjualan`),
  CONSTRAINT `retur_penjualan_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.retur_penjualan: ~1 rows (approximately)
INSERT INTO `retur_penjualan` (`id`, `id_penjualan`, `jumlah`, `alasan`, `tanggal`) VALUES
	(1, 1, 5, 'Rusak saat pengiriman ubah', '2025-06-08');

-- Dumping structure for table intimart.stok_fisik
CREATE TABLE IF NOT EXISTS `stok_fisik` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NOT NULL,
  `jumlah_fisik` int NOT NULL,
  `stok_sistem` int DEFAULT NULL,
  `koreksi` tinyint(1) DEFAULT '0',
  `tanggal` date NOT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `keterangan` text,
  `id_user` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `stok_fisik_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `stok_fisik_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.stok_fisik: ~1 rows (approximately)
INSERT INTO `stok_fisik` (`id`, `id_barang`, `jumlah_fisik`, `stok_sistem`, `koreksi`, `tanggal`, `lokasi`, `keterangan`, `id_user`) VALUES
	(1, 2, 170, 173, 1, '2025-06-10', 'Gudang Pusat', 'te koreksi', 1);

-- Dumping structure for table intimart.supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) NOT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.supplier: ~2 rows (approximately)
INSERT INTO `supplier` (`id`, `nama_supplier`, `kontak`, `alamat`) VALUES
	(1, 'CV Sumber Jaya', '0811111111', 'Jl. Veteran No.1'),
	(2, 'PT Mega Distribusi', '0822222222', 'Jl. Pangeran Antasari No.2');

-- Dumping structure for table intimart.target_sales
CREATE TABLE IF NOT EXISTS `target_sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_sales` int DEFAULT NULL,
  `bulan` varchar(7) DEFAULT NULL,
  `target` int DEFAULT NULL,
  `realisasi` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_sales` (`id_sales`),
  CONSTRAINT `target_sales_ibfk_1` FOREIGN KEY (`id_sales`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.target_sales: ~0 rows (approximately)
INSERT INTO `target_sales` (`id`, `id_sales`, `bulan`, `target`, `realisasi`) VALUES
	(1, 4, '2025-06', 1000000, 750000);

-- Dumping structure for table intimart.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` enum('admin','manajer','karyawan','sales') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.user: ~4 rows (approximately)
INSERT INTO `user` (`id`, `username`, `password`, `nama_lengkap`, `role`) VALUES
	(1, 'admin', 'admin123', 'Administrator', 'admin'),
	(2, 'manajer1', 'manajer123', 'Manager Pusat', 'manajer'),
	(3, 'karyawan1', 'karyawan123', 'Karyawan Gudang', 'karyawan'),
	(4, 'sales1', 'sales123', 'Sales Lapangan', 'sales');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
