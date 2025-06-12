<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// Hanya admin yang boleh menghapus
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=rekonsiliasi");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=rekonsiliasi");
    exit;
}

// Cek status finalisasi data
$cek = $koneksi->prepare("SELECT is_final FROM rekonsiliasi_pembayaran WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$res = $cek->get_result()->fetch_assoc();

if (!$res) {
    header("Location: index.php?msg=notfound&obj=rekonsiliasi");
    exit;
}

// ❌ Jika sudah final → tidak bisa dihapus
if ($res['is_final'] == 1) {
    header("Location: index.php?msg=locked&obj=rekonsiliasi");
    exit;
}

// Lanjutkan hapus
$stmt = $koneksi->prepare("DELETE FROM rekonsiliasi_pembayaran WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=rekonsiliasi");
} else {
    header("Location: index.php?msg=fk_blocked&obj=rekonsiliasi");
}
exit;
