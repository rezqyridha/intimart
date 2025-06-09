<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

$id = $_GET['id'] ?? '';

// Validasi ID
if ($id === '' || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=pelanggan");
    exit;
}

$stmt = $koneksi->prepare("DELETE FROM pelanggan WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Jika berhasil hapus
    header("Location: index.php?msg=deleted&obj=pelanggan");
} else {
    // Jika gagal karena foreign key (misalnya ada relasi transaksi)
    if ($koneksi->errno === 1451) {
        header("Location: index.php?msg=fk_blocked&obj=pelanggan");
    } else {
        header("Location: index.php?msg=failed&obj=pelanggan");
    }
}
exit;
