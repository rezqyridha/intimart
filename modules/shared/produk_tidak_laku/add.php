<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: index.php?msg=unauthorized&obj=tidaklaku");
    exit;
}

// Ambil input
$id_barang     = trim($_POST['id_barang'] ?? '');
$periode_awal  = trim($_POST['periode_awal'] ?? '');
$periode_akhir = trim($_POST['periode_akhir'] ?? '');
$jumlah        = trim($_POST['jumlah_terjual'] ?? '');
$keterangan    = trim($_POST['keterangan'] ?? '');
$status        = trim($_POST['status'] ?? 'diperiksa');

// Validasi
if (
    $id_barang === '' || $periode_awal === '' || $periode_akhir === '' ||
    $keterangan === '' || !in_array($status, ['diperiksa', 'tindaklanjut', 'selesai'])
) {
    header("Location: index.php?msg=kosong&obj=tidaklaku");
    exit;
}

if (!is_numeric($jumlah) || $jumlah < 0) $jumlah = 0;

// Cek duplikat data (barang & periode sama)
$cek = $koneksi->prepare("
    SELECT id FROM produk_tidak_laku
    WHERE id_barang = ? AND periode_awal = ? AND periode_akhir = ?
");
$cek->bind_param("iss", $id_barang, $periode_awal, $periode_akhir);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    header("Location: index.php?msg=duplicate&obj=tidaklaku");
    exit;
}

// Simpan data
$stmt = $koneksi->prepare("
    INSERT INTO produk_tidak_laku (id_barang, periode_awal, periode_akhir, jumlah_terjual, keterangan, status)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ississ", $id_barang, $periode_awal, $periode_akhir, $jumlah, $keterangan, $status);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=tidaklaku");
} else {
    header("Location: index.php?msg=failed&obj=tidaklaku");
}
exit;
