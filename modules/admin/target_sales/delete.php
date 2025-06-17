<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=target");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=target");
    exit;
}

// Cek apakah data ada dan ambil bulan
$get = $koneksi->prepare("SELECT bulan FROM target_sales WHERE id = ?");
$get->bind_param("i", $id);
$get->execute();
$get->bind_result($bulan);
$get->fetch();
$get->close();

if (!$bulan) {
    header("Location: index.php?msg=invalid&obj=target");
    exit;
}

if ($bulan < date('Y-m')) {
    header("Location: index.php?msg=locked&obj=target");
    exit;
}

// Lanjut hapus
$stmt = $koneksi->prepare("DELETE FROM target_sales WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=target");
} else {
    header("Location: index.php?msg=failed&obj=target");
}
exit;
