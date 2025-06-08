<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

// Hanya admin yang boleh hapus
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=pengiriman");
    exit;
}

$id = $_GET['id'] ?? '';
if ($id === '' || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=pengiriman");
    exit;
}

// Cek apakah data ada
$cek = $koneksi->query("SELECT id FROM pengiriman WHERE id = $id");
if ($cek->num_rows === 0) {
    header("Location: index.php?msg=notfound&obj=pengiriman");
    exit;
}

// Hapus detail terlebih dahulu
$koneksi->query("DELETE FROM pengiriman_detail WHERE id_pengiriman = $id");

// Hapus utama
$koneksi->query("DELETE FROM pengiriman WHERE id = $id");

header("Location: index.php?msg=deleted&obj=pengiriman");
exit;
