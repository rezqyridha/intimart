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

// Validasi apakah transaksi penjualan ada
$stmt = $koneksi->prepare("SELECT jumlah FROM penjualan WHERE id = ?");
$stmt->bind_param("i", $id_penjualan);
$stmt->execute();
$res = $stmt->get_result();
$penjualan = $res->fetch_assoc();

if (!$penjualan) {
    header("Location: index.php?msg=invalid&obj=retur");
    exit;
}

// Cek apakah sudah pernah retur untuk penjualan ini
$cekDuplikat = $koneksi->prepare("SELECT COUNT(*) AS total FROM retur_penjualan WHERE id_penjualan = ?");
$cekDuplikat->bind_param("i", $id_penjualan);
$cekDuplikat->execute();
$duplikat = $cekDuplikat->get_result()->fetch_assoc();

if ($duplikat['total'] > 0) {
    header("Location: index.php?msg=duplicate&obj=retur");
    exit;
}

// Validasi jumlah retur tidak melebihi jumlah penjualan
if ($jumlah > $penjualan['jumlah']) {
    $maks = $penjualan['jumlah'];
    header("Location: index.php?msg=melebihi&obj=retur&maks=$maks");
    exit;
}


// Simpan retur
$insert = $koneksi->prepare("INSERT INTO retur_penjualan (id_penjualan, jumlah, alasan, tanggal) VALUES (?, ?, ?, ?)");
$insert->bind_param("iiss", $id_penjualan, $jumlah, $alasan, $tanggal);

if ($insert->execute()) {
    header("Location: index.php?msg=added&obj=retur");
} else {
    header("Location: index.php?msg=error&obj=retur");
}
exit;
