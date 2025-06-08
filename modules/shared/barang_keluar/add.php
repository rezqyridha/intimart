<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

// Hanya admin dan karyawan yang boleh menambah
if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=barang_keluar");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $tanggal   = $_POST['tanggal'] ?? '';
    $tujuan    = trim($_POST['tujuan'] ?? '');
    $id_user   = $_SESSION['id_user'] ?? 0;

    // Validasi input kosong
    if ($id_barang <= 0 || $jumlah <= 0 || empty($tanggal) || empty($tujuan) || $id_user <= 0) {
        header("Location: index.php?msg=kosong&obj=barang_keluar");
        exit;
    }

    // Cek apakah barang tersedia
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE id = ?");
    $cek->bind_param("i", $id_barang);
    $cek->execute();
    $cek->bind_result($ada);
    $cek->fetch();
    $cek->close();

    if ($ada == 0) {
        header("Location: index.php?msg=invalid&obj=barang_keluar");
        exit;
    }

    // Simpan ke database
    $stmt = $koneksi->prepare("INSERT INTO barang_keluar (id_barang, id_user, tanggal, jumlah, tujuan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $id_barang, $id_user, $tanggal, $jumlah, $tujuan);

    if ($stmt->execute()) {
        header("Location: index.php?msg=added&obj=barang_keluar");
    } else {
        header("Location: index.php?msg=failed&obj=barang_keluar");
    }

    $stmt->close();
    exit;
}
