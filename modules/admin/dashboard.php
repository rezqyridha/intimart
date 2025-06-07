<?php
require_once __DIR__ . '/../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// Cek role admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: " . AUTH_PATH . "/login.php");
    exit;
}

// Data Statistik
$today = date('Y-m-d');
$todayDisplay = date('d-m-Y');

$totalUser   = $koneksi->query("SELECT COUNT(*) AS total FROM user")->fetch_assoc()['total'] ?? 0;
$totalBarang = $koneksi->query("SELECT COUNT(*) AS total FROM barang")->fetch_assoc()['total'] ?? 0;
$totalJual   = $koneksi->query("SELECT COUNT(*) AS total FROM penjualan WHERE DATE(tanggal) = '$today'")->fetch_assoc()['total'] ?? 0;
$totalMasuk  = $koneksi->query("SELECT COUNT(*) AS total FROM barang_masuk WHERE DATE(tanggal) = '$today'")->fetch_assoc()['total'] ?? 0;
?>

<?php
require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<!-- Start::Content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Page Header -->
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mt-4 mb-1">Selamat Datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></h4>
                <p class="text-muted mb-0">PT. INTIBOGA MANDIRI – Dashboard <?= ucfirst($_SESSION['role']) ?> – <?= $todayDisplay ?></p>
            </div>
        </div>



        <!-- Statistik Row -->
        <div class="row g-3">

            <!-- Box Jumlah Pengguna -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Jumlah Pengguna</h6>
                            <h4 class="fw-bold mb-0"><?= $totalUser ?></h4>
                            <small class="text-muted">User terdaftar</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box Total Barang -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Barang</h6>
                            <h4 class="fw-bold mb-0"><?= $totalBarang ?></h4>
                            <small class="text-muted">Data barang masuk</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box Transaksi Hari ini -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Transaksi Hari Ini</h6>
                            <h4 class="fw-bold mb-0"><?= $totalJual ?></h4>
                            <small class="text-muted">Tanggal <?= $todayDisplay ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box Barang Masuk Hari ini -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-arrow-in-down fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Barang Masuk Hari Ini</h6>
                            <h4 class="fw-bold mb-0"><?= $totalMasuk ?></h4>
                            <small class="text-muted">Tanggal <?= $todayDisplay ?></small>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- End row -->

    </div>

</div>
<!-- End::app-content -->
<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>
</div>
<!-- End::Content -->