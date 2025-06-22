<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Validasi dan filter input
$id_barang      = intval($_POST['id_barang'] ?? 0);
$periode_awal   = $_POST['periode_awal'] ?? '';
$periode_akhir  = $_POST['periode_akhir'] ?? '';
$jumlah_terjual = intval($_POST['jumlah_terjual'] ?? 0);
$status         = $_POST['status'] ?? 'diperiksa';
$keterangan     = trim($_POST['keterangan'] ?? '');

if (!$id_barang || !$periode_awal || !$periode_akhir || $jumlah_terjual < 0) {
    header("Location: index.php?msg=invalid&obj=tidaklaku");
    exit;
}

// Simpan ke database
$stmt = $koneksi->prepare("
    INSERT INTO produk_tidak_laku (id_barang, periode_awal, periode_akhir, jumlah_terjual, status, keterangan)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ississ", $id_barang, $periode_awal, $periode_akhir, $jumlah_terjual, $status, $keterangan);
$stmt->execute();

// Redirect kembali ke index dengan notifikasi sukses
header("Location: index.php?msg=added&obj=tidaklaku");
exit;
