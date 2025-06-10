<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
if ($role !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Ambil input
$id_penjualan = $_POST['id_penjualan'] ?? '';
$jumlah_bayar = $_POST['jumlah_bayar'] ?? '';
$metode = $_POST['metode'] ?? '';
$keterangan = $_POST['keterangan'] ?? null;
$tanggal = $_POST['tanggal'] ?? date('Y-m-d');

// Validasi kosong
if (empty($id_penjualan) || empty($jumlah_bayar) || empty($metode) || empty($tanggal)) {
    header("Location: index.php?msg=kosong&obj=pembayaran");
    exit;
}

// Ambil total transaksi dari penjualan
$qTotal = $koneksi->query("SELECT harga_total FROM penjualan WHERE id = $id_penjualan");
$dataTotal = $qTotal->fetch_assoc();
$total_penjualan = $dataTotal['harga_total'] ?? 0;

// Hitung total pembayaran sebelumnya
$qBayar = $koneksi->query("SELECT SUM(jumlah_bayar) AS total_bayar FROM pembayaran WHERE id_penjualan = $id_penjualan");
$bayarSebelumnya = $qBayar->fetch_assoc()['total_bayar'] ?? 0;

// Cek apakah sudah lunas
if ($bayarSebelumnya >= $total_penjualan) {
    header("Location: index.php?msg=duplicate&obj=pembayaran");
    exit;
}

// Cek apakah jumlah_bayar akan menyebabkan kelebihan bayar
if (($bayarSebelumnya + $jumlah_bayar) > $total_penjualan) {
    header("Location: index.php?msg=invalid&obj=pembayaran");
    exit;
}

// Simpan pembayaran
$stmt = $koneksi->prepare("INSERT INTO pembayaran (id_penjualan, jumlah_bayar, metode, keterangan, tanggal) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("idsss", $id_penjualan, $jumlah_bayar, $metode, $keterangan, $tanggal);

if ($stmt->execute()) {
    // Update status pelunasan jika lunas
    $totalSetelahBayar = $bayarSebelumnya + $jumlah_bayar;
    if ($totalSetelahBayar >= $total_penjualan) {
        $koneksi->query("UPDATE penjualan SET status_pelunasan = 'lunas' WHERE id = $id_penjualan");
    }

    header("Location: index.php?msg=added&obj=pembayaran");
} else {
    header("Location: index.php?msg=failed&obj=pembayaran");
}
