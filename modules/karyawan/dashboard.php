<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// Cek role
if ($_SESSION['role'] !== 'karyawan') {
    header("Location: " . BASE_URL . "/forbidden.php");
    exit;
}

$today = date('Y-m-d');
$todayDisplay = date('d-m-Y');

// Statistik
$barangMasukHariIni = $koneksi->query("SELECT COUNT(*) as total FROM barang_masuk WHERE DATE(tanggal) = '$today'")->fetch_assoc()['total'] ?? 0;

$resultStok = $koneksi->query("SELECT SUM(jumlah) AS total_stok FROM stok");
$totalStok = $resultStok->fetch_assoc()['total_stok'] ?? 0;

$produkTidakLaku = $koneksi->query("SELECT COUNT(*) as jumlahd FROM produk_tidak_laku")->fetch_assoc()['total'] ?? 0;

$retur = $koneksi->query("SELECT COUNT(*) as jumlah FROM retur")->fetch_assoc()['total'] ?? 0;

$pageTitle = 'Dashboard Karyawan';
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

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-archive-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Stok</h6>
                            <h4 class="fw-bold mb-0"><?= $totalStok ?></h4>
                            <small class="text-muted">Total item di semua gudang</small>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-emoji-frown fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Produk Tidak Laku</h6>
                            <h4 class="fw-bold mb-0"><?= $produkTidakLaku ?></h4>
                            <small class="text-muted">Total item</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-danger text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-arrow-counterclockwise fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Retur Penjualan</h6>
                            <h4 class="fw-bold mb-0"><?= $retur ?></h4>
                            <small class="text-muted">Total retur</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<!-- End::app-content -->

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>