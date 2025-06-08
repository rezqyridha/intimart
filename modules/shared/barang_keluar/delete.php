<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

// Hanya admin yang diizinkan menghapus
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=barang_keluar");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

// Cek data ada atau tidak
$cek = $koneksi->prepare("SELECT COUNT(*) FROM barang_keluar WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->bind_result($ada);
$cek->fetch();
$cek->close();

if ($ada == 0) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM barang_keluar WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute() ?
    header("Location: index.php?msg=deleted&obj=barang_keluar") :
    header("Location: index.php?msg=failed&obj=barang_keluar");
$stmt->close();
exit;
