
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once FPDF_PATH . '/fpdf.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer', 'sales'])) {
    die("Akses ditolak.");
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$id_sales = $_GET['sales'] ?? '';

$query = "
    SELECT p.*, b.nama_barang, b.satuan, u.nama_lengkap AS nama_sales
    FROM penjualan p
    JOIN barang b ON p.id_barang = b.id
    JOIN user u ON u.id = p.id_sales
    WHERE p.tanggal BETWEEN '$dari' AND '$sampai'
";

if ($_SESSION['role'] === 'sales') {
    $query .= " AND p.id_sales = " . $_SESSION['id_user'];
} elseif ($id_sales !== '') {
    $query .= " AND p.id_sales = " . intval($id_sales);
}

$query .= " ORDER BY p.tanggal ASC";
$data = $koneksi->query($query);

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
$pdf->Cell(0, 7, 'LAPORAN PENJUALAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

// TABLE HEADER
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Barang', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Sales', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Jumlah', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Total', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Status', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$no = 1;
$total_all = 0;
while ($row = $data->fetch_assoc()) {
    $tanggal = date('d-m-Y', strtotime($row['tanggal']));
    $barang = $row['nama_barang'] . ' (' . $row['satuan'] . ')';
    $sales = $row['nama_sales'];
    $jumlah = $row['jumlah'];
    $total = $row['harga_total'];
    $status = $row['status_pelunasan'];

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // MultiCell Barang
    $pdf->SetXY($x + 10 + 25, $y); // Skip No + Tanggal
    $pdf->MultiCell(45, 6, $barang, 1);
    $cellHeight = $pdf->GetY() - $y;

    // Kolom No
    $pdf->SetXY($x, $y);
    $pdf->Cell(10, $cellHeight, $no++, 1, 0, 'C');

    // Kolom Tanggal
    $pdf->SetXY($x + 10, $y);
    $pdf->Cell(25, $cellHeight, $tanggal, 1);

    // Kolom Sales
    $pdf->SetXY($x + 10 + 25 + 45, $y);
    $pdf->Cell(40, $cellHeight, $sales, 1);

    // Jumlah
    $pdf->Cell(20, $cellHeight, $jumlah, 1, 0, 'C');

    // Total
    $pdf->Cell(30, $cellHeight, 'Rp ' . number_format($total, 0, ',', '.'), 1, 0, 'R');

    // Status
    $pdf->Cell(20, $cellHeight, $status, 1, 1, 'C');

    $total_all += $total;
}


// TOTAL
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 8, 'Total Keseluruhan', 1);
$pdf->Cell(30, 8, 'Rp ' . number_format($total_all, 0, ',', '.'), 1, 0, 'R');
$pdf->Cell(20, 8, '', 1, 1);

// FOOTER TTD
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

$pdf->Output("I", "laporan_penjualan.pdf");
exit;
