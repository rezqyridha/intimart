<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            b.id,
            b.nama_barang, 
            b.satuan,
            (
                COALESCE(bm.jumlah, 0) 
                - (COALESCE(bk.jumlah, 0) + COALESCE(pj.jumlah, 0) - COALESCE(rj.jumlah, 0))
            ) AS stok_akhir
        FROM barang b
        LEFT JOIN (
            SELECT id_barang, SUM(jumlah) AS jumlah FROM barang_masuk GROUP BY id_barang
        ) bm ON bm.id_barang = b.id
        LEFT JOIN (
            SELECT id_barang, SUM(jumlah) AS jumlah FROM barang_keluar GROUP BY id_barang
        ) bk ON bk.id_barang = b.id
        LEFT JOIN (
            SELECT id_barang, SUM(jumlah) AS jumlah FROM penjualan GROUP BY id_barang
        ) pj ON pj.id_barang = b.id
        LEFT JOIN (
            SELECT p.id_barang, SUM(r.jumlah) AS jumlah
            FROM retur_penjualan r
            JOIN penjualan p ON r.id_penjualan = p.id
            GROUP BY p.id_barang
        ) rj ON rj.id_barang = b.id
        WHERE (
            COALESCE(bm.jumlah, 0)
            - (COALESCE(bk.jumlah, 0) + COALESCE(pj.jumlah, 0) - COALESCE(rj.jumlah, 0))
        ) <= b.stok_minimum
        ORDER BY stok_akhir ASC
    ";

    $result = $koneksi->query($query);
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id'     => (int) $row['id'],
            'nama'   => $row['nama_barang'],
            'satuan' => $row['satuan'],
            'stok'   => (int) $row['stok_akhir'],
        ];
    }

    echo json_encode([
        'success' => true,
        'total'   => count($data),
        'items'   => $data,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error']);
}
