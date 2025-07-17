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
    SELECT p.tanggal, u.nama_lengkap AS nama_sales, b.nama_barang, b.satuan,
           p.jumlah AS jumlah_piutang, p.status
    FROM piutang p
    JOIN user u ON p.id_sales = u.id
    LEFT JOIN penjualan pj ON p.id_penjualan = pj.id
    LEFT JOIN barang b ON pj.id_barang = b.id
    WHERE p.tanggal BETWEEN ? AND ?
";

$params = [$dari, $sampai];
$types = 'ss';

if (!empty($id_sales)) {
    $query .= " AND p.id_sales = ?";
    $types .= 'ssi';
    $params[] = $id_sales;
}

$query .= " ORDER BY p.tanggal ASC";
$stmt = $koneksi->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$data = $stmt->get_result();

// PDF
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
$pdf->Cell(0, 7, 'LAPORAN PIUTANG ', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

// Header tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(22, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(43, 8, 'Barang', 1, 0, 'C', true);
$pdf->Cell(38, 8, 'Sales', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Jumlah Piutang', 1, 0, 'C', true);
$pdf->Cell(37, 8, 'Status', 1, 1, 'C', true);

// Data
$pdf->SetFont('Arial', '', 10);
$no = 1;
$total = 0;
while ($row = $data->fetch_assoc()) {
    $tanggal = date('d-m-Y', strtotime($row['tanggal']));
    $barang = ($row['nama_barang'] ?? '-') . ' (' . ($row['satuan'] ?? '-') . ')';
    $sales = $row['nama_sales'];
    $jumlah = $row['jumlah_piutang'];
    $status = ucfirst($row['status']);

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->SetXY($x + 10 + 22, $y);
    $pdf->MultiCell(43, 6, $barang, 1);
    $cellHeight = $pdf->GetY() - $y;

    $pdf->SetXY($x, $y);
    $pdf->Cell(10, $cellHeight, $no++, 1, 0, 'C');
    $pdf->SetXY($x + 10, $y);
    $pdf->Cell(22, $cellHeight, $tanggal, 1);
    $pdf->SetXY($x + 10 + 22 + 43, $y);
    $pdf->Cell(38, $cellHeight, $sales, 1);
    $pdf->Cell(40, $cellHeight, 'Rp ' . number_format($jumlah, 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(37, $cellHeight, $status, 1, 1, 'C');

    $total += $jumlah;
}

// Footer Total
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(113, 8, 'Total Seluruh', 1, 0, 'L', true);
$pdf->Cell(40, 8, 'Rp ' . number_format($total, 0, ',', '.'), 1, 0, 'R', true);
$pdf->Cell(37, 8, '', 1, 1);

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

$pdf->Output("I", "laporan_piutang_manual.pdf");
exit;
