<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$role = $_SESSION['role'];
$idSession = $_SESSION['id_user']; // ✅ Perbaikan utama

// Ambil data dari form dengan sanitasi
$id_barang        = intval($_POST['id_barang'] ?? 0);
$id_sales         = intval($_POST['id_sales'] ?? 0); // Akan dioverride jika role sales
$tanggal          = trim($_POST['tanggal'] ?? '');
$jumlah           = intval($_POST['jumlah'] ?? 0);
$harga_total      = floatval($_POST['harga_total'] ?? 0);
$status_pelunasan = trim($_POST['status_pelunasan'] ?? 'belum lunas');

// Jika sales login, paksa id_sales ke session
if ($role === 'sales') {
    $id_sales = $idSession;
}

// Validasi hak akses role
if (!in_array($role, ['admin', 'sales'])) {
    header("Location: index.php?msg=unauthorized");
    exit;
}

// Validasi input wajib
if (!$id_barang || !$id_sales || !$tanggal || !$jumlah || !$harga_total) {
    header("Location: index.php?msg=kosong&obj=penjualan");
    exit;
}

// Validasi nilai logis
if ($jumlah <= 0 || $harga_total <= 0) {
    header("Location: index.php?msg=invalid&obj=penjualan");
    exit;
}

// Hitung stok tersedia dari 3 sumber
$qMasuk  = $koneksi->query("SELECT SUM(jumlah) AS total FROM barang_masuk WHERE id_barang = $id_barang");
$qKeluar = $koneksi->query("SELECT SUM(jumlah) AS total FROM barang_keluar WHERE id_barang = $id_barang");
$qJual   = $koneksi->query("SELECT SUM(jumlah) AS total FROM penjualan WHERE id_barang = $id_barang");

$stok_masuk         = (int) ($qMasuk->fetch_assoc()['total'] ?? 0);
$stok_keluar_manual = (int) ($qKeluar->fetch_assoc()['total'] ?? 0);
$stok_terjual       = (int) ($qJual->fetch_assoc()['total'] ?? 0);
$stok_tersedia      = $stok_masuk - ($stok_keluar_manual + $stok_terjual);

// Validasi stok cukup
if ($jumlah > $stok_tersedia) {
    header("Location: index.php?msg=failed&obj=penjualan&stok=kurang");
    exit;
}

// Simpan data penjualan
$stmt = $koneksi->prepare("
    INSERT INTO penjualan (id_barang, id_sales, tanggal, jumlah, harga_total, status_pelunasan)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("iisids", $id_barang, $id_sales, $tanggal, $jumlah, $harga_total, $status_pelunasan);

// Redirect dengan notifikasi hasil
if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=penjualan");
} else {
    header("Location: index.php?msg=failed&obj=penjualan");
}
exit;
