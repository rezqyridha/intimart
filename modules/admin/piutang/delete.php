<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=piutang");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

// Cek apakah data ada dan statusnya
$cek = $koneksi->prepare("SELECT status FROM piutang WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    $cek->close();
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

$cek->bind_result($status);
$cek->fetch();
$cek->close();

// Validasi: jika status lunas, tidak boleh dihapus
if (strtolower($status) === 'lunas') {
    header("Location: index.php?msg=locked&obj=piutang");
    exit;
}

// Eksekusi hapus
$stmt = $koneksi->prepare("DELETE FROM piutang WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=piutang");
} else {
    header("Location: index.php?msg=failed&obj=piutang");
}
exit;
