<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?msg=invalid&obj=pengiriman");
    exit;
}

$tujuan = trim($_POST['tujuan'] ?? '');
$tanggal_kirim = $_POST['tanggal_kirim'] ?? '';
$estimasi_tiba = $_POST['estimasi_tiba'] ?? null;
$id_barangs = $_POST['id_barang'] ?? [];
$jumlahs = $_POST['jumlah'] ?? [];

// Validasi data
if ($tujuan === '' || $tanggal_kirim === '' || empty($id_barangs) || empty($jumlahs)) {
    header("Location: index.php?msg=kosong&obj=pengiriman");
    exit;
}

if (count($id_barangs) !== count($jumlahs)) {
    header("Location: index.php?msg=invalid&obj=pengiriman");
    exit;
}

// Simpan ke tabel pengiriman
$stmt = $koneksi->prepare("INSERT INTO pengiriman (tujuan, tanggal_kirim, estimasi_tiba, status_pengiriman, created_at) VALUES (?, ?, ?, 'diproses', NOW())");
$stmt->bind_param("sss", $tujuan, $tanggal_kirim, $estimasi_tiba);

if (!$stmt->execute()) {
    header("Location: index.php?msg=error&obj=pengiriman");
    exit;
}

$id_pengiriman = $stmt->insert_id;
$stmt->close();

// Simpan ke pengiriman_detail
$stmt_detail = $koneksi->prepare("INSERT INTO pengiriman_detail (id_pengiriman, id_barang, jumlah) VALUES (?, ?, ?)");
foreach ($id_barangs as $i => $id_barang) {
    $jumlah = (int) $jumlahs[$i];
    $stmt_detail->bind_param("iii", $id_pengiriman, $id_barang, $jumlah);
    $stmt_detail->execute();
}
$stmt_detail->close();

header("Location: index.php?msg=success&obj=pengiriman");
exit;
