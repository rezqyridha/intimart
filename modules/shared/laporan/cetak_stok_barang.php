<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once FPDF_PATH . '/fpdf.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer', 'karyawan'])) {
    die("Akses ditolak.");
}

$query = "
    SELECT b.id, b.nama_barang, b.satuan, b.stok_minimum,
        IFNULL(masuk.total_masuk, 0) AS stok_masuk,
        IFNULL(keluar.total_keluar, 0) AS stok_keluar_manual,
        IFNULL(pj.total_terjual, 0) AS stok_terjual,
        IFNULL(retur.total_retur, 0) AS stok_retur,
        sf.jumlah_fisik,
        sf.koreksi,
        (
            IFNULL(masuk.total_masuk, 0)
            - (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0))
        ) AS stok_akhir
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
        SELECT p.id_barang, SUM(rp.jumlah) AS total_retur
        FROM retur_penjualan rp JOIN penjualan p ON rp.id_penjualan = p.id
        GROUP BY p.id_barang
    ) retur ON b.id = retur.id_barang
    LEFT JOIN (
        SELECT id_barang, jumlah_fisik, koreksi FROM stok_fisik WHERE koreksi = 1 ORDER BY tanggal DESC
    ) sf ON b.id = sf.id_barang
    WHERE sf.koreksi = 1
    ORDER BY b.nama_barang ASC
";

$data = $koneksi->query($query);

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, 'PT. INTI BOGA MANDIRI', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Distributor Resmi Indomie & PopMie Kalimantan Selatan - Tengah', 0, 1, 'C');
$pdf->Cell(0, 5, 'Jl. Pasar Baru 87 - 89 Kertak Baru Ilir Banjar Barat, Banjarmasin', 0, 1, 'C');
$pdf->Cell(0, 5, 'Telp: 0511-3360373, 0511-4366629, 0511-4369746', 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'LAPORAN STOK BARANG DIKOREKSI', 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Nama Barang', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Satuan', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Sistem', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Fisik', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Minimum', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Status', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $data->fetch_assoc()) {
    $sistem = $row['stok_akhir'];
    $fisik = $row['jumlah_fisik'];
    $status = 'Aman';
    if ($fisik <= 0) {
        $status = 'Habis';
    } elseif ($fisik <= $row['stok_minimum']) {
        $status = 'Menipis';
    }

    $pdf->Cell(10, 7, $no++, 1);
    $pdf->Cell(50, 7, substr($row['nama_barang'], 0, 30), 1);
    $pdf->Cell(20, 7, $row['satuan'], 1);
    $pdf->Cell(25, 7, $sistem, 1, 0, 'C');
    $pdf->Cell(25, 7, $fisik, 1, 0, 'C');
    $pdf->Cell(25, 7, $row['stok_minimum'], 1, 0, 'C');
    $pdf->Cell(35, 7, $status, 1, 1, 'C');
}

$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(130, 6, '', 0);
$pdf->Cell(60, 6, 'Banjarmasin, ' . date('j') . ' ' .
    ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n') - 1] .
    ' ' . date('Y'), 0, 1);
$pdf->Cell(130, 6, '', 0);
$pdf->Cell(60, 6, 'Mengetahui,', 0, 1);
$pdf->Ln(16);
$pdf->Cell(130, 6, '', 0);
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(60, 6, 'Administrator', 0, 1);

$pdf->Output("I", "laporan_stok_barang_koreksi.pdf");
exit;
