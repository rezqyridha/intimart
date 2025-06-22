<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $role = $_SESSION['role'];

    if (!in_array($role, ['manajer', 'admin'])) {
        header("Location: index.php?error=Akses ditolak");
        exit;
    }

    $allowed = ['diperiksa', 'tindaklanjut', 'selesai'];
    if (!in_array($status, $allowed)) {
        header("Location: index.php?error=Status tidak valid");
        exit;
    }

    $stmt = $koneksi->prepare("UPDATE produk_tidak_laku SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=tidaklaku");
    } else {
        header("Location: index.php?msg=error&obj=tidaklaku");
    }
} else {
    header("Location: index.php?msg=invalid&obj=tidaklaku");
}
exit;
