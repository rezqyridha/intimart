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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang: ~5 rows (approximately)
INSERT INTO `barang` (`id`, `nama_barang`, `satuan`, `harga_beli`, `harga_jual`, `stok_minimum`) VALUES
	(1, 'Indomie Ayam Bawang', 'karton', 78000.00, 85000.00, 10),
	(2, 'Indomie Soto Mie', 'karton', 77000.00, 84000.00, 10),
	(3, 'Pop Mie Kari Ayam Jumbo', 'karton', 49000.00, 54000.00, 5),
	(4, 'Indomie Goreng Rendang', 'karton', 82000.00, 89000.00, 10),
	(5, 'Pop Mie Baso Sapi Jumbo', 'karton', 48500.00, 53000.00, 5);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_kadaluarsa: ~5 rows (approximately)
INSERT INTO `barang_kadaluarsa` (`id`, `id_barang`, `tanggal_expired`, `jumlah`, `lokasi`) VALUES
	(1, 1, '2025-07-01', 5, 'Gudang Pusat'),
	(2, 2, '2025-07-15', 3, 'Gudang Cabang A'),
	(3, 3, '2025-07-20', 7, 'Gudang Cabang B'),
	(4, 4, '2025-08-01', 10, 'Gudang Pusat'),
	(5, 5, '2025-08-15', 4, 'Gudang Cabang B');

-- Dumping structure for table intimart.barang_keluar
CREATE TABLE IF NOT EXISTS `barang_keluar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NOT NULL,
  `id_gudang` int DEFAULT NULL,
  `id_user` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `jenis` enum('internal','rusak','hilang','retur_supplier') NOT NULL,
  `tujuan` varchar(150) DEFAULT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  KEY `id_user` (`id_user`),
  KEY `fk_barangkeluar_gudang` (`id_gudang`),
  CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_keluar_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_barangkeluar_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_keluar: ~5 rows (approximately)
INSERT INTO `barang_keluar` (`id`, `id_barang`, `id_gudang`, `id_user`, `tanggal`, `jumlah`, `jenis`, `tujuan`, `keterangan`) VALUES
	(1, 1, 1, 1, '2025-06-06', 25, 'internal', 'Toko A', 'Distribusi mingguan'),
	(2, 2, 2, 1, '2025-06-07', 20, 'rusak', 'Toko B', 'Distribusi mingguan'),
	(3, 3, 3, 1, '2025-06-08', 15, 'hilang', 'Toko C', 'Distribusi mingguan'),
	(4, 4, 1, 1, '2025-06-09', 18, 'retur_supplier', 'Toko D', 'Distribusi mingguan'),
	(5, 5, 2, 1, '2025-06-10', 22, 'internal', 'Toko E', 'Distribusi mingguan');

-- Dumping structure for table intimart.barang_masuk
CREATE TABLE IF NOT EXISTS `barang_masuk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `id_barang` int NOT NULL,
  `id_gudang` int DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_barang` (`id_barang`),
  KEY `fk_barangmasuk_gudang` (`id_gudang`),
  CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_masuk_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_barangmasuk_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_masuk: ~5 rows (approximately)
INSERT INTO `barang_masuk` (`id`, `id_user`, `id_barang`, `id_gudang`, `tanggal`, `jumlah`, `keterangan`) VALUES
	(1, 5, 1, 1, '2025-06-01', 100, 'Pengadaan rutin'),
	(2, 5, 2, 2, '2025-06-02', 120, 'Pengadaan rutin'),
	(3, 1, 3, 3, '2025-06-03', 90, 'Pengadaan rutin'),
	(4, 1, 4, 1, '2025-06-04', 85, 'Pengadaan rutin'),
	(5, 1, 5, 2, '2025-06-05', 110, 'Pengadaan rutin');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.detail_restok: ~5 rows (approximately)
INSERT INTO `detail_restok` (`id`, `id_restok`, `id_barang`, `jumlah`) VALUES
	(1, 1, 1, 50),
	(2, 1, 2, 30),
	(3, 2, 3, 40),
	(4, 3, 4, 60),
	(5, 3, 5, 25);

-- Dumping structure for table intimart.gudang
CREATE TABLE IF NOT EXISTS `gudang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_gudang` varchar(100) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.gudang: ~3 rows (approximately)
INSERT INTO `gudang` (`id`, `nama_gudang`, `alamat`) VALUES
	(1, 'Gudang Pusat', 'Jl. Pasar Baru No. 87-89, Banjarmasin'),
	(2, 'Gudang Cabang A', 'Jl. A. Yani KM.5, Banjarmasin'),
	(3, 'Gudang Cabang B', 'Jl. Trikora No. 12, Banjarbaru');

-- Dumping structure for table intimart.kas
CREATE TABLE IF NOT EXISTS `kas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis` enum('masuk','keluar') DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.kas: ~4 rows (approximately)
INSERT INTO `kas` (`id`, `jenis`, `keterangan`, `jumlah`, `tanggal`, `created_by`) VALUES
	(1, 'masuk', 'Penjualan tunai', 85000, '2025-06-06', 1),
	(2, 'masuk', 'Transfer pelanggan', 168000, '2025-06-07', 1),
	(3, 'keluar', 'Biaya operasional', 50000, '2025-06-08', 1),
	(4, 'keluar', 'Transport cabang', 75000, '2025-06-09', 1);

-- Dumping structure for table intimart.laba
CREATE TABLE IF NOT EXISTS `laba` (
  `id` int NOT NULL AUTO_INCREMENT,
  `periode` varchar(7) DEFAULT NULL,
  `total_pendapatan` decimal(12,2) DEFAULT NULL,
  `total_pengeluaran` decimal(12,2) DEFAULT NULL,
  `laba_bersih` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.laba: ~0 rows (approximately)
INSERT INTO `laba` (`id`, `periode`, `total_pendapatan`, `total_pengeluaran`, `laba_bersih`) VALUES
	(1, '2025-06', 450000.00, 150000.00, 300000.00);

-- Dumping structure for table intimart.pelanggan
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pelanggan: ~5 rows (approximately)
INSERT INTO `pelanggan` (`id`, `nama_pelanggan`, `no_hp`, `alamat`) VALUES
	(1, 'Toko A', '081234567001', 'Jl. Pelanggan A No.1'),
	(2, 'Toko B', '081234567002', 'Jl. Pelanggan B No.2'),
	(3, 'Toko C', '081234567003', 'Jl. Pelanggan C No.3'),
	(4, 'Toko D', '081234567004', 'Jl. Pelanggan D No.4'),
	(5, 'Toko E', '081234567005', 'Jl. Pelanggan E No.5');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pembayaran: ~4 rows (approximately)

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pemesanan: ~5 rows (approximately)
INSERT INTO `pemesanan` (`id`, `id_barang`, `id_sales`, `jumlah`, `catatan`, `status`, `tanggal_pemesanan`, `tanggal_direspon`) VALUES
	(1, 1, 2, 20, 'Restok untuk Toko A', 'menunggu', '2025-06-12 09:00:00', NULL),
	(2, 2, 2, 15, 'Persiapan promo', 'disetujui', '2025-06-12 10:00:00', '2025-06-13 08:00:00'),
	(3, 3, 3, 25, 'Request cabang baru', 'ditolak', '2025-06-13 14:30:00', '2025-06-13 16:00:00'),
	(4, 4, 2, 10, 'Top up stok harian', 'disetujui', '2025-06-14 11:00:00', '2025-06-14 15:00:00'),
	(5, 5, 3, 30, 'Restok bulk order', 'menunggu', '2025-06-15 10:00:00', NULL);

-- Dumping structure for table intimart.pengiriman
CREATE TABLE IF NOT EXISTS `pengiriman` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tujuan` varchar(100) NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `estimasi_tiba` date DEFAULT NULL,
  `status_pengiriman` enum('diproses','dikirim','diterima') DEFAULT 'diproses',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pengiriman: ~2 rows (approximately)
