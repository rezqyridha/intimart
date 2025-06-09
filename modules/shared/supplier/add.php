<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

// Ambil dan filter input
$nama   = trim($_POST['nama'] ?? '');
$kontak  = trim($_POST['kontak'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

// Validasi input wajib
if ($nama === '') {
    header("Location: index.php?msg=kosong&obj=supplier");
    exit;
}

// Validasi kontak opsional (angka, +, -, spasi)
if ($kontak !== '' && !preg_match('/^[0-9+\-\s]{6,20}$/', $kontak)) {
    header("Location: index.php?msg=invalid&obj=supplier");
    exit;
}

// Simpan ke database
$stmt = $koneksi->prepare("INSERT INTO supplier (nama_supplier, kontak, alamat) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $kontak, $alamat);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=supplier");
} else {
    header("Location: index.php?msg=failed&obj=supplier");
}
exit;
