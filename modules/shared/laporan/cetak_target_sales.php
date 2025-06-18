<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';
require_once FPDF_PATH . '/fpdf.php';

$role = $_SESSION['role'] ?? '';
$id_user = $_SESSION['id_user'] ?? 0;

if (!in_array($role, ['admin', 'manajer', 'sales'])) {
    die("Akses ditolak.");
}

$bulan = $_GET['bulan'] ?? '';
$salesFilter = $_GET['sales'] ?? '';

// Query data
$query = "
    SELECT ts.*, u.nama_lengkap 
    FROM target_sales ts
    JOIN user u ON ts.id_sales = u.id
    WHERE 1=1
";

if ($bulan !== '') {
    $query .= " AND ts.bulan = '$bulan'";
}

if ($role === 'sales') {
    $query .= " AND ts.id_sales = $id_user";
} elseif ($salesFilter !== '') {
    $query .= " AND ts.id_sales = " . intval($salesFilter);
}

$query .= " ORDER BY ts.bulan DESC, u.nama_lengkap";
$data = $koneksi->query($query);

// PDF setup
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
$pdf->Cell(0, 7, 'LAPORAN TARGET vs REALISASI SALES', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, ($bulan !== '' ? 'Bulan: ' . ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n', strtotime($bulan . '-01')) - 1] . ' ' . date('Y', strtotime($bulan . '-01')) : 'Semua Bulan'), 0, 1, 'C');
$pdf->Ln(4);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(55, 8, 'Nama Sales', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Bulan', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Target', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Realisasi', 1, 0, 'C', true);
$pdf->Cell(25, 8, '% Capaian', 1, 1, 'C', true);

// Table Body
$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $data->fetch_assoc()) {
    $persen = $row['target'] > 0 ? round($row['realisasi'] / $row['target'] * 100, 2) : 0;
    $pdf->Cell(10, 7, $no++, 1);
    $pdf->Cell(55, 7, substr($row['nama_lengkap'], 0, 35), 1);
    $pdf->Cell(
        30,
        7,
        ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n', strtotime($row['bulan'] . '-01')) - 1] .
            date(' Y', strtotime($row['bulan'] . '-01')),
        1,
        0,
        'C'
    );
    $pdf->Cell(35, 7, 'Rp ' . number_format($row['target'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(35, 7, 'Rp ' . number_format($row['realisasi'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(25, 7, $persen . '%', 1, 1, 'C');
}

// Footer TTD
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

$pdf->Output("I", "laporan_target_sales.pdf");
exit;
