<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'manajer', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=restok_supplier");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_supplier = (int)($_POST['id_supplier'] ?? 0);
    $tgl_pesan   = trim($_POST['tgl_pesan'] ?? '');
    $catatan     = trim($_POST['catatan'] ?? '');
    $id_user     = $_SESSION['id_user'] ?? 0;
    $status      = 'diproses';

    // Validasi wajib
    if ($id_supplier <= 0 || empty($tgl_pesan) || $id_user <= 0) {
        header("Location: index.php?msg=kosong&obj=restok_supplier");
        exit;
    }

    // Validasi supplier
    $cekSupplier = $koneksi->prepare("SELECT COUNT(*) FROM supplier WHERE id = ?");
    $cekSupplier->bind_param("i", $id_supplier);
    $cekSupplier->execute();
    $cekSupplier->bind_result($ada);
    $cekSupplier->fetch();
    $cekSupplier->close();

    if ($ada == 0) {
        header("Location: index.php?msg=invalid&obj=restok_supplier");
        exit;
    }

    // Simpan ke DB
    $stmt = $koneksi->prepare("
        INSERT INTO restok_supplier (id_supplier, tgl_pesan, status, catatan)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $id_supplier, $tgl_pesan, $status, $catatan);

    if ($stmt->execute()) {
        header("Location: index.php?msg=added&obj=restok_supplier");
    } else {
        header("Location: index.php?msg=failed&obj=restok_supplier");
    }

    $stmt->close();
    exit;
}
