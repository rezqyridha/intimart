<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

// Hanya admin & karyawan yang boleh menambahkan
if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=stok_fisik");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang     = (int)($_POST['id_barang'] ?? 0);
    $jumlah_fisik  = (int)($_POST['jumlah_fisik'] ?? 0);
    $lokasi        = trim($_POST['lokasi'] ?? '');
    $tanggal       = $_POST['tanggal'] ?? '';
    $keterangan    = trim($_POST['keterangan'] ?? '');
    $id_user       = $_SESSION['id_user'] ?? 0;

    // Validasi input
    if ($id_barang <= 0 || $jumlah_fisik <= 0 || empty($lokasi) || empty($tanggal) || $id_user <= 0) {
        header("Location: index.php?msg=kosong&obj=stok_fisik");
        exit;
    }

    // Validasi barang
    $cek_barang = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE id = ?");
    $cek_barang->bind_param("i", $id_barang);
    $cek_barang->execute();
    $cek_barang->bind_result($barang_ada);
    $cek_barang->fetch();
    $cek_barang->close();
    if ($barang_ada == 0) {
        header("Location: index.php?msg=invalid&obj=stok_fisik");
        exit;
    }

    // Validasi user
    $cek_user = $koneksi->prepare("SELECT COUNT(*) FROM user WHERE id = ?");
    $cek_user->bind_param("i", $id_user);
    $cek_user->execute();
    $cek_user->bind_result($user_ada);
    $cek_user->fetch();
    $cek_user->close();
    if ($user_ada == 0) {
        header("Location: index.php?msg=invalid&obj=stok_fisik");
        exit;
    }

    // Simpan ke database
    $stmt = $koneksi->prepare("INSERT INTO stok_fisik (id_barang, jumlah_fisik, lokasi, tanggal, keterangan, id_user) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssi", $id_barang, $jumlah_fisik, $lokasi, $tanggal, $keterangan, $id_user);

    if ($stmt->execute()) {
        header("Location: index.php?msg=added&obj=stok_fisik");
    } else {
        header("Location: index.php?msg=failed&obj=stok_fisik");
    }

    $stmt->close();
    exit;
}
