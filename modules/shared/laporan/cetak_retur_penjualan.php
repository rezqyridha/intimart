<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';
require_once FPDF_PATH . '/fpdf.php';

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];

if (!in_array($role, ['admin', 'manajer', 'sales'])) {
    die("Akses ditolak.");
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$id_sales = $_GET['sales'] ?? '';

$query = "
    SELECT r.*, p.id_sales, p.id_barang, u.nama_lengkap AS nama_sales,
           b.nama_barang, b.satuan
    FROM retur_penjualan r
    JOIN penjualan p ON r.id_penjualan = p.id
    JOIN user u ON p.id_sales = u.id
    JOIN barang b ON p.id_barang = b.id
    WHERE r.tanggal BETWEEN '$dari' AND '$sampai'
";

if ($role === 'sales') {
    $query .= " AND p.id_sales = $id_user";
} elseif ($id_sales !== '') {
    $query .= " AND p.id_sales = " . intval($id_sales);
}
$query .= " ORDER BY r.tanggal ASC";
$data = $koneksi->query($query);

// PDF Setup
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
$pdf->Cell(0, 7, 'LAPORAN RETUR PENJUALAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Periode: ' . date('d/m/Y', strtotime($dari)) . ' s.d ' . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

// Header Tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Barang', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Sales', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Jumlah', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Alasan', 1, 1, 'C', true);

// Body Tabel
$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $data->fetch_assoc()) {
    $tanggal = date('d-m-Y', strtotime($row['tanggal']));
    $barang = $row['nama_barang'] . ' (' . $row['satuan'] . ')';
    $sales = $row['nama_sales'];
    $jumlah = $row['jumlah'];
    $alasan = $row['alasan'];

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // MultiCell Barang
    $pdf->SetXY($x + 10 + 25, $y); // skip No dan Tanggal
    $pdf->MultiCell(45, 6, $barang, 1);
    $cellHeight = $pdf->GetY() - $y;

    // No
    $pdf->SetXY($x, $y);
    $pdf->Cell(10, $cellHeight, $no++, 1, 0, 'C');

    // Tanggal
    $pdf->SetXY($x + 10, $y);
    $pdf->Cell(25, $cellHeight, $tanggal, 1);

    // Sales & Lainnya
    $pdf->SetXY($x + 10 + 25 + 45, $y);
    $pdf->Cell(40, $cellHeight, $sales, 1);
    $pdf->Cell(20, $cellHeight, $jumlah, 1, 0, 'C');
    $pdf->Cell(50, $cellHeight, $alasan, 1);
    $pdf->Ln();
}

// Footer
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

$pdf->Output("I", "laporan_retur_penjualan.pdf");
exit;
