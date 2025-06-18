<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';
require_once FPDF_PATH . '/fpdf.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer', 'sales'])) {
    die("Akses ditolak.");
}

$role = $_SESSION['role'];
$idUser = $_SESSION['id_user'] ?? 0;

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? '';
$salesFilter = $_GET['sales'] ?? '';

// Query data
$query = "
    SELECT p.*, b.nama_barang, b.satuan, u.nama_lengkap AS nama_sales
    FROM pemesanan p
    JOIN barang b ON p.id_barang = b.id
    JOIN user u ON p.id_sales = u.id
    WHERE DATE(p.tanggal_pemesanan) BETWEEN '$dari' AND '$sampai'
";

if ($role === 'sales') {
    $query .= " AND p.id_sales = $idUser";
} elseif ($salesFilter !== '') {
    $query .= " AND p.id_sales = " . intval($salesFilter);
}
if ($statusFilter !== '') {
    $query .= " AND p.status = '$statusFilter'";
}
$query .= " ORDER BY p.tanggal_pemesanan DESC";
$data = $koneksi->query($query);

// PDF Header
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
$pdf->Cell(0, 7, 'LAPORAN PEMESANAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

// HEADER TABEL
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Barang', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Sales', 1, 0, 'C', true);
$pdf->Cell(10, 8, 'Jml', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Status', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Respon', 1, 1, 'C', true);

// BODY TABEL
$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $data->fetch_assoc()) {
    $tanggal = date('d-m-Y H:i', strtotime($row['tanggal_pemesanan']));
    $barang = $row['nama_barang'] . ' (' . $row['satuan'] . ')';
    $sales = $row['nama_sales'];
    $jumlah = $row['jumlah'];
    $respon = $row['tanggal_direspon'] ? date('d-m-Y H:i', strtotime($row['tanggal_direspon'])) : '-';
    $status = ucfirst($row['status']);

    $pdf->Cell(10, 7, $no++, 1);
    $pdf->Cell(30, 7, $tanggal, 1);
    $pdf->Cell(50, 7, substr($barang, 0, 50), 1);
    $pdf->Cell(35, 7, substr($sales, 0, 30), 1);
    $pdf->Cell(10, 7, $jumlah, 1, 0, 'C');
    $pdf->Cell(25, 7, $status, 1, 0, 'C');
    $pdf->Cell(30, 7, $respon, 1, 1, 'C');
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

$pdf->Output("I", "laporan_pemesanan.pdf");
exit;
