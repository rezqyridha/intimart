<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'] ?? null;
if ($role !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=pembayaran");
    exit;
}

// Ambil id_penjualan sebelum hapus
$q = $koneksi->prepare("SELECT id_penjualan FROM pembayaran WHERE id = ?");
$q->bind_param("i", $id);
$q->execute();
$q->bind_result($id_penjualan);
if (!$q->fetch()) {
    header("Location: index.php?msg=invalid&obj=pembayaran");
    exit;
}
$q->close();

// Hapus pembayaran
$stmt = $koneksi->prepare("DELETE FROM pembayaran WHERE id = ?");
$stmt->bind_param("i", $id);
$success = $stmt->execute();
$stmt->close();

// Update ulang status pelunasan jika pembayaran berkurang
if ($success) {
    // Ambil total harga dari penjualan
    $qTotal = $koneksi->query("SELECT harga_total FROM penjualan WHERE id = $id_penjualan");
    $harga_total = $qTotal->fetch_assoc()['harga_total'] ?? 0;

    // Hitung total pembayaran setelah penghapusan
    $qBayar = $koneksi->query("SELECT SUM(jumlah_bayar) AS total_bayar FROM pembayaran WHERE id_penjualan = $id_penjualan");
    $bayar = $qBayar->fetch_assoc()['total_bayar'] ?? 0;

    // Jika belum lunas, ubah status
    if ($bayar < $harga_total) {
        $koneksi->query("UPDATE penjualan SET status_pelunasan = 'belum lunas' WHERE id = $id_penjualan");
    }

    header("Location: index.php?msg=deleted&obj=pembayaran");
} else {
    header("Location: index.php?msg=failed&obj=pembayaran");
}
