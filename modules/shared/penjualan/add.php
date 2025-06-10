<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// Ambil data dari form
$id_barang        = trim($_POST['id_barang'] ?? '');
$id_sales         = trim($_POST['id_sales'] ?? '');
$tanggal          = trim($_POST['tanggal'] ?? '');
$jumlah           = trim($_POST['jumlah'] ?? '');
$harga_total      = trim($_POST['harga_total'] ?? '');
$status_pelunasan = trim($_POST['status_pelunasan'] ?? 'belum lunas');

// Validasi input dasar
if ($id_barang === '' || $id_sales === '' || $tanggal === '' || $jumlah === '' || $harga_total === '') {
    header("Location: index.php?msg=kosong&obj=penjualan");
    exit;
}

// Validasi numerik
if (!is_numeric($jumlah) || $jumlah <= 0 || !is_numeric($harga_total) || $harga_total <= 0) {
    header("Location: index.php?msg=invalid&obj=penjualan");
    exit;
}

// Validasi stok tersedia
$qMasuk = $koneksi->query("SELECT SUM(jumlah) AS total FROM barang_masuk WHERE id_barang = $id_barang");
$stok_masuk = (int) ($qMasuk->fetch_assoc()['total'] ?? 0);

$qKeluar = $koneksi->query("SELECT SUM(jumlah) AS total FROM barang_keluar WHERE id_barang = $id_barang");
$stok_keluar_manual = (int) ($qKeluar->fetch_assoc()['total'] ?? 0);

$qJual = $koneksi->query("SELECT SUM(jumlah) AS total FROM penjualan WHERE id_barang = $id_barang");
$stok_terjual = (int) ($qJual->fetch_assoc()['total'] ?? 0);

$stok_tersedia = $stok_masuk - ($stok_keluar_manual + $stok_terjual);

if ($jumlah > $stok_tersedia) {
    header("Location: index.php?msg=failed&obj=penjualan");
    exit;
}

// Simpan ke DB
$stmt = $koneksi->prepare("INSERT INTO penjualan (id_barang, id_sales, tanggal, jumlah, harga_total, status_pelunasan) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisids", $id_barang, $id_sales, $tanggal, $jumlah, $harga_total, $status_pelunasan);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=penjualan");
} else {
    header("Location: index.php?msg=failed&obj=penjualan");
}
exit;
