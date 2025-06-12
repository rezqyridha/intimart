<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=rekonsiliasi");
    exit;
}

// Ambil dan sanitasi input
$id_pembayaran = $_POST['id_pembayaran'] ?? '';
$catatan       = trim($_POST['catatan'] ?? '');
$status        = $_POST['status'] ?? 'sesuai'; // default dari ENUM

// Validasi
if ($id_pembayaran === '' || $catatan === '') {
    header("Location: index.php?msg=kosong&obj=rekonsiliasi");
    exit;
}

// Validasi status harus sesuai enum
if (!in_array($status, ['sesuai', 'tidak sesuai'])) {
    header("Location: index.php?msg=invalid&obj=rekonsiliasi");
    exit;
}

// Validasi duplikat (1 pembayaran hanya boleh 1 rekonsiliasi)
$cek = $koneksi->prepare("SELECT id FROM rekonsiliasi_pembayaran WHERE id_pembayaran = ?");
$cek->bind_param("i", $id_pembayaran);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    header("Location: index.php?msg=duplicate&obj=rekonsiliasi");
    exit;
}

// Simpan ke database (tanggal_rekonsiliasi otomatis oleh TIMESTAMP DEFAULT)
$stmt = $koneksi->prepare("INSERT INTO rekonsiliasi_pembayaran (id_pembayaran, catatan, status) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $id_pembayaran, $catatan, $status);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=rekonsiliasi");
} else {
    header("Location: index.php?msg=failed&obj=rekonsiliasi");
}
exit;
