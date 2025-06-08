<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=barang");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $satuan = trim($_POST['satuan'] ?? '');
    $harga_beli = (int)($_POST['harga_beli'] ?? 0);
    $harga_jual = (int)($_POST['harga_jual'] ?? 0);
    $stok_minimum = (int)($_POST['stok_minimum'] ?? 0);

    if ($nama_barang === '' || $satuan === '' || $harga_beli <= 0 || $harga_jual <= 0) {
        header("Location: index.php?msg=kosong&obj=barang");
        exit;
    }

    $cek = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE nama_barang = ? AND satuan = ?");
    $cek->bind_param("ss", $nama_barang, $satuan);
    $cek->execute();
    $cek->bind_result($ada);
    $cek->fetch();
    $cek->close();

    if ($ada > 0) {
        header("Location: index.php?msg=duplicate&obj=barang");
        exit;
    }

    $stmt = $koneksi->prepare("INSERT INTO barang (nama_barang, satuan, harga_beli, harga_jual, stok_minimum) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $nama_barang, $satuan, $harga_beli, $harga_jual, $stok_minimum);
    $stmt->execute() ?
        header("Location: index.php?msg=added&obj=barang") :
        header("Location: index.php?msg=failed&obj=barang");
    $stmt->close();
}
