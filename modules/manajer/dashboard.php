<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// Validasi hanya role 'manajer' yang bisa akses
if ($_SESSION['role'] !== 'manajer') {
    header("Location: " . AUTH_PATH . "/login.php");
    exit;
}

// Ambil data statistik untuk manajer
$today = date('Y-m-d');
$todayDisplay = date('d-m-Y');

$totalBarang = $koneksi->query("SELECT COUNT(*) AS total FROM barang")->fetch_assoc()['total'] ?? 0;
$totalPenjualan = $koneksi->query("SELECT COUNT(*) AS total FROM penjualan WHERE DATE(tanggal) = '$today'")->fetch_assoc()['total'] ?? 0;
$totalRetur = $koneksi->query("SELECT COUNT(*) AS total FROM retur")->fetch_assoc()['total'] ?? 0;
$totalPembayaran = $koneksi->query("SELECT COUNT(*) AS total FROM pembayaran")->fetch_assoc()['total'] ?? 0;

// Layout
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


        <!-- Statistik Row -->
        <div class="row mt-4">

            <!-- Box: Total Barang -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Barang</h6>
                            <h4 class="fw-bold mb-0"><?= $totalBarang ?></h4>
                            <small class="text-muted">Data produk tersedia</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box: Penjualan Hari Ini -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart-check-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Penjualan Hari Ini</h6>
                            <h4 class="fw-bold mb-0"><?= $totalPenjualan ?></h4>
                            <small class="text-muted">Tanggal <?= $todayDisplay ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box: Retur Penjualan -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-arrow-return-left fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Retur Penjualan</h6>
                            <h4 class="fw-bold mb-0"><?= $totalRetur ?></h4>
                            <small class="text-muted">Total retur seluruh waktu</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box: Pembayaran -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Pembayaran</h6>
                            <h4 class="fw-bold mb-0"><?= $totalPembayaran ?></h4>
                            <small class="text-muted">Riwayat transaksi</small>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- End row -->

    </div>
</div>
<!-- End::app-content -->
<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>