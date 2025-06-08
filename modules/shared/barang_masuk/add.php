<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

// Hanya admin & karyawan yang boleh mengakses
if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=barang_masuk");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang   = (int)($_POST['id_barang'] ?? 0);
    $jumlah      = (int)($_POST['jumlah'] ?? 0);
    $tanggal     = $_POST['tanggal'] ?? '';
    $keterangan  = trim($_POST['keterangan'] ?? '');
    $id_user     = $_SESSION['id_user'] ?? 0;


    // Validasi input wajib
    if ($id_barang <= 0 || $jumlah <= 0 || empty($tanggal) || $id_user <= 0) {
        header("Location: index.php?msg=kosong&obj=barang_masuk");
        exit;
    }

    // Validasi id_barang benar-benar ada
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE id = ?");
    $cek->bind_param("i", $id_barang);
    $cek->execute();
    $cek->bind_result($ada);
    $cek->fetch();
    $cek->close();

    if ($ada == 0) {
        header("Location: index.php?msg=invalid&obj=barang_masuk");
        exit;
    }

    // Validasi id_user valid (untuk mencegah FK error)
    $cekUser = $koneksi->prepare("SELECT COUNT(*) FROM user WHERE id = ?");
    $cekUser->bind_param("i", $id_user);
    $cekUser->execute();
    $cekUser->bind_result($user_ada);
    $cekUser->fetch();
    $cekUser->close();

    if ($user_ada == 0) {
        header("Location: index.php?msg=invalid&obj=barang_masuk");
        exit;
    }

    // Simpan data
    $stmt = $koneksi->prepare("INSERT INTO barang_masuk (id_barang, id_user, jumlah, tanggal, keterangan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $id_barang, $id_user, $jumlah, $tanggal, $keterangan);

    if ($stmt->execute()) {
        header("Location: index.php?msg=added&obj=barang_masuk");
    } else {
        header("Location: index.php?msg=failed&obj=barang_masuk");
    }

    $stmt->close();
    exit;
}
