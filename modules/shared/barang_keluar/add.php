<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=barang_masuk");
    exit;
}

$id_barang  = (int)($_POST['id_barang'] ?? 0);
$id_gudang  = (int)($_POST['id_gudang'] ?? 0);
$jumlah     = trim($_POST['jumlah'] ?? '');
$tanggal    = trim($_POST['tanggal'] ?? '');
$jenis      = trim($_POST['jenis'] ?? '');
$tujuan     = trim($_POST['tujuan'] ?? null);
$keterangan = trim($_POST['keterangan'] ?? null);
$id_user    = $_SESSION['id_user'] ?? null;

// Validasi wajib
if ($id_barang <= 0 || $id_gudang <= 0 || $jumlah === '' || $tanggal === '' || $jenis === '' || !$id_user) {
    header("Location: index.php?msg=kosong&obj=barang_keluar");
    exit;
}

// Validasi nilai jumlah & jenis
$allowedJenis = ['internal', 'rusak', 'hilang', 'retur_supplier'];
if (!is_numeric($jumlah) || $jumlah <= 0 || !in_array($jenis, $allowedJenis)) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

// Validasi barang
$cek = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE id = ?");
$cek->bind_param("i", $id_barang);
$cek->execute();
$cek->bind_result($ada_barang);
$cek->fetch();
$cek->close();

if (!$ada_barang) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

// Validasi gudang
$cekG = $koneksi->prepare("SELECT COUNT(*) FROM gudang WHERE id = ?");
$cekG->bind_param("i", $id_gudang);
$cekG->execute();
$cekG->bind_result($ada_gudang);
$cekG->fetch();
$cekG->close();

if (!$ada_gudang) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

// Simpan ke database
$stmt = $koneksi->prepare("INSERT INTO barang_keluar 
    (id_barang, id_gudang, id_user, tanggal, jumlah, jenis, tujuan, keterangan) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiisisss", $id_barang, $id_gudang, $id_user, $tanggal, $jumlah, $jenis, $tujuan, $keterangan);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=barang_keluar");
} else {
    header("Location: index.php?msg=failed&obj=barang_keluar");
}
exit;
