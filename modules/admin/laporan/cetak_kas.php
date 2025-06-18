<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once FPDF_PATH . '/fpdf.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$jenis = $_GET['jenis'] ?? '';

$where = "WHERE tanggal BETWEEN '$dari' AND '$sampai'";
if ($jenis !== '') {
    $where .= " AND jenis = '$jenis'";
}

$query = "SELECT * FROM kas $where ORDER BY tanggal ASC";
$data = $koneksi->query($query);

$total_masuk = $koneksi->query("SELECT SUM(jumlah) FROM kas WHERE jenis='masuk' AND tanggal BETWEEN '$dari' AND '$sampai'")->fetch_row()[0] ?? 0;
$total_keluar = $koneksi->query("SELECT SUM(jumlah) FROM kas WHERE jenis='keluar' AND tanggal BETWEEN '$dari' AND '$sampai'")->fetch_row()[0] ?? 0;
$saldo = $total_masuk - $total_keluar;

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
$pdf->Cell(0, 7, 'LAPORAN KEUANGAN (KAS)', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Jenis', 1, 0, 'C', true);
$pdf->Cell(90, 8, 'Keterangan', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Jumlah', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $data->fetch_assoc()) {
    $pdf->Cell(10, 7, $no++, 1);
    $pdf->Cell(25, 7, date('d-m-Y', strtotime($row['tanggal'])), 1);
    $pdf->Cell(25, 7, ucfirst($row['jenis']), 1);
    $pdf->Cell(90, 7, substr($row['keterangan'], 0, 60), 1);
    $pdf->Cell(40, 7, 'Rp ' . number_format($row['jumlah'], 0, ',', '.'), 1, 1, 'R');
}

$pdf->SetFont('Arial', 'B', 10);
$pdf->Ln(2);
$pdf->Cell(0, 6, "Total Masuk  : Rp " . number_format($total_masuk, 0, ',', '.'), 0, 1);
$pdf->Cell(0, 6, "Total Keluar : Rp " . number_format($total_keluar, 0, ',', '.'), 0, 1);
$pdf->Cell(0, 6, "Saldo Akhir  : Rp " . number_format($saldo, 0, ',', '.'), 0, 1);

// Footer
$pdf->Ln(10);
$pdf->Cell(130, 6, '', 0);
$pdf->Cell(60, 6, 'Banjarmasin, ' . date('j') . ' ' .
    ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n') - 1] .
    ' ' . date('Y'), 0, 1);
$pdf->Cell(130, 6, '', 0);
$pdf->Cell(60, 6, 'Mengetahui,', 0, 1);
$pdf->Ln(14);
$pdf->Cell(130, 6, '', 0);
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(60, 6, 'Administrator', 0, 1);

$pdf->Output("I", "laporan_kas.pdf");
exit;
