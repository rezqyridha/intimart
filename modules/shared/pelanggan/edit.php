<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

// Ambil dan filter input
$id     = trim($_POST['id'] ?? '');
$nama   = trim($_POST['nama'] ?? '');
$no_hp  = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

// Validasi wajib
if ($id === '' || $nama === '') {
    header("Location: index.php?msg=kosong&obj=pelanggan");
    exit;
}

// Validasi no_hp opsional (angka, +, -, spasi)
if ($no_hp !== '' && !preg_match('/^[0-9+\-\s]{6,20}$/', $no_hp)) {
    header("Location: index.php?msg=invalid&obj=pelanggan");
    exit;
}

// Eksekusi update
$stmt = $koneksi->prepare("UPDATE pelanggan SET nama_pelanggan = ?, no_hp = ?, alamat = ? WHERE id = ?");
$stmt->bind_param("sssi", $nama, $no_hp, $alamat, $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=updated&obj=pelanggan");
} else {
    header("Location: index.php?msg=failed&obj=pelanggan");
}
exit;
