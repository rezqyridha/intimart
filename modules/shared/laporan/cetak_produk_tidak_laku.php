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

$query = "
    SELECT 
        b.nama_barang, b.satuan,
        COALESCE(SUM(p.jumlah), 0) AS total_terjual,
        MAX(p.tanggal) AS terakhir_terjual
    FROM barang b
    LEFT JOIN penjualan p 
        ON b.id = p.id_barang AND p.tanggal BETWEEN '$dari' AND '$sampai'
    GROUP BY b.id
    ORDER BY b.nama_barang ASC
";
$result = $koneksi->query($query);

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, 'PT. INTI BOGA MANDIRI', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Distributor Resmi Indomie & PopMie Kalimantan Selatan - Tengah', 0, 1, 'C');
$pdf->Cell(0, 5, 'Jl. Pasar Baru 87 - 89 Kertak Baru Ilir Banjar Barat, Banjarmasin', 0, 1, 'C');
$pdf->Cell(0, 5, 'Telp: 0511-3360373, 0511-4366629, 0511-4369746', 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'LAPORAN ANALITIK PRODUK TIDAK LAKU', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(65, 8, 'Nama Barang', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Satuan', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Terjual', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Terakhir', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Status', 1, 1, 'C', true);

// Table Body
$pdf->SetFont('Arial', '', 10);
$no = 1;
$cellHeight = 6;

while ($row = $result->fetch_assoc()) {
    $isLaku = $row['total_terjual'] > 0;
    $status = $isLaku ? '[v] Laku' : '[x] Tidak Laku';

    if ($statusFilter === 'laku' && !$isLaku) continue;
    if ($statusFilter === 'tidak' && $isLaku) continue;

    $terakhir = $row['terakhir_terjual'] ? date('d-m-Y', strtotime($row['terakhir_terjual'])) : '-';
    $barang = $row['nama_barang'];

    // Hitung tinggi berdasarkan panjang nama barang
    $barangLines = ceil($pdf->GetStringWidth($barang) / 63);
    $rowHeight = max($cellHeight * $barangLines, $cellHeight);

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->MultiCell(10, $rowHeight, $no++, 1, 'C');
    $pdf->SetXY($x + 10, $y);
    $pdf->MultiCell(65, $cellHeight, $barang, 1);
    $pdf->SetXY($x + 10 + 65, $y);
    $pdf->Cell(20, $rowHeight, $row['satuan'], 1, 0, 'C');
    $pdf->Cell(25, $rowHeight, $row['total_terjual'], 1, 0, 'C');
    $pdf->Cell(30, $rowHeight, $terakhir, 1, 0, 'C');
    $pdf->Cell(40, $rowHeight, $status, 1, 1, 'C');
}

// Keterangan status
$pdf->Ln(5);
$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(0, 5, 'Keterangan:', 0, 1);
$pdf->Cell(0, 5, '[v] Laku        : Produk memiliki penjualan pada periode yang dipilih', 0, 1);
$pdf->Cell(0, 5, '[x] Tidak Laku : Produk tidak memiliki penjualan sama sekali pada periode', 0, 1);

// Footer
$pdf->Ln(8);
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
