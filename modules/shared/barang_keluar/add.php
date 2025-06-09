<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$id_barang  = trim($_POST['id_barang'] ?? '');
$jumlah     = trim($_POST['jumlah'] ?? '');
$tanggal    = trim($_POST['tanggal'] ?? '');
$jenis      = trim($_POST['jenis'] ?? '');
$tujuan     = trim($_POST['tujuan'] ?? null);
$keterangan = trim($_POST['keterangan'] ?? null);
$id_user    = $_SESSION['id_user'] ?? null;

// Validasi wajib
if ($id_barang === '' || $jumlah === '' || $tanggal === '' || $jenis === '' || !$id_user) {
    header("Location: index.php?msg=kosong&obj=barang_keluar");
    exit;
}

// Validasi nilai jumlah & jenis
$allowedJenis = ['internal', 'rusak', 'hilang', 'retur_supplier'];
if (!is_numeric($jumlah) || $jumlah <= 0 || !in_array($jenis, $allowedJenis)) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

// Simpan ke database
$stmt = $koneksi->prepare("INSERT INTO barang_keluar 
    (id_barang, id_user, tanggal, jumlah, jenis, tujuan, keterangan) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisssss", $id_barang, $id_user, $tanggal, $jumlah, $jenis, $tujuan, $keterangan);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=barang_keluar");
} else {
    header("Location: index.php?msg=failed&obj=barang_keluar");
}
exit;
