<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=kadaluarsa");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=kadaluarsa");
    exit;
}

// Pastikan data ada sebelum menghapus
$cek = $koneksi->prepare("SELECT id FROM barang_kadaluarsa WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$hasil = $cek->get_result()->fetch_assoc();

if (!$hasil) {
    header("Location: index.php?msg=notfound&obj=kadaluarsa");
    exit;
}

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM barang_kadaluarsa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=kadaluarsa");
} else {
    header("Location: index.php?msg=failed&obj=kadaluarsa");
}
exit;
