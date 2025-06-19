<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'karyawan') {
    header("Location: index.php?msg=unauthorized&obj=gudang");
    exit;
}

$nama_gudang = trim($_POST['nama_gudang'] ?? '');
$alamat      = trim($_POST['alamat'] ?? '');

// Validasi kosong
if ($nama_gudang === '' || $alamat === '') {
    header("Location: index.php?msg=kosong&obj=gudang");
    exit;
}

// Cek duplikat
$cek = $koneksi->prepare("SELECT id FROM gudang WHERE nama_gudang = ?");
$cek->bind_param("s", $nama_gudang);
$cek->execute();
$cek->store_result();
if ($cek->num_rows > 0) {
    $cek->close();
    header("Location: index.php?msg=duplicate&obj=gudang");
    exit;
}
$cek->close();

// Simpan ke DB
$stmt = $koneksi->prepare("INSERT INTO gudang (nama_gudang, alamat) VALUES (?, ?)");
$stmt->bind_param("ss", $nama_gudang, $alamat);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=gudang");
} else {
    header("Location: index.php?msg=failed&obj=gudang");
}
exit;
