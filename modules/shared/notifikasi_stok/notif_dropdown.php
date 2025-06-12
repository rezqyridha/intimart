<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
header('Content-Type: application/json');

$query = "
    SELECT 
        b.nama_barang,
        b.satuan,
        COALESCE(masuk.total_masuk, 0)
        - (COALESCE(keluar.total_keluar, 0) + COALESCE(pj.total_terjual, 0) - COALESCE(retur.total_retur, 0)) AS stok_akhir
    FROM barang b
    LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_masuk FROM barang_masuk GROUP BY id_barang) masuk ON b.id = masuk.id_barang
    LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_keluar FROM barang_keluar GROUP BY id_barang) keluar ON b.id = keluar.id_barang
    LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_terjual FROM penjualan GROUP BY id_barang) pj ON b.id = pj.id_barang
    LEFT JOIN (
        SELECT p.id_barang, SUM(rp.jumlah) AS total_retur
        FROM retur_penjualan rp
        JOIN penjualan p ON rp.id_penjualan = p.id
        GROUP BY p.id_barang
    ) retur ON b.id = retur.id_barang
    WHERE (
        COALESCE(masuk.total_masuk, 0)
        - (COALESCE(keluar.total_keluar, 0) + COALESCE(pj.total_terjual, 0) - COALESCE(retur.total_retur, 0))
    ) <= b.stok_minimum
    ORDER BY stok_akhir ASC
    LIMIT 5
";

$data = [];
$result = $koneksi->query($query);
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
