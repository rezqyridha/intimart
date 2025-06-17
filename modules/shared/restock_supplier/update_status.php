<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: index.php?msg=unauthorized&obj=restok_supplier");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

$allowed = ['diproses', 'dikirim', 'selesai', 'batal'];
if ($id <= 0 || !in_array($status, $allowed)) {
    header("Location: index.php?msg=invalid&obj=restok_supplier");
    exit;
}

// Validasi data ada dan status belum final
$cek = $koneksi->prepare("SELECT status FROM restok_supplier WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->bind_result($statusLama);
$cek->fetch();
$cek->close();

if (!$statusLama || in_array($statusLama, ['selesai', 'batal'])) {
    header("Location: index.php?msg=locked&obj=restok_supplier");
    exit;
}

// Update status
$stmt = $koneksi->prepare("UPDATE restok_supplier SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);
if ($stmt->execute()) {
    header("Location: index.php?msg=updated&obj=restok_supplier");
} else {
    header("Location: index.php?msg=failed&obj=restok_supplier");
}
$stmt->close();
exit;
