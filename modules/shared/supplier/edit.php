<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

// Ambil dan filter input
$id     = trim($_POST['id'] ?? '');
$nama   = trim($_POST['nama'] ?? '');
$kontak = trim($_POST['kontak'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

// Validasi wajib
if ($id === '' || $nama === '') {
    header("Location: index.php?msg=kosong&obj=supplier");
    exit;
}

// Validasi kontak (opsional)
if ($kontak !== '' && !preg_match('/^[0-9+\-\s]{6,20}$/', $kontak)) {
    header("Location: index.php?msg=invalid&obj=supplier");
    exit;
}

// Update data
$stmt = $koneksi->prepare("UPDATE supplier SET nama_supplier = ?, kontak = ?, alamat = ? WHERE id = ?");
$stmt->bind_param("sssi", $nama, $kontak, $alamat, $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=updated&obj=supplier");
} else {
    header("Location: index.php?msg=failed&obj=supplier");
}
exit;
