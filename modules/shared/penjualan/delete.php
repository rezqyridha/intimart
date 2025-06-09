<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// RBAC: hanya admin yang boleh hapus
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=penjualan");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=penjualan");
    exit;
}

$stmt = $koneksi->prepare("DELETE FROM penjualan WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=deleted&obj=penjualan");
} else {
    header("Location: index.php?msg=failed&obj=penjualan");
}
exit;
