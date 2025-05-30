-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 30, 2025 at 02:43 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `distributor`
--

-- --------------------------------------------------------

--
-- Table structure for table `arus_kas`
--

CREATE TABLE `arus_kas` (
  `id_arus_kas` int NOT NULL,
  `id_manager` int NOT NULL,
  `id_penjualan` int NOT NULL,
  `id_laba_bersih` int NOT NULL,
  `saldo_kas` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `arus_kas`
--

INSERT INTO `arus_kas` (`id_arus_kas`, `id_manager`, `id_penjualan`, `id_laba_bersih`, `saldo_kas`) VALUES
(4, 0, 1, 1, -1100000),
(5, 0, 7, 2, 1470000),
(6, 1, 8, 3, 30000);

-- --------------------------------------------------------

--
-- Table structure for table `brg_keluar`
--

CREATE TABLE `brg_keluar` (
  `id_keluar` int NOT NULL,
  `id_penjualan` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brg_msk`
--

CREATE TABLE `brg_msk` (
  `id_masuk` int NOT NULL,
  `id_manager` int NOT NULL,
  `id_pesan` int NOT NULL,
  `catatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `brg_msk`
--

INSERT INTO `brg_msk` (`id_masuk`, `id_manager`, `id_pesan`, `catatan`) VALUES
(1, 0, 1, 'Tidak Ada Kerusakan'),
(2, 1, 3, 'Tidak Ada Kerusakan\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `laba_bersih`
--

CREATE TABLE `laba_bersih` (
  `id_laba_bersih` int NOT NULL,
  `id_manager` int NOT NULL,
  `id_penjualan` int NOT NULL,
  `biaya_transportasi` int NOT NULL,
  `biaya_gaji` int NOT NULL,
  `total_biaya` int NOT NULL,
  `bersih` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `laba_bersih`
--

INSERT INTO `laba_bersih` (`id_laba_bersih`, `id_manager`, `id_penjualan`, `biaya_transportasi`, `biaya_gaji`, `total_biaya`, `bersih`) VALUES
(1, 0, 1, 100000, 1500000, 1600000, -1100000),
(2, 0, 7, 30000, 1000000, 1030000, 1470000),
(3, 1, 8, 20000, 1000000, 1020000, 30000);

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `id_manager` int NOT NULL,
  `nm_manager` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`id_manager`, `nm_manager`, `jabatan`) VALUES
(1, 'SIDIK ANSORI', 'Sales Manager');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id_penjualan` int NOT NULL,
  `id_manager` int NOT NULL,
  `nm_toko` varchar(100) NOT NULL,
  `tgl` date NOT NULL,
  `no_transaksi` varchar(100) NOT NULL,
  `nm_brg` varchar(100) NOT NULL,
  `jumlah` int NOT NULL,
  `hrg_perdus` int NOT NULL,
  `total_penjualan` int NOT NULL,
  `nm_sales` varchar(100) NOT NULL,
  `area_penjualan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `id_manager`, `nm_toko`, `tgl`, `no_transaksi`, `nm_brg`, `jumlah`, `hrg_perdus`, `total_penjualan`, `nm_sales`, `area_penjualan`) VALUES
(8, 1, 'Toko Meriah', '2025-02-07', 'C1', 'Indomie Goreng', 30, 35000, 1050000, 'Maki', 'Banjarmasin');

-- --------------------------------------------------------

--
-- Table structure for table `pesan_barang`
--

CREATE TABLE `pesan_barang` (
  `id_pesan` int NOT NULL,
  `id_manager` int NOT NULL,
  `tgl_pemesanan` date NOT NULL,
  `no_pemesanan` varchar(100) NOT NULL,
  `nm_pengaju` varchar(100) NOT NULL,
  `nm_supplier` varchar(100) NOT NULL,
  `alamat_pengiriman` varchar(100) NOT NULL,
  `kode_barang` varchar(100) NOT NULL,
  `nm_brg` varchar(100) NOT NULL,
  `jml_brg` int NOT NULL,
  `hrg_perunit` int NOT NULL,
  `total_hrg` int NOT NULL,
  `tgl_pengiriman_inginkan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesan_barang`
--

INSERT INTO `pesan_barang` (`id_pesan`, `id_manager`, `tgl_pemesanan`, `no_pemesanan`, `nm_pengaju`, `nm_supplier`, `alamat_pengiriman`, `kode_barang`, `nm_brg`, `jml_brg`, `hrg_perunit`, `total_hrg`, `tgl_pengiriman_inginkan`) VALUES
(3, 1, '2025-02-03', 'A1', 'PT. INTIBOGA MANDIRI', 'PT. INDOFOOD', 'Jl. Sultan Adam', 'A123', 'Indomie Goreng', 300, 30000, 9000000, '2025-02-06');

-- --------------------------------------------------------

--
-- Table structure for table `piutang`
--

CREATE TABLE `piutang` (
  `id_pelanggan` int NOT NULL,
  `id_manager` int NOT NULL,
  `id_penjualan` int NOT NULL,
  `nm_pelanggan` varchar(100) NOT NULL,
  `nm_toko` varchar(100) NOT NULL,
  `no_hp` varchar(100) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `foto_ktp` varchar(100) NOT NULL,
  `tempo_pembayaran` varchar(100) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah_barang` varchar(100) NOT NULL,
  `total` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `piutang`
--

INSERT INTO `piutang` (`id_pelanggan`, `id_manager`, `id_penjualan`, `nm_pelanggan`, `nm_toko`, `no_hp`, `alamat`, `foto_ktp`, `tempo_pembayaran`, `nama_barang`, `jumlah_barang`, `total`) VALUES
(3, 1, 8, 'Daus', 'Toko Meriah', '087362842612', 'Jl. Belitung Darat', '', '2 Minggu', 'indomie goreng', '20', 700000);

-- --------------------------------------------------------

--
-- Table structure for table `stokbrg_periode`
--

CREATE TABLE `stokbrg_periode` (
  `id_stokperiode` int NOT NULL,
  `id_manager` int NOT NULL,
  `id_pesan` int NOT NULL,
  `id_penjualan` int NOT NULL,
  `saldo_akhir` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stokbrg_periode`
--

INSERT INTO `stokbrg_periode` (`id_stokperiode`, `id_manager`, `id_pesan`, `id_penjualan`, `saldo_akhir`) VALUES
(3, 0, 3, 8, 270);

-- --------------------------------------------------------

--
-- Table structure for table `stok_fisik`
--

CREATE TABLE `stok_fisik` (
  `id_stok_fisik` int NOT NULL,
  `nm_brg` varchar(100) NOT NULL,
  `kode_brg` varchar(100) NOT NULL,
  `jml_fisik` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_sistem`
--

CREATE TABLE `stok_sistem` (
  `id_stok_sistem` int NOT NULL,
  `id_stok_fisik` int NOT NULL,
  `id_stokperiode` int NOT NULL,
  `selisih` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stok_sistem`
--

INSERT INTO `stok_sistem` (`id_stok_sistem`, `id_stok_fisik`, `id_stokperiode`, `selisih`) VALUES
(1, 1, 1, 20);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `level` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `level`) VALUES
(1, 'admin', '$2y$10$oOYa2mS/6KgIjiDKm8eQgeQV0erYpoNowsJOXlImTfZVqygmrkD4K', 'admin'),
(2, 'sales', '$2y$10$m0AFfviw9akBjAa72tpMrOLDOj7cFgWg7FY2L.1K1SLcBueCo8M8y', 'sales'),
(3, 'dani', '$2y$10$fUDi4UIu7tC5CJLXJ5hXSOEFR0OS3Jy0Z2pF7d9roUgtCkT2HwwPu', ''),
(4, 'alfi', '$2y$10$ii0gRJJiB/u7EZyByMn3KeINOtMefRTm.gMASXD08WiCH0dfaOxFm', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arus_kas`
--
ALTER TABLE `arus_kas`
  ADD PRIMARY KEY (`id_arus_kas`);

--
-- Indexes for table `brg_keluar`
--
ALTER TABLE `brg_keluar`
  ADD PRIMARY KEY (`id_keluar`);

--
-- Indexes for table `brg_msk`
--
ALTER TABLE `brg_msk`
  ADD PRIMARY KEY (`id_masuk`);

--
-- Indexes for table `laba_bersih`
--
ALTER TABLE `laba_bersih`
  ADD PRIMARY KEY (`id_laba_bersih`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id_manager`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`);

--
-- Indexes for table `pesan_barang`
--
ALTER TABLE `pesan_barang`
  ADD PRIMARY KEY (`id_pesan`);

--
-- Indexes for table `piutang`
--
ALTER TABLE `piutang`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `stokbrg_periode`
--
ALTER TABLE `stokbrg_periode`
  ADD PRIMARY KEY (`id_stokperiode`);

--
-- Indexes for table `stok_fisik`
--
ALTER TABLE `stok_fisik`
  ADD PRIMARY KEY (`id_stok_fisik`);

--
-- Indexes for table `stok_sistem`
--
ALTER TABLE `stok_sistem`
  ADD PRIMARY KEY (`id_stok_sistem`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arus_kas`
--
ALTER TABLE `arus_kas`
  MODIFY `id_arus_kas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `brg_keluar`
--
ALTER TABLE `brg_keluar`
  MODIFY `id_keluar` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brg_msk`
--
ALTER TABLE `brg_msk`
  MODIFY `id_masuk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `laba_bersih`
--
ALTER TABLE `laba_bersih`
  MODIFY `id_laba_bersih` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id_manager` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pesan_barang`
--
ALTER TABLE `pesan_barang`
  MODIFY `id_pesan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `piutang`
--
ALTER TABLE `piutang`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stokbrg_periode`
--
ALTER TABLE `stokbrg_periode`
  MODIFY `id_stokperiode` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stok_fisik`
--
ALTER TABLE `stok_fisik`
  MODIFY `id_stok_fisik` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stok_sistem`
--
ALTER TABLE `stok_sistem`
  MODIFY `id_stok_sistem` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
