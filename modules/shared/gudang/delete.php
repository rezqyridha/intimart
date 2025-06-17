<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=gudang");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=gudang");
    exit;
}

// Cek apakah gudang digunakan (misal di tabel stok atau pengiriman)
$cekRelasi = $koneksi->query("
    SELECT 1 FROM stok WHERE id_gudang = $id LIMIT 1
    UNION
    SELECT 1 FROM pengiriman WHERE id_gudang = $id LIMIT 1
");
if ($cekRelasi && $cekRelasi->num_rows > 0) {
    header("Location: index.php?msg=fk_blocked&obj=gudang");
    exit;
}

// Lanjut hapus
$stmt = $koneksi->prepare("DELETE FROM gudang WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: index.php?msg=deleted&obj=gudang");
} else {
    header("Location: index.php?msg=failed&obj=gudang");
}
exit;
