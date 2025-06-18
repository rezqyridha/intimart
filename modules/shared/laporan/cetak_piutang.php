<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';
require_once FPDF_PATH . '/fpdf.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    die("Akses ditolak.");
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$id_sales = $_GET['sales'] ?? '';

$query = "
    SELECT p.tanggal, u.nama_lengkap AS nama_sales, b.nama_barang, 
           p.harga_total, 
           COALESCE((SELECT SUM(jumlah_bayar) FROM pembayaran WHERE id_penjualan = p.id), 0) AS total_bayar,
           (p.harga_total - COALESCE((SELECT SUM(jumlah_bayar) FROM pembayaran WHERE id_penjualan = p.id), 0)) AS sisa
    FROM penjualan p
    JOIN barang b ON p.id_barang = b.id
    JOIN user u ON p.id_sales = u.id
    WHERE p.status_pelunasan != 'lunas'
    AND p.tanggal BETWEEN '$dari' AND '$sampai'
";

if ($id_sales !== '') {
    $query .= " AND p.id_sales = " . intval($id_sales);
}

$query .= " ORDER BY p.tanggal ASC";
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
$pdf->Cell(0, 7, 'LAPORAN PIUTANG', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(22, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(43, 8, 'Barang', 1, 0, 'C', true);
$pdf->Cell(38, 8, 'Sales', 1, 0, 'C', true);
$pdf->Cell(27, 8, 'Total', 1, 0, 'C', true);
$pdf->Cell(27, 8, 'Terbayar', 1, 0, 'C', true);
$pdf->Cell(23, 8, 'Sisa', 1, 1, 'C', true);


$pdf->SetFont('Arial', '', 10);
$no = 1;
$total_all = 0;
while ($row = $data->fetch_assoc()) {
    $pdf->Cell(10, 7, $no++, 1);
    $pdf->Cell(22, 7, date('d-m-Y', strtotime($row['tanggal'])), 1);
    $pdf->Cell(43, 7, substr($row['nama_barang'], 0, 35), 1);
    $pdf->Cell(38, 7, substr($row['nama_sales'], 0, 30), 1);
    $pdf->Cell(27, 7, 'Rp ' . number_format($row['harga_total'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(27, 7, 'Rp ' . number_format($row['total_bayar'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(23, 7, 'Rp ' . number_format($row['sisa'], 0, ',', '.'), 1, 1, 'R');
    $total_all += $row['sisa'];
}

// Footer total, merge kolom dan "Total Seluruh" di kiri
$pdf->SetFont('Arial', 'B', 10);
// Merge 6 kolom pertama (10+22+43+38+27+27 = 167)
$pdf->Cell(167, 8, 'Total Seluruh', 1, 0, 'L', true);
$pdf->Cell(23, 8, 'Rp ' . number_format($total_all, 0, ',', '.'), 1, 1, 'R', true); // Sisa


$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(130, 6, '', 0);
$pdf->Cell(60, 6, 'Banjarmasin, ' . date('j') . ' ' .
    ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n') - 1] . ' ' . date('Y'), 0, 1);
$pdf->Cell(130, 6, '', 0);
$pdf->Cell(60, 6, 'Mengetahui,', 0, 1);
$pdf->Ln(16);
$pdf->Cell(130, 6, '', 0);
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(60, 6, 'Administrator', 0, 1);

$pdf->Output("I", "laporan_piutang.pdf");
exit;
