<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=rekonsiliasi");
    exit;
}

$id = $_GET['id'] ?? '';
if (!is_numeric($id) || $id === '') {
    header("Location: index.php?msg=invalid&obj=rekonsiliasi");
    exit;
}

// Cek status terlebih dahulu
$cek = $koneksi->prepare("SELECT status FROM rekonsiliasi_pembayaran WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$res = $cek->get_result()->fetch_assoc();

if (!$res) {
    header("Location: index.php?msg=notfound&obj=rekonsiliasi");
    exit;
}

if ($res['status'] === 'sudah_rekonsiliasi') {
    header("Location: index.php?msg=locked&obj=rekonsiliasi");
    exit;
}

// Hapus jika valid
$stmt = $koneksi->prepare("DELETE FROM rekonsiliasi_pembayaran WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=deleted&obj=rekonsiliasi");
} else {
    header("Location: index.php?msg=fk_blocked&obj=rekonsiliasi");
}
exit;
