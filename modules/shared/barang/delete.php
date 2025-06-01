<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/koneksi.php';

// Validasi role admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: /intimart/index.php?error=unauthorized");
    exit;
}

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /intimart/modules/shared/barang/index.php?msg=invalid");
    exit;
}

$id = (int) $_GET['id'];

// Cek relasi ke tabel lain (misal: barang_kadaluarsa)
$cek = $conn->prepare("SELECT 1 FROM barang_kadaluarsa WHERE id_barang = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    // Barang masih digunakan, tidak boleh dihapus
    $cek->close();
    header("Location: /intimart/modules/shared/barang/index.php?msg=fk_blocked");
    exit;
}
$cek->close();

// Eksekusi penghapusan
$stmt = $conn->prepare("DELETE FROM barang WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg = 'deleted';
} else {
    $msg = 'failed';
}

$stmt->close();
$conn->close();

header("Location: /intimart/modules/shared/barang/index.php?msg=$msg");
exit;
