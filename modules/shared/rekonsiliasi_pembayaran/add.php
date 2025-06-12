<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$role     = $_SESSION['role'] ?? '';
$id_user  = $_SESSION['id_user'] ?? 0;

if (!in_array($role, ['admin', 'sales'])) {
    header("Location: index.php?msg=unauthorized&obj=rekonsiliasi");
    exit;
}

// Ambil & sanitasi input
$id_pembayaran = intval($_POST['id_pembayaran'] ?? 0);
$catatan       = trim($_POST['catatan'] ?? '');
$status        = $_POST['status'] ?? null;

// Validasi input wajib
if ($id_pembayaran <= 0 || $catatan === '') {
    header("Location: index.php?msg=kosong&obj=rekonsiliasi");
    exit;
}

// Validasi duplikat
$cek = $koneksi->prepare("SELECT id FROM rekonsiliasi_pembayaran WHERE id_pembayaran = ?");
$cek->bind_param("i", $id_pembayaran);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    header("Location: index.php?msg=duplicate&obj=rekonsiliasi");
    exit;
}

// ✅ Logika role
if ($role === 'sales') {
    // Sales: hanya input tanpa status, is_final default = 0
    $stmt = $koneksi->prepare("
        INSERT INTO rekonsiliasi_pembayaran (id_pembayaran, catatan)
        VALUES (?, ?)
    ");
    $stmt->bind_param("is", $id_pembayaran, $catatan);
} else {
    // Admin: input status dan langsung finalisasi
    if (!in_array($status, ['sesuai', 'tidak sesuai'])) {
        header("Location: index.php?msg=invalid&obj=rekonsiliasi");
        exit;
    }

    $stmt = $koneksi->prepare("
        INSERT INTO rekonsiliasi_pembayaran (id_pembayaran, catatan, status, is_final)
        VALUES (?, ?, ?, 1)
    ");
    $stmt->bind_param("iss", $id_pembayaran, $catatan, $status);
}

// Eksekusi & redirect
if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=rekonsiliasi");
} else {
    header("Location: index.php?msg=failed&obj=rekonsiliasi");
}
exit;
