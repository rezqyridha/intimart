<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

$id = $_GET['id'] ?? '';

// Validasi ID
if ($id === '' || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=supplier");
    exit;
}

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM supplier WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=deleted&obj=supplier");
} else {
    if ($koneksi->errno === 1451) {
        header("Location: index.php?msg=fk_blocked&obj=supplier");
    } else {
        header("Location: index.php?msg=failed&obj=supplier");
    }
}
exit;
