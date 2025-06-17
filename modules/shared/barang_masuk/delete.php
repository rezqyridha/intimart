<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

// Hanya admin yang boleh hapus
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=barang_masuk");
    exit;
}

// Validasi ID
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=barang_masuk");
    exit;
}

// Pastikan data ada
$cek = $koneksi->prepare("SELECT COUNT(*) FROM barang_masuk WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->bind_result($ada);
$cek->fetch();
$cek->close();

if ($ada == 0) {
    header("Location: index.php?msg=invalid&obj=barang_masuk");
    exit;
}

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM barang_masuk WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=barang_masuk");
} else {
    header("Location: index.php?msg=failed&obj=barang_masuk");
}
$stmt->close();
exit;