INSERT INTO `pengiriman` (`id`, `tujuan`, `tanggal_kirim`, `estimasi_tiba`, `status_pengiriman`, `created_at`) VALUES
	(1, 'Toko A', '2025-06-07', '2025-06-08', 'dikirim', '2025-06-19 14:50:25'),
	(2, 'Toko B', '2025-06-08', '2025-06-09', 'diproses', '2025-06-19 14:50:25');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pengiriman_detail: ~4 rows (approximately)
INSERT INTO `pengiriman_detail` (`id`, `id_pengiriman`, `id_barang`, `jumlah`) VALUES
	(1, 1, 1, 10),
	(2, 1, 2, 5),
	(3, 2, 3, 12),
	(4, 2, 5, 8);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.penjualan: ~5 rows (approximately)

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
	(1, 3, '2025-06-08', 41000.00, 'belum lunas'),
	(2, 3, '2025-06-10', 53000.00, 'belum lunas');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.produk_tidak_laku: ~5 rows (approximately)
INSERT INTO `produk_tidak_laku` (`id`, `id_barang`, `periode_awal`, `periode_akhir`, `jumlah_terjual`, `keterangan`, `status`, `created_at`) VALUES
	(1, 1, '2025-05-01', '2025-05-31', 0, 'Tidak ada permintaan', 'diperiksa', '2025-06-19 14:51:09'),
	(2, 2, '2025-05-01', '2025-05-31', 3, 'Penjualan sangat rendah', 'tindaklanjut', '2025-06-19 14:51:09'),
	(3, 3, '2025-05-01', '2025-05-31', 1, 'Perlu strategi diskon', 'selesai', '2025-06-19 14:51:09'),
	(4, 4, '2025-05-01', '2025-05-31', 0, 'Tersimpan lama di gudang', 'diperiksa', '2025-06-19 14:51:09'),
	(5, 5, '2025-05-01', '2025-05-31', 2, 'Kurang diminati pelanggan', 'tindaklanjut', '2025-06-19 14:51:09');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.rekonsiliasi_pembayaran: ~4 rows (approximately)

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.restok_supplier: ~3 rows (approximately)
INSERT INTO `restok_supplier` (`id`, `id_supplier`, `tgl_pesan`, `status`, `catatan`, `created_at`) VALUES
	(1, 1, '2025-06-01', 'diproses', 'Permintaan reguler', '2025-06-19 14:58:24'),
	(2, 2, '2025-06-03', 'selesai', 'Restok awal bulan', '2025-06-19 14:58:24'),
	(3, 3, '2025-06-05', 'dikirim', 'Stok promo', '2025-06-19 14:58:24');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.retur_penjualan: ~2 rows (approximately)
