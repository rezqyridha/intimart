<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// Validasi role
if ($_SESSION['role'] !== 'sales') {
    header("Location: " . BASE_URL . "/forbidden.php");
    exit;
}

$today = date('Y-m-d');
$todayDisplay = date('d-m-Y');
$nama = $_SESSION['nama_lengkap'] ?? 'User';

// Penjualan hari ini
$qJual = $koneksi->query("SELECT COUNT(*) AS total_jual FROM penjualan WHERE DATE(tanggal) = '$today'");
$totalJual = $qJual->fetch_assoc()['total_jual'] ?? 0;

// Produk tidak laku
$qTidakLaku = $koneksi->query("SELECT COUNT(*) AS total_tidak_laku FROM produk_tidak_laku WHERE status IN ('diperiksa', 'tindaklanjut')");
$totalTidakLaku = $qTidakLaku->fetch_assoc()['total_tidak_laku'] ?? 0;

// Target sales (misal: target bulanan)
$qTarget = $koneksi->query("SELECT COUNT(*) AS target_sales FROM target_sales");
$totalTarget = $qTarget->fetch_assoc()['target_sales'] ?? 0;

// Pengiriman aktif
$qKirim = $koneksi->query("SELECT COUNT(*) AS total_kirim FROM pengiriman WHERE status_pengiriman = 'dikirim'");
$totalKirim = $qKirim->fetch_assoc()['total_kirim'] ?? 0;

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mt-4 mb-1">Selamat Datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></h4>
                <p class="text-muted mb-0">PT. INTIBOGA MANDIRI – Dashboard <?= ucfirst($_SESSION['role']) ?> – <?= $todayDisplay ?></p>
            </div>
        </div>


        <!-- Statistik Kinerja -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mt-2">

            <!-- Penjualan Hari Ini -->
            <div class="col">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart-check fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Penjualan Hari Ini</h6>
                            <h4 class="fw-bold mb-0"><?= $totalJual ?></h4>
                            <small class="text-muted">Transaksi per <?= $todayDisplay ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produk Tidak Laku -->
            <div class="col">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-danger text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-archive fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Produk Tidak Laku</h6>
                            <h4 class="fw-bold mb-0"><?= $totalTidakLaku ?></h4>
                            <small class="text-muted">Butuh evaluasi atau promo</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Sales -->
            <div class="col">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-dark rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-graph-up fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Target Aktif</h6>
                            <h4 class="fw-bold mb-0"><?= $totalTarget ?></h4>
                            <small class="text-muted">Target performa penjualan</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengiriman Aktif -->
            <div class="col">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-truck fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Pengiriman Berjalan</h6>
                            <h4 class="fw-bold mb-0"><?= $totalKirim ?></h4>
                            <small class="text-muted">Dalam proses pengantaran</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- End::statistik -->

    </div>
</div>
<!-- End::app-content -->

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>