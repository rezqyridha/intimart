<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=user");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$loginId = $_SESSION['user_id'] ?? 0;

// Cegah hapus diri sendiri
if ($id === $loginId) {
    header("Location: index.php?msg=locked&obj=user");
    exit;
}

// Validasi data ada
$cek = $koneksi->prepare("SELECT id FROM user WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    $cek->close();
    header("Location: index.php?msg=invalid&obj=user");
    exit;
}
$cek->close();

// Hapus
$stmt = $koneksi->prepare("DELETE FROM user WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=user");
} else {
    header("Location: index.php?msg=failed&obj=user");
}
exit;