INSERT INTO `retur_penjualan` (`id`, `id_penjualan`, `jumlah`, `alasan`, `tanggal`) VALUES
	(1, 3, 3, 'Cup rusak', '2025-06-09'),
	(2, 5, 2, 'Salah kirim rasa', '2025-06-11');

-- Dumping structure for table intimart.stok_fisik
CREATE TABLE IF NOT EXISTS `stok_fisik` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NOT NULL,
  `id_gudang` int DEFAULT NULL,
  `id_user` int NOT NULL,
  `jumlah_fisik` int NOT NULL,
  `stok_sistem` int DEFAULT NULL,
  `koreksi` tinyint(1) DEFAULT '0',
  `tanggal` date NOT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  KEY `id_user` (`id_user`),
  KEY `fk_stokfisik_gudang` (`id_gudang`),
  CONSTRAINT `fk_stokfisik_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `stok_fisik_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `stok_fisik_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.stok_fisik: ~3 rows (approximately)
INSERT INTO `stok_fisik` (`id`, `id_barang`, `id_gudang`, `id_user`, `jumlah_fisik`, `stok_sistem`, `koreksi`, `tanggal`, `keterangan`) VALUES
	(2, 2, 2, 5, 120, 120, 1, '2025-06-08', 'Sesuai sistem'),
	(4, 1, 1, 1, 70, 65, 1, '2025-07-01', '-'),
	(5, 2, 2, 1, 70, NULL, 0, '2025-07-11', '');

-- Dumping structure for table intimart.supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) NOT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.supplier: ~3 rows (approximately)
INSERT INTO `supplier` (`id`, `nama_supplier`, `kontak`, `alamat`) VALUES
	(1, 'PT Indofood CBP', '081234567890', 'Jakarta'),
	(2, 'CV Sumber Saji', '082112345678', 'Surabaya'),
	(3, 'UD Rasa Sejati', '085345678901', 'Banjarmasin');

-- Dumping structure for table intimart.target_sales
CREATE TABLE IF NOT EXISTS `target_sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_sales` int DEFAULT NULL,
  `bulan` varchar(7) DEFAULT NULL,
  `target` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_sales` (`id_sales`),
  CONSTRAINT `target_sales_ibfk_1` FOREIGN KEY (`id_sales`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.target_sales: ~4 rows (approximately)
INSERT INTO `target_sales` (`id`, `id_sales`, `bulan`, `target`) VALUES
	(1, 2, '2025-06', 1000000,),
	(2, 3, '2025-06', 1200000,),
	(3, 2, '2025-07', 1100000,),
	(4, 3, '2025-07', 1300000,);

-- Dumping structure for table intimart.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('admin','manajer','karyawan','sales') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.user: ~5 rows (approximately)
INSERT INTO `user` (`id`, `username`, `password`, `nama_lengkap`, `foto`, `role`) VALUES
	(1, 'admin', '$2y$10$hKzGALpxWT/tAMP3QmvYLeRa6Fxn8fFxkLEP8KizKopy/V0SxfkfS', 'Administrator Utama', 'user_1_1750341743.png', 'admin'),
	(2, 'sales1', '$2y$10$q73dTuFau208m4QH01aNsuJqWIqDJat28GGSbfXGUXKbKoJfhmP3y', 'Budi Sales', 'user_2_1750345917.png', 'sales'),
	(3, 'sales2', '$2y$10$p3RrJERckK2mEcgz8bX46OSP4Btg6USMYeE.vZJUTVVdQUCJoP06y', 'Ani Sales', 'user_3_1750346026.png', 'sales'),
	(4, 'manajer1', '$2y$10$jB7mfEcL9Xw5r9iGxFsAf.f.Nps/sYhfm0FP.4acC1b2DqHsp/pq.', 'Rudi Manajer', 'user_4_1750345849.png', 'manajer'),
	(5, 'karyawan1', '$2y$10$hlE6CCwqHxF7RMcNEa59uuoq61i/PqUxdJuLsQMD6QuFyZ3uFxf8K', 'Siti Karyawan', 'user_5_1750346094.png', 'karyawan');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
