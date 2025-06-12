<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

header('Content-Type: application/json');

// Hitung jumlah barang yang stok akhir <= stok_minimum
$query = "
    SELECT COUNT(*) AS total_menipis
    FROM (
        SELECT b.id,
            IFNULL(masuk.total_masuk, 0) -
            (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0)) AS stok_akhir,
            b.stok_minimum
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
            FROM retur_penjualan r 
            JOIN penjualan p ON r.id_penjualan = p.id 
            GROUP BY p.id_barang
        ) retur ON b.id = retur.id_barang
    ) stok_data
    WHERE stok_akhir <= stok_minimum
";

$result = $koneksi->query($query);
$data = $result->fetch_assoc();
echo json_encode(['total' => intval($data['total_menipis'])]);
