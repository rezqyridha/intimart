<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

$nama   = trim($_POST['nama'] ?? '');
$no_hp  = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

if ($nama === '') {
    header("Location: index.php?msg=kosong&obj=pelanggan");
    exit;
}

if ($no_hp !== '' && !preg_match('/^[0-9+\-\s]{6,20}$/', $no_hp)) {
    header("Location: index.php?msg=invalid&obj=pelanggan");
    exit;
}

$stmt = $koneksi->prepare("INSERT INTO pelanggan (nama_pelanggan, no_hp, alamat) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $no_hp, $alamat);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=pelanggan");
} else {
    header("Location: index.php?msg=failed&obj=pelanggan");
}
exit;
