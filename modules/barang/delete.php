<?php
require '../../session_start.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php?error=unauthorized");
    exit;
}
require '../../koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?msg=invalid");
    exit;
}

$id = (int)$_GET['id'];

// Cek apakah barang digunakan di tabel lain (contoh: barang_kadaluarsa)
$cek = $conn->prepare("SELECT 1 FROM barang_kadaluarsa WHERE id_barang = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    // Ada relasi, tidak bisa dihapus
    header("Location: index.php?msg=fk_blocked");
    exit;
}

$cek->close();

// Jika aman dihapus
$stmt = $conn->prepare("DELETE FROM barang WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=deleted");
} else {
    header("Location: index.php?msg=failed");
}
$stmt->close();
$conn->close();
