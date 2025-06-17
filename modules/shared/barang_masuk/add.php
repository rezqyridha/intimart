<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=barang_masuk");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang   = (int)($_POST['id_barang'] ?? 0);
    $id_gudang   = (int)($_POST['id_gudang'] ?? 0);
    $jumlah      = (int)($_POST['jumlah'] ?? 0);
    $tanggal     = $_POST['tanggal'] ?? '';
    $keterangan  = trim($_POST['keterangan'] ?? '');
    $id_user     = $_SESSION['id_user'] ?? 0;

    // Validasi input wajib
    if ($id_barang <= 0 || $id_gudang <= 0 || $jumlah <= 0 || empty($tanggal) || $id_user <= 0) {
        header("Location: index.php?msg=kosong&obj=barang_masuk");
        exit;
    }

    // Validasi barang
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE id = ?");
    $cek->bind_param("i", $id_barang);
    $cek->execute();
    $cek->bind_result($ada_barang);
    $cek->fetch();
    $cek->close();

    if ($ada_barang == 0) {
        header("Location: index.php?msg=invalid&obj=barang_masuk");
        exit;
    }

    // Validasi gudang
    $cekGudang = $koneksi->prepare("SELECT COUNT(*) FROM gudang WHERE id = ?");
    $cekGudang->bind_param("i", $id_gudang);
    $cekGudang->execute();
    $cekGudang->bind_result($ada_gudang);
    $cekGudang->fetch();
    $cekGudang->close();

    if ($ada_gudang == 0) {
        header("Location: index.php?msg=invalid&obj=barang_masuk");
        exit;
    }

    // Validasi user
    $cekUser = $koneksi->prepare("SELECT COUNT(*) FROM user WHERE id = ?");
    $cekUser->bind_param("i", $id_user);
    $cekUser->execute();
    $cekUser->bind_result($ada_user);
    $cekUser->fetch();
    $cekUser->close();

    if ($ada_user == 0) {
        header("Location: index.php?msg=invalid&obj=barang_masuk");
        exit;
    }

    // Simpan ke database
    $stmt = $koneksi->prepare("INSERT INTO barang_masuk (id_barang, id_gudang, id_user, jumlah, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiss", $id_barang, $id_gudang, $id_user, $jumlah, $tanggal, $keterangan);

    if ($stmt->execute()) {
        header("Location: index.php?msg=added&obj=barang_masuk");
    } else {
        header("Location: index.php?msg=failed&obj=barang_masuk");
    }

    $stmt->close();
    exit;
}
