<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: index.php?msg=unauthorized&obj=tidaklaku");
    exit;
}

$id = $_GET['id'] ?? '';
if (!is_numeric($id) || $id === '') {
    header("Location: index.php?msg=invalid&obj=tidaklaku");
    exit;
}

// Validasi data ada
$stmt = $koneksi->prepare("SELECT id FROM produk_tidak_laku WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: index.php?msg=notfound&obj=tidaklaku");
    exit;
}
$stmt->close();

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM produk_tidak_laku WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: index.php?msg=deleted&obj=tidaklaku");
} else {
    header("Location: index.php?msg=fk_blocked&obj=tidaklaku");
}
exit;
