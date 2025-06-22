<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';
require_once FPDF_PATH . '/fpdf.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$status = $_GET['status'] ?? '';
$metode = $_GET['metode'] ?? '';

$query = "
    SELECT rp.*, b.nama_barang, b.satuan, p.tanggal, p.metode, p.jumlah_bayar
    FROM rekonsiliasi_pembayaran rp
    JOIN pembayaran p ON rp.id_pembayaran = p.id
    JOIN penjualan j ON p.id_penjualan = j.id
    JOIN barang b ON j.id_barang = b.id
    WHERE rp.tanggal_rekonsiliasi BETWEEN '$dari' AND '$sampai'
";

if ($status !== '') {
    $query .= " AND rp.status = '$status'";
}
if ($metode !== '') {
    $query .= " AND p.metode = '$metode'";
}

$query .= " ORDER BY rp.tanggal_rekonsiliasi DESC";
$data = $koneksi->query($query);

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
$pdf->Cell(0, 7, 'LAPORAN REKONSILIASI PEMBAYARAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(5);

// Table header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Barang', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Metode', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Nominal', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Tgl Bayar', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Tgl Rekon', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Status', 1, 1, 'C', true);

// Table body
$pdf->SetFont('Arial', '', 10);
$no = 1;
$no = 1;
while ($row = $data->fetch_assoc()) {
    $barangText = $row['nama_barang'] . ' (' . $row['satuan'] . ')';

    // Simpan posisi awal
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Cetak MultiCell Barang (diukur dulu)
    $x1 = $x + 10; // setelah kolom no
    $pdf->SetXY($x1, $y);
    $pdf->MultiCell(35, 6, $barangText, 1);
    $cellHeight = $pdf->GetY() - $y;

    // Cetak kolom No dengan tinggi menyesuaikan
    $pdf->SetXY($x, $y);
    $pdf->Cell(10, $cellHeight, $no++, 1, 0, 'C');

    // Lanjut kolom lain
    $pdf->SetXY($x1 + 35, $y);
    $pdf->Cell(25, $cellHeight, ucfirst($row['metode']), 1);
    $pdf->Cell(30, $cellHeight, 'Rp ' . number_format($row['jumlah_bayar'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(30, $cellHeight, date('d-m-Y', strtotime($row['tanggal'])), 1);
    $pdf->Cell(30, $cellHeight, date('d-m-Y', strtotime($row['tanggal_rekonsiliasi'])), 1);
    $pdf->Cell(30, $cellHeight, ucwords(str_replace('_', ' ', $row['status'])), 1);
    $pdf->Ln();
}


// Footer tanda tangan
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

$pdf->Output("I", "rekonsiliasi_pembayaran.pdf");
exit;
