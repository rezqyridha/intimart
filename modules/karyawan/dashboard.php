<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'karyawan') {
    header("Location: " . BASE_URL . "/forbidden.php");
    exit;
}

$today = date('Y-m-d');
$todayDisplay = date('d-m-Y');
$tahunIni = date('Y');

// Barang Masuk Hari Ini
$barangMasukHariIni = $koneksi->query("SELECT COUNT(*) AS total FROM barang_masuk WHERE DATE(tanggal) = '$today'")->fetch_assoc()['total'] ?? 0;

// Total Stok Real (masuk - keluar)
$qStok = "
    SELECT 
        b.nama_barang,
        IFNULL(masuk.total_masuk, 0) AS masuk,
        IFNULL(keluar.total_keluar, 0) AS keluar,
        (IFNULL(masuk.total_masuk, 0) - IFNULL(keluar.total_keluar, 0)) AS total_stok
    FROM barang b
    LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_masuk FROM barang_masuk GROUP BY id_barang) masuk ON b.id = masuk.id_barang
    LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_keluar FROM barang_keluar GROUP BY id_barang) keluar ON b.id = keluar.id_barang
";
$resStok = $koneksi->query($qStok);
$totalStok = 0;
while ($s = $resStok->fetch_assoc()) {
    $totalStok += (int)$s['total_stok'];
}

// Produk Tidak Laku
$produkTidakLaku = $koneksi->query("SELECT COUNT(*) AS total FROM produk_tidak_laku")->fetch_assoc()['total'] ?? 0;

// Retur Penjualan
$retur = $koneksi->query("SELECT COUNT(*) AS total FROM retur_penjualan")->fetch_assoc()['total'] ?? 0;

// Grafik Barang Masuk per Bulan
$barangMasukPerBulan = array_fill(1, 12, 0);
$resBM = $koneksi->query("
    SELECT MONTH(tanggal) AS bulan, COUNT(*) AS total
    FROM barang_masuk
    WHERE YEAR(tanggal) = $tahunIni
    GROUP BY bulan
");
while ($row = $resBM->fetch_assoc()) {
    $barangMasukPerBulan[(int)$row['bulan']] = (int)$row['total'];
}
$labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$dataBarangMasuk = array_values($barangMasukPerBulan);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Greeting -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mt-4 mb-1">Selamat Datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></h4>
                <p class="text-muted mb-0">Dashboard Karyawan - <?= $todayDisplay ?></p>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row">
            <!-- Barang Masuk Hari Ini -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-arrow-in-down fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Barang Masuk</h6>
                            <h4 class="fw-bold mb-0"><?= $barangMasukHariIni ?></h4>
                            <small class="text-muted">Hari ini</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Stok -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-archive-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Stok</h6>
                            <h4 class="fw-bold mb-0"><?= $totalStok ?></h4>
                            <small class="text-muted">Stok real semua barang</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produk Tidak Laku -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-danger text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-emoji-frown fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Produk Tidak Laku</h6>
                            <h4 class="fw-bold mb-0"><?= $produkTidakLaku ?></h4>
                            <small class="text-muted">Perlu strategi promosi</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Retur Penjualan -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-arrow-counterclockwise fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Retur Penjualan</h6>
                            <h4 class="fw-bold mb-0"><?= $retur ?></h4>
                            <small class="text-muted">Total retur pelanggan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">📦 Grafik Barang Masuk per Bulan</h6>
            </div>
            <div class="card-body">
                <canvas id="grafikBarangMasuk"></canvas>
            </div>
        </div>

    </div>
</div>
<!-- End::app-content -->

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('grafikBarangMasuk'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelBulan) ?>,
            datasets: [{
                label: 'Barang Masuk',
                data: <?= json_encode($dataBarangMasuk) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>