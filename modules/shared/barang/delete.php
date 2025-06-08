<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=barang");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=barang");
    exit;
}

$cekRelasi = [
    'barang_masuk'        => 'SELECT COUNT(*) FROM barang_masuk WHERE id_barang = ?',
    'barang_keluar'       => 'SELECT COUNT(*) FROM barang_keluar WHERE id_barang = ?',
    'barang_kadaluarsa'   => 'SELECT COUNT(*) FROM barang_kadaluarsa WHERE id_barang = ?',
    'detail_restok'       => 'SELECT COUNT(*) FROM detail_restok WHERE id_barang = ?',
    'penjualan'           => 'SELECT COUNT(*) FROM penjualan WHERE id_barang = ?',
    'pengiriman'          => 'SELECT COUNT(*) FROM pengiriman WHERE id_barang = ?',
    'pemesanan'           => 'SELECT COUNT(*) FROM pemesanan WHERE id_barang = ?',
    'stok'                => 'SELECT COUNT(*) FROM stok WHERE id_barang = ?'
];

foreach ($cekRelasi as $query) {
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    if ($total > 0) {
        header("Location: index.php?msg=fk_blocked&obj=barang");
        exit;
    }
}

$stmt = $koneksi->prepare("DELETE FROM barang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute() ?
    header("Location: index.php?msg=deleted&obj=barang") :
    header("Location: index.php?msg=failed&obj=barang");
$stmt->close();
