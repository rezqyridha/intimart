<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$role     = $_SESSION['role'] ?? '';
$id_user  = $_SESSION['id_user'] ?? 0;

if ($role !== 'sales') {
    header("Location: index.php?msg=unauthorized&obj=pemesanan");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=pemesanan");
    exit;
}

// Hanya bisa hapus milik sendiri & status masih menunggu
$stmt = $koneksi->prepare("
    DELETE FROM pemesanan
    WHERE id = ? AND id_sales = ? AND status = 'menunggu'
");
$stmt->bind_param("ii", $id, $id_user);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=pemesanan");
} else {
    header("Location: index.php?msg=locked_or_notfound&obj=pemesanan");
}
exit;
