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

-- Dumping structure for table intimart.arus_kas
CREATE TABLE IF NOT EXISTS `arus_kas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tanggal` date DEFAULT NULL,
  `keterangan` text,
  `debet` decimal(12,2) DEFAULT NULL,
  `kredit` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.arus_kas: ~0 rows (approximately)

-- Dumping structure for table intimart.barang
CREATE TABLE IF NOT EXISTS `barang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(100) NOT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `harga_beli` decimal(12,2) DEFAULT NULL,
  `harga_jual` decimal(12,2) DEFAULT NULL,
  `stok_minimum` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang: ~4 rows (approximately)
INSERT INTO `barang` (`id`, `nama_barang`, `satuan`, `harga_beli`, `harga_jual`, `stok_minimum`) VALUES
	(1, 'Sabun Mandi', 'pcs', 2000.00, 3000.00, 20),
	(2, 'Susu Kotak', 'pak', 5000.00, 6500.00, 10),
	(7, 'Sabun Cuci ubah', 'pcs e', 5000.00, 6000.00, 11),
	(8, 'Hydro coco', 'pak', 8000.00, 12000.00, 10);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_kadaluarsa: ~0 rows (approximately)
INSERT INTO `barang_kadaluarsa` (`id`, `id_barang`, `tanggal_expired`, `jumlah`, `lokasi`) VALUES
	(1, 1, '2025-06-10', 20, 'Gudang Utama'),
	(2, 2, '2025-06-15', 15, 'Gudang Cabang');

-- Dumping structure for table intimart.barang_keluar
CREATE TABLE IF NOT EXISTS `barang_keluar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `tujuan` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_keluar: ~0 rows (approximately)

-- Dumping structure for table intimart.barang_masuk
CREATE TABLE IF NOT EXISTS `barang_masuk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.barang_masuk: ~2 rows (approximately)
INSERT INTO `barang_masuk` (`id`, `id_barang`, `tanggal`, `jumlah`, `keterangan`) VALUES
	(1, 1, '2025-05-30', 100, 'Stok awal Sabun Mandi'),
	(2, 2, '2025-05-30', 80, 'Stok awal Susu Kotak');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.detail_restok: ~0 rows (approximately)
INSERT INTO `detail_restok` (`id`, `id_restok`, `id_barang`, `jumlah`) VALUES
	(1, 1, 1, 100),
	(2, 1, 2, 50),
	(3, 2, 1, 150);

-- Dumping structure for table intimart.gudang
CREATE TABLE IF NOT EXISTS `gudang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_gudang` varchar(100) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.gudang: ~0 rows (approximately)
INSERT INTO `gudang` (`id`, `nama_gudang`, `alamat`) VALUES
	(1, 'Gudang Pusat', 'Jl. Intiboga No. 1, Banjarmasin');

-- Dumping structure for table intimart.laba
CREATE TABLE IF NOT EXISTS `laba` (
  `id` int NOT NULL AUTO_INCREMENT,
  `periode` varchar(7) DEFAULT NULL,
  `total_pendapatan` decimal(12,2) DEFAULT NULL,
  `total_pengeluaran` decimal(12,2) DEFAULT NULL,
  `laba_bersih` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.laba: ~0 rows (approximately)

-- Dumping structure for table intimart.pembayaran
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int DEFAULT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_penjualan` (`id_penjualan`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pembayaran: ~0 rows (approximately)
INSERT INTO `pembayaran` (`id`, `id_penjualan`, `metode`, `tanggal`, `jumlah`) VALUES
	(1, 1, 'tunai', '2025-05-30', 30000.00);

-- Dumping structure for table intimart.pemesanan
CREATE TABLE IF NOT EXISTS `pemesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pemesanan: ~0 rows (approximately)

-- Dumping structure for table intimart.pengiriman
CREATE TABLE IF NOT EXISTS `pengiriman` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int DEFAULT NULL,
  `tujuan` varchar(100) DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `status_pengiriman` enum('dikirim','diterima') DEFAULT 'dikirim',
  `estimasi_tiba` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.pengiriman: ~1 rows (approximately)
INSERT INTO `pengiriman` (`id`, `id_barang`, `tujuan`, `tanggal_kirim`, `status_pengiriman`, `estimasi_tiba`) VALUES
	(1, 1, 'Toko A - Banjarmasin', '2025-05-30', 'dikirim', '2025-06-05');

-- Dumping structure for table intimart.penjualan
CREATE TABLE IF NOT EXISTS `penjualan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int DEFAULT NULL,
  `id_sales` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  KEY `id_sales` (`id_sales`),
  CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`),
  CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_sales`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.penjualan: ~0 rows (approximately)
INSERT INTO `penjualan` (`id`, `id_barang`, `id_sales`, `tanggal`, `jumlah`, `total`) VALUES
	(1, 1, 4, '2025-05-30', 10, 30000.00);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.piutang: ~0 rows (approximately)

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.restok_supplier: ~0 rows (approximately)
INSERT INTO `restok_supplier` (`id`, `id_supplier`, `tgl_pesan`, `status`, `catatan`, `created_at`) VALUES
	(1, 1, '2025-05-01', 'diproses', 'Restok rutin bulanan', '2025-05-30 23:00:29'),
	(2, 2, '2025-05-02', 'dikirim', 'Permintaan mendadak', '2025-05-30 23:00:29');

-- Dumping structure for table intimart.retur
CREATE TABLE IF NOT EXISTS `retur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `alasan` text,
  `tanggal` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_penjualan` (`id_penjualan`),
  CONSTRAINT `retur_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.retur: ~0 rows (approximately)
INSERT INTO `retur` (`id`, `id_penjualan`, `jumlah`, `alasan`, `tanggal`) VALUES
	(1, 1, 2, 'Kemasan rusak', '2025-05-30');

-- Dumping structure for table intimart.stok
CREATE TABLE IF NOT EXISTS `stok` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `stok_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.stok: ~0 rows (approximately)

-- Dumping structure for table intimart.supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) NOT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `alamat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table intimart.supplier: ~0 rows (approximately)
INSERT INTO `supplier` (`id`, `nama_supplier`, `kontak`, `alamat`) VALUES
	(1, 'CV Sumber Jaya', '08123456789', 'Jl. Ahmad Yani No. 12'),
	(2, 'PT Mega Distribusi', '08129876543', 'Jl. Veteran KM 5');

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

-- Dumping data for table intimart.target_sales: ~1 rows (approximately)
INSERT INTO `target_sales` (`id`, `id_sales`, `bulan`, `target`, `realisasi`) VALUES
	(1, 4, '2025-05', 500000, 120);

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
