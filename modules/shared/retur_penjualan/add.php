<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=retur");
    exit;
}

$id_penjualan = trim($_POST['id_penjualan'] ?? '');
$jumlah       = trim($_POST['jumlah'] ?? '');
$alasan       = trim($_POST['alasan'] ?? '');
$tanggal      = trim($_POST['tanggal'] ?? '');

if ($id_penjualan === '' || $jumlah === '' || $alasan === '' || $tanggal === '') {
    header("Location: index.php?msg=kosong&obj=retur");
    exit;
}

if (!is_numeric($jumlah) || $jumlah <= 0) {
    header("Location: index.php?msg=invalid&obj=retur");
    exit;
}

// Validasi transaksi penjualan
$query = $koneksi->prepare("SELECT jumlah FROM penjualan WHERE id = ?");
$query->bind_param("i", $id_penjualan);
$query->execute();
$result = $query->get_result();
$penjualan = $result->fetch_assoc();

if (!$penjualan) {
    header("Location: index.php?msg=invalid&obj=retur");
    exit;
}

// Hitung total retur sebelumnya
$qRetur = $koneksi->query("SELECT SUM(jumlah) AS total FROM retur WHERE id_penjualan = $id_penjualan");
$retur_sebelumnya = (int) ($qRetur->fetch_assoc()['total'] ?? 0);

// Validasi retur tidak melebihi penjualan
if ($jumlah > ($penjualan['jumlah'] - $retur_sebelumnya)) {
    header("Location: index.php?msg=failed&obj=retur");
    exit;
}

// Simpan retur
$stmt = $koneksi->prepare("INSERT INTO retur (id_penjualan, jumlah, alasan, tanggal) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $id_penjualan, $jumlah, $alasan, $tanggal);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=retur");
} else {
    header("Location: index.php?msg=failed&obj=retur");
}
exit;
