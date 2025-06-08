<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=stok_fisik");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=stok_fisik");
    exit;
}

// Cek apakah data stok fisik ada
$cek = $koneksi->prepare("SELECT COUNT(*) FROM stok_fisik WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->bind_result($ada);
$cek->fetch();
$cek->close();

if ($ada == 0) {
    header("Location: index.php?msg=invalid&obj=stok_fisik");
    exit;
}

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM stok_fisik WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=deleted&obj=stok_fisik");
} else {
    // Jika gagal karena FK atau lainnya
    header("Location: index.php?msg=fk_blocked&obj=stok_fisik");
}
$stmt->close();
exit;
