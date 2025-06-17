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

// Cek koreksi
$stmt = $koneksi->prepare("SELECT koreksi FROM stok_fisik WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($koreksi);
$stmt->fetch();
$stmt->close();

// Tidak bisa hapus jika data dikoreksi
if ((int)$koreksi === 1) {
    header("Location: index.php?msg=locked&obj=stok_fisik");
    exit;
}

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM stok_fisik WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=deleted&obj=stok_fisik");
} else {
    header("Location: index.php?msg=failed&obj=stok_fisik");
}
$stmt->close();
exit;
