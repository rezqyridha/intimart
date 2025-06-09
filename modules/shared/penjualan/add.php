<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$id_barang = trim($_POST['id_barang'] ?? '');
$jumlah    = trim($_POST['jumlah'] ?? '');
$tanggal   = trim($_POST['tanggal'] ?? '');

if ($id_barang === '' || $jumlah === '' || $tanggal === '' || !is_numeric($jumlah) || $jumlah <= 0) {
    header("Location: index.php?msg=kosong&obj=penjualan");
    exit;
}

// Ambil harga jual
$stmt = $koneksi->prepare("SELECT harga_jual FROM barang WHERE id = ?");
$stmt->bind_param("i", $id_barang);
$stmt->execute();
$result = $stmt->get_result();
$data_barang = $result->fetch_assoc();

if (!$data_barang) {
    header("Location: index.php?msg=invalid&obj=penjualan");
    exit;
}

$harga_jual = $data_barang['harga_jual'];

// Hitung stok masuk
$qMasuk = $koneksi->query("SELECT SUM(jumlah) AS total FROM barang_masuk WHERE id_barang = $id_barang");
$stok_masuk = (int) ($qMasuk->fetch_assoc()['total'] ?? 0);

// Hitung total barang keluar (manual + penjualan)
$qKeluar = $koneksi->query("SELECT SUM(jumlah) AS total FROM barang_keluar WHERE id_barang = $id_barang");
$stok_keluar_manual = (int) ($qKeluar->fetch_assoc()['total'] ?? 0);

$qJual = $koneksi->query("SELECT SUM(jumlah) AS total FROM penjualan WHERE id_barang = $id_barang");
$stok_terjual = (int) ($qJual->fetch_assoc()['total'] ?? 0);

$stok_tersedia = $stok_masuk - ($stok_keluar_manual + $stok_terjual);

if ($jumlah > $stok_tersedia) {
    header("Location: index.php?msg=failed&obj=penjualan");
    exit;
}

$harga_total = $jumlah * $harga_jual;

// Simpan data penjualan
$stmt_insert = $koneksi->prepare("INSERT INTO penjualan (id_barang, jumlah, total, tanggal) VALUES (?, ?, ?, ?)");
$stmt_insert->bind_param("iids", $id_barang, $jumlah, $harga_total, $tanggal);

if ($stmt_insert->execute()) {
    header("Location: index.php?msg=added&obj=penjualan");
} else {
    header("Location: index.php?msg=failed&obj=penjualan");
}
exit;
