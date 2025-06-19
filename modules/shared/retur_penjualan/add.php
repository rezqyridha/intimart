<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$role = $_SESSION['role'] ?? '';
$id_user = $_SESSION['id_user'] ?? 0;

// Hanya admin, karyawan, atau sales yang boleh mengakses
if (!in_array($role, ['admin', 'karyawan', 'sales'])) {
    header("Location: index.php?msg=unauthorized&obj=retur");
    exit;
}

// Ambil input
$id_penjualan = intval($_POST['id_penjualan'] ?? 0);
$jumlah       = intval($_POST['jumlah'] ?? 0);
$alasan       = trim($_POST['alasan'] ?? '');
$tanggal      = trim($_POST['tanggal'] ?? '');

// Validasi kosong
if (!$id_penjualan || !$jumlah || $alasan === '' || $tanggal === '') {
    header("Location: index.php?msg=kosong&obj=retur");
    exit;
}

// Validasi logis
if ($jumlah <= 0) {
    header("Location: index.php?msg=invalid&obj=retur");
    exit;
}

// Ambil data penjualan
$stmt = $koneksi->prepare("SELECT jumlah, id_sales FROM penjualan WHERE id = ?");
$stmt->bind_param("i", $id_penjualan);
$stmt->execute();
$result = $stmt->get_result();
$penjualan = $result->fetch_assoc();

// Jika penjualan tidak ditemukan
if (!$penjualan) {
    header("Location: index.php?msg=notfound&obj=retur");
    exit;
}

// Jika sales, pastikan hanya retur penjualan miliknya
if ($role === 'sales' && $penjualan['id_sales'] != $id_user) {
    header("Location: index.php?msg=unauthorized&obj=retur");
    exit;
}

// Validasi retur tidak lebih dari jumlah jual
if ($jumlah > $penjualan['jumlah']) {
    $maks = $penjualan['jumlah'];
    header("Location: index.php?msg=melebihi&obj=retur&maks=$maks");
    exit;
}

// Cek apakah sudah retur untuk penjualan ini
$cek = $koneksi->prepare("SELECT COUNT(*) AS total FROM retur_penjualan WHERE id_penjualan = ?");
$cek->bind_param("i", $id_penjualan);
$cek->execute();
$cek_result = $cek->get_result()->fetch_assoc();

if ($cek_result['total'] > 0) {
    header("Location: index.php?msg=duplicate&obj=retur");
    exit;
}

// Simpan data
$insert = $koneksi->prepare("
    INSERT INTO retur_penjualan (id_penjualan, jumlah, alasan, tanggal)
    VALUES (?, ?, ?, ?)
");
$insert->bind_param("iiss", $id_penjualan, $jumlah, $alasan, $tanggal);

if ($insert->execute()) {
    header("Location: index.php?msg=added&obj=retur");
} else {
    header("Location: index.php?msg=failed&obj=retur");
}
exit;
