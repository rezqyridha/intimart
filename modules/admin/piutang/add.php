<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=piutang");
    exit;
}

$id_penjualan = intval($_POST['id_penjualan'] ?? 0);
$tanggal      = trim($_POST['tanggal'] ?? '');
$jumlah       = trim($_POST['jumlah'] ?? '');
$status       = trim($_POST['status'] ?? 'belum lunas');



// Validasi input kosong
if ($id_penjualan <= 0 || empty($tanggal) || empty($jumlah) || empty($status)) {
    header("Location: index.php?msg=kosong&obj=piutang");
    exit;
}

// Validasi jumlah numerik positif
if (!is_numeric($jumlah) || $jumlah <= 0) {
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

// Ambil id_sales dan harga_total dari penjualan
$getPenjualan = $koneksi->prepare("SELECT id_sales, harga_total FROM penjualan WHERE id = ?");
$getPenjualan->bind_param("i", $id_penjualan);
$getPenjualan->execute();
$getPenjualan->bind_result($id_sales, $harga_total);
$getPenjualan->fetch();
$getPenjualan->close();

// Validasi data ditemukan
if (!$id_sales || !$harga_total) {
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

// Validasi jumlah piutang tidak boleh melebihi harga_total
if ($jumlah > $harga_total) {
    header("Location: index.php?msg=overlimit&obj=piutang");
    exit;
}

// Simpan ke database
$stmt = $koneksi->prepare("INSERT INTO piutang (id_sales, id_penjualan, tanggal, jumlah, status) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $id_sales, $id_penjualan, $tanggal, $jumlah, $status);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=piutang");
} else {
    header("Location: index.php?msg=failed&obj=piutang");
}
exit;
