<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: index.php?msg=unauthorized&obj=restok_supplier");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=restok_supplier");
    exit;
}

// Cek apakah data ada dan status masih diproses
$stmt = $koneksi->prepare("SELECT status FROM restok_supplier WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

if (!$status) {
    header("Location: index.php?msg=invalid&obj=restok_supplier");
    exit;
}

if ($status !== 'diproses') {
    header("Location: index.php?msg=locked&obj=restok_supplier");
    exit;
}

// Hapus data
$hapus = $koneksi->prepare("DELETE FROM restok_supplier WHERE id = ?");
$hapus->bind_param("i", $id);
if ($hapus->execute()) {
    header("Location: index.php?msg=deleted&obj=restok_supplier");
} else {
    header("Location: index.php?msg=failed&obj=restok_supplier");
}
$hapus->close();
exit;
