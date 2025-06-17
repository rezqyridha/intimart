<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=piutang");
    exit;
}

$id_sales = intval($_POST['id_sales'] ?? 0);
$tanggal  = trim($_POST['tanggal'] ?? '');
$jumlah   = trim($_POST['jumlah'] ?? '');
$status   = trim($_POST['status'] ?? 'belum lunas');

// Validasi kosong
if ($id_sales <= 0 || empty($tanggal) || empty($jumlah) || empty($status)) {
    header("Location: index.php?msg=kosong&obj=piutang");
    exit;
}

// Validasi jumlah numerik positif
if (!is_numeric($jumlah) || $jumlah <= 0) {
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

// Cek sales valid
$cekSales = $koneksi->prepare("SELECT id FROM user WHERE id = ? AND role = 'sales'");
$cekSales->bind_param("i", $id_sales);
$cekSales->execute();
$cekSales->store_result();

if ($cekSales->num_rows === 0) {
    $cekSales->close();
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}
$cekSales->close();

// Simpan ke DB
$stmt = $koneksi->prepare("INSERT INTO piutang (id_sales, tanggal, jumlah, status) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isds", $id_sales, $tanggal, $jumlah, $status);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=piutang");
} else {
    header("Location: index.php?msg=failed&obj=piutang");
}
exit;
