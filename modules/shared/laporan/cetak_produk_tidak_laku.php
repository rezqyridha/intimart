<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';
require_once FPDF_PATH . '/fpdf.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer', 'sales', 'karyawan'])) {
    die("Akses ditolak.");
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? '';

// Query total & tanggal terakhir penjualan
$query = "
    SELECT 
        b.nama_barang, b.satuan,
        COALESCE(SUM(p.jumlah), 0) AS total_terjual,
        MAX(p.tanggal) AS terakhir_terjual
    FROM barang b
    LEFT JOIN penjualan p ON b.id = p.id_barang 
        AND p.tanggal BETWEEN '$dari' AND '$sampai'
    GROUP BY b.id
    ORDER BY b.nama_barang
";
$result = $koneksi->query($query);

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// HEADER
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, 'PT. INTI BOGA MANDIRI', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Distributor Resmi Indomie & PopMie Kalimantan Selatan - Tengah', 0, 1, 'C');
$pdf->Cell(0, 5, 'Jl. Pasar Baru 87 - 89 Kertak Baru Ilir Banjar Barat, Banjarmasin', 0, 1, 'C');
$pdf->Cell(0, 5, 'Telp: 0511-3360373, 0511-4366629, 0511-4369746', 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'LAPORAN PRODUK TIDAK LAKU', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

// TABLE HEADER
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Nama Barang', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Satuan', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Total Terjual', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Tgl Terakhir', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Status', 1, 1, 'C', true);

// TABLE BODY
$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $result->fetch_assoc()) {
    $isLaku = $row['total_terjual'] > 0;
    $status = $isLaku ? '[v] Laku' : '[x] Tidak Laku';

    if ($statusFilter === 'laku' && !$isLaku) continue;
    if ($statusFilter === 'tidak' && $isLaku) continue;

    $terakhir = $row['terakhir_terjual'] ? date('d-m-Y', strtotime($row['terakhir_terjual'])) : '-';

    $pdf->Cell(10, 7, $no++, 1);
    $pdf->Cell(60, 7, substr($row['nama_barang'], 0, 40), 1);
    $pdf->Cell(20, 7, $row['satuan'], 1, 0, 'C');
    $pdf->Cell(25, 7, $row['total_terjual'], 1, 0, 'C');
    $pdf->Cell(35, 7, $terakhir, 1, 0, 'C');
    $pdf->Cell(40, 7, $status, 1, 1, 'C');
}

// FOOTER
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

$pdf->Output("I", "laporan_produk_tidak_laku.pdf");
exit;
