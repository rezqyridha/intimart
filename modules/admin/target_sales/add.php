<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=target");
    exit;
}

$id_sales = intval($_POST['id_sales'] ?? 0);
$bulan    = trim($_POST['bulan'] ?? '');
$target   = trim($_POST['target'] ?? '');

if ($id_sales <= 0 || empty($bulan) || empty($target)) {
    header("Location: index.php?msg=kosong&obj=target");
    exit;
}

if (!is_numeric($target) || $target <= 0) {
    header("Location: index.php?msg=invalid&obj=target");
    exit;
}

$today = date('Y-m');
if ($bulan < $today) {
    header("Location: index.php?msg=invalid&obj=target");
    exit;
}

// Cek sales valid
$cekSales = $koneksi->prepare("SELECT id FROM user WHERE id = ? AND role = 'sales'");
$cekSales->bind_param("i", $id_sales);
$cekSales->execute();
$cekSales->store_result();
if ($cekSales->num_rows === 0) {
    $cekSales->close();
    header("Location: index.php?msg=invalid&obj=target");
    exit;
}
$cekSales->close();

// Cek duplikat
$cekDup = $koneksi->prepare("SELECT id FROM target_sales WHERE id_sales = ? AND bulan = ?");
$cekDup->bind_param("is", $id_sales, $bulan);
$cekDup->execute();
$cekDup->store_result();
if ($cekDup->num_rows > 0) {
    $cekDup->close();
    header("Location: index.php?msg=duplicate&obj=target");
    exit;
}
$cekDup->close();

// Simpan
$stmt = $koneksi->prepare("INSERT INTO target_sales (id_sales, bulan, target, realisasi) VALUES (?, ?, ?, 0)");
$stmt->bind_param("isd", $id_sales, $bulan, $target);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=target");
} else {
    header("Location: index.php?msg=failed&obj=target");
}
exit;
