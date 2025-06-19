<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role     = $_SESSION['role'];
$id_user  = $_SESSION['id_user'];

if (!in_array($role, ['admin', 'sales'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Ambil & sanitasi input
$id_penjualan   = intval($_POST['id_penjualan'] ?? 0);
$jumlah_bayar   = floatval($_POST['jumlah_bayar'] ?? 0);
$metode         = trim($_POST['metode'] ?? '');
$keterangan     = trim($_POST['keterangan'] ?? '');
$tanggal        = trim($_POST['tanggal'] ?? date('Y-m-d'));

// Validasi dasar
if (!$id_penjualan || !$jumlah_bayar || !$metode || !$tanggal) {
    header("Location: index.php?msg=kosong&obj=pembayaran");
    exit;
}
if ($jumlah_bayar <= 0) {
    header("Location: index.php?msg=invalid&obj=pembayaran");
    exit;
}

// Validasi akses khusus sales
if ($role === 'sales') {
    $cek = $koneksi->query("SELECT id FROM penjualan WHERE id = $id_penjualan AND id_sales = $id_user");
    if ($cek->num_rows === 0) {
        header("Location: index.php?msg=unauthorized");
        exit;
    }
}

// Ambil total transaksi
$qTotal = $koneksi->query("SELECT harga_total FROM penjualan WHERE id = $id_penjualan");
$total_penjualan = floatval($qTotal->fetch_assoc()['harga_total'] ?? 0);

// Hitung total pembayaran sebelumnya
$qBayar = $koneksi->query("SELECT SUM(jumlah_bayar) AS total_bayar FROM pembayaran WHERE id_penjualan = $id_penjualan");
$bayar_sebelumnya = floatval($qBayar->fetch_assoc()['total_bayar'] ?? 0);

// Validasi pelunasan
if ($bayar_sebelumnya >= $total_penjualan) {
    header("Location: index.php?msg=duplicate&obj=pembayaran");
    exit;
}
if (($bayar_sebelumnya + $jumlah_bayar) > $total_penjualan) {
    header("Location: index.php?msg=invalid&obj=pembayaran&info=overpaid");
    exit;
}

// Simpan ke DB
$stmt = $koneksi->prepare("
    INSERT INTO pembayaran (id_penjualan, jumlah_bayar, metode, keterangan, tanggal)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("idsss", $id_penjualan, $jumlah_bayar, $metode, $keterangan, $tanggal);

if ($stmt->execute()) {
    // Update status pelunasan jika lunas
    $total_bayar = $bayar_sebelumnya + $jumlah_bayar;
    if ($total_bayar >= $total_penjualan) {
        $koneksi->query("UPDATE penjualan SET status_pelunasan = 'lunas' WHERE id = $id_penjualan");
    }

    header("Location: index.php?msg=added&obj=pembayaran");
} else {
    header("Location: index.php?msg=failed&obj=pembayaran");
}
exit;
