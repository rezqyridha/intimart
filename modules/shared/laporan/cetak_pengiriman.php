<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';
require_once FPDF_PATH . '/fpdf.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer', 'karyawan'])) {
    die("Akses ditolak.");
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? '';

// Ambil data pengiriman
$query = "
    SELECT p.*, 
        GROUP_CONCAT(CONCAT(b.nama_barang, ' (', pd.jumlah, ' ', b.satuan, ')') SEPARATOR ', ') AS detail_barang
    FROM pengiriman p
    JOIN pengiriman_detail pd ON p.id = pd.id_pengiriman
    JOIN barang b ON pd.id_barang = b.id
    WHERE p.tanggal_kirim BETWEEN '$dari' AND '$sampai'
";
if ($statusFilter !== '') {
    $query .= " AND p.status_pengiriman = '$statusFilter'";
}
$query .= " GROUP BY p.id ORDER BY p.tanggal_kirim ASC";
$data = $koneksi->query($query);

// Mulai PDF
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
$pdf->Cell(0, 7, 'LAPORAN PENGIRIMAN BARANG', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, "Periode: " . date('d/m/Y', strtotime($dari)) . " s.d " . date('d/m/Y', strtotime($sampai)), 0, 1, 'C');
$pdf->Ln(4);

// Header tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Tgl Kirim', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Barang (Jumlah Satuan)', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Tujuan', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Estimasi', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Status', 1, 1, 'C', true);

// Body tabel
$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $data->fetch_assoc()) {
    $tglKirim = date('d-m-Y', strtotime($row['tanggal_kirim']));
    $estimasi = $row['estimasi_tiba'] ? date('d-m-Y', strtotime($row['estimasi_tiba'])) : '-';
    $status   = ucfirst($row['status_pengiriman']);

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Simpan posisi sebelum MultiCell Barang
    $pdf->SetXY($x + 10 + 30, $y);
    $pdf->MultiCell(60, 7, $row['detail_barang'], 1);
    $barisTinggi = $pdf->GetY() - $y;

    // Kembali ke posisi kiri baris
    $pdf->SetXY($x, $y);
    $pdf->Cell(10, $barisTinggi, $no++, 1, 0, 'C');
    $pdf->Cell(30, $barisTinggi, $tglKirim, 1, 0, 'C');

    // Geser ke kanan setelah kolom barang (x + 10 + 30 + 60 = 100)
    $pdf->SetXY($x + 100, $y);
    $pdf->Cell(40, $barisTinggi, substr($row['tujuan'], 0, 35), 1);
    $pdf->Cell(25, $barisTinggi, $estimasi, 1, 0, 'C');
    $pdf->Cell(25, $barisTinggi, $status, 1, 1, 'C');
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

$pdf->Output("I", "laporan_pengiriman.pdf");
exit;
