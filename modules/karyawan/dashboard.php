<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';
require_once APP_PATH . '/koneksi.php';
require_once APP_PATH . '/views/layout/head.php';
require_once APP_PATH . '/views/layout/header.php';

// Query statistik
$query_barang_masuk = $conn->query("SELECT COUNT(*) as total FROM barang_masuk WHERE DATE(tanggal) = CURDATE()");
$barang_masuk_hari_ini = $query_barang_masuk->fetch_assoc()['total'] ?? 0;

$query_pengiriman = $conn->query("SELECT COUNT(*) as total FROM pengiriman WHERE status_pengiriman = 'dikirim'");
$jumlah_pengiriman = $query_pengiriman->fetch_assoc()['total'] ?? 0;
?>

<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Start::page-header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb">
            <div>
                <h2 class="main-content-title fs-24 mb-1">Dashboard Karyawan</h2>
                <p class="text-muted mb-0">Statistik hari ini</p>
            </div>
        </div>
        <!-- End::page-header -->

        <!-- Start::row-statistik -->
        <div class="row row-sm">
            <!-- Barang Masuk Hari Ini -->
            <div class="col-lg-6 col-xl-6 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Barang Masuk Hari Ini</h5>
                        <h3 class="fw-bold"><?= $barang_masuk_hari_ini ?></h3>
                        <span class="text-muted">Update stok berdasarkan tanggal hari ini</span>
                    </div>
                </div>
            </div>

            <!-- Pengiriman Diproses -->
            <div class="col-lg-6 col-xl-6 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Pengiriman Diproses</h5>
                        <h3 class="fw-bold"><?= $jumlah_pengiriman ?></h3>
                        <span class="text-muted">Data pengiriman aktif</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- End::row-statistik -->
    </div>
</div>
<!-- End::app-content -->

<?php require_once APP_PATH . '/views/layout/footer.php'; ?>