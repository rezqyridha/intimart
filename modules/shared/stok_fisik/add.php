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
    $koreksi       = isset($_POST['koreksi']) ? 1 : 0;
    $stok_sistem   = null;

    // Validasi input wajib
    if ($id_barang <= 0 || $jumlah_fisik <= 0 || empty($lokasi) || empty($tanggal) || $id_user <= 0) {
        header("Location: index.php?msg=kosong&obj=stok_fisik");
        exit;
    }

    // Validasi data barang
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

    // Ambil stok sistem real-time jika checkbox koreksi dicentang
    if ($koreksi === 1) {
        $query = "
            SELECT 
                IFNULL(masuk.total_masuk, 0)
              - (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0)) AS stok_akhir
            FROM barang b
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) AS total_masuk FROM barang_masuk GROUP BY id_barang
            ) masuk ON b.id = masuk.id_barang
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) AS total_keluar FROM barang_keluar GROUP BY id_barang
            ) keluar ON b.id = keluar.id_barang
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) AS total_terjual FROM penjualan GROUP BY id_barang
            ) pj ON b.id = pj.id_barang
            LEFT JOIN (
                SELECT p.id_barang, SUM(r.jumlah) AS total_retur
                FROM retur r JOIN penjualan p ON r.id_penjualan = p.id
                GROUP BY p.id_barang
            ) retur ON b.id = retur.id_barang
            WHERE b.id = ?
        ";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();
        $stmt->bind_result($stok_sistem);
        $stmt->fetch();
        $stmt->close();
        if (!is_numeric($stok_sistem)) {
            $stok_sistem = null;
        }
    }

    // Simpan data ke tabel stok_fisik
    $stmt = $koneksi->prepare("
        INSERT INTO stok_fisik (id_barang, jumlah_fisik, stok_sistem, koreksi, lokasi, tanggal, keterangan, id_user)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiisssi", $id_barang, $jumlah_fisik, $stok_sistem, $koreksi, $lokasi, $tanggal, $keterangan, $id_user);

    if ($stmt->execute()) {
        header("Location: index.php?msg=added&obj=stok_fisik");
    } else {
        header("Location: index.php?msg=failed&obj=stok_fisik");
    }
    $stmt->close();
    exit;
}
