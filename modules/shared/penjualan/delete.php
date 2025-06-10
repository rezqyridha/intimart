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

// Cek relasi ke pembayaran
$cek_pembayaran = $koneksi->query("SELECT 1 FROM pembayaran WHERE id_penjualan = $id LIMIT 1");
if ($cek_pembayaran->num_rows > 0) {
    header("Location: index.php?msg=fk_blocked&obj=penjualan");
    exit;
}

// Cek relasi ke retur
$cek_retur = $koneksi->query("SELECT 1 FROM retur WHERE id_penjualan = $id LIMIT 1");
if ($cek_retur->num_rows > 0) {
    header("Location: index.php?msg=fk_blocked&obj=penjualan");
    exit;
}

// Lanjut hapus
$stmt = $koneksi->prepare("DELETE FROM penjualan WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=penjualan");
} else {
    header("Location: index.php?msg=failed&obj=penjualan");
}
exit;
