<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?msg=invalid&obj=pengiriman");
    exit;
}

$tujuan = trim($_POST['tujuan'] ?? '');
$tanggal_kirim = $_POST['tanggal_kirim'] ?? '';
$estimasi_tiba = $_POST['estimasi_tiba'] ?? null;
$id_barangs = $_POST['id_barang'] ?? [];
$jumlahs = $_POST['jumlah'] ?? [];

// Validasi awal
if ($tujuan === '' || $tanggal_kirim === '' || empty($id_barangs) || empty($jumlahs) || count($id_barangs) !== count($jumlahs)) {
    header("Location: index.php?msg=kosong&obj=pengiriman");
    exit;
}

// Validasi stok berdasarkan stok sistem (real-time)
foreach ($id_barangs as $i => $id_barang) {
    $jumlah = (int) $jumlahs[$i];

    $sql = "
        SELECT 
            IFNULL(masuk.total_masuk, 0) -
            (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0)) AS stok_akhir
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
            FROM retur r
            JOIN penjualan p ON r.id_penjualan = p.id
            GROUP BY p.id_barang
        ) retur ON b.id = retur.id_barang
        WHERE b.id = ?
    ";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id_barang);
    $stmt->execute();
    $stmt->bind_result($stok_akhir);
    $stmt->fetch();
    $stmt->close();

    if ($jumlah > $stok_akhir) {
        header("Location: index.php?msg=stok_limit&obj=pengiriman");
        exit;
    }
}

// Simpan ke tabel pengiriman
$stmt = $koneksi->prepare("INSERT INTO pengiriman (tujuan, tanggal_kirim, estimasi_tiba, status_pengiriman, created_at) VALUES (?, ?, ?, 'diproses', NOW())");
$stmt->bind_param("sss", $tujuan, $tanggal_kirim, $estimasi_tiba);

if (!$stmt->execute()) {
    header("Location: index.php?msg=error&obj=pengiriman");
    exit;
}

$id_pengiriman = $stmt->insert_id;
$stmt->close();

// Simpan ke pengiriman_detail
$stmt_detail = $koneksi->prepare("INSERT INTO pengiriman_detail (id_pengiriman, id_barang, jumlah) VALUES (?, ?, ?)");
foreach ($id_barangs as $i => $id_barang) {
    $jumlah = (int) $jumlahs[$i];
    $stmt_detail->bind_param("iii", $id_pengiriman, $id_barang, $jumlah);
    $stmt_detail->execute();
}
$stmt_detail->close();

header("Location: index.php?msg=success&obj=pengiriman");
exit;
