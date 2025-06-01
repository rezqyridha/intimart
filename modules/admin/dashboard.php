<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';
require_once APP_PATH . '/views/layout/head.php';
require_once APP_PATH . '/views/layout/header.php';
?>

<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Start::page-header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb">
            <div>
                <h2 class="main-content-title fs-24 mb-1">Selamat Datang di Aplikasi PT. INTIBOGA MANDIRI</h2>
                <p class="text-muted mb-0">Dashboard Administrator</p>
            </div>
        </div>
        <!-- End::page-header -->

        <!-- Start::row-statistik -->
        <div class="row row-sm">
            <!-- Box Jumlah Pengguna -->
            <div class="col-lg-6 col-xl-3 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Jumlah Pengguna</h5>
                        <h3 class="fw-bold">0</h3>
                        <span class="text-muted">Coming soon (data dummy)</span>
                    </div>
                </div>
            </div>

            <!-- Box Total Barang -->
            <div class="col-lg-6 col-xl-3 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Total Barang</h5>
                        <h3 class="fw-bold">0</h3>
                        <span class="text-muted">Akan terhubung ke tabel barang</span>
                    </div>
                </div>
            </div>

            <!-- Box Transaksi Hari Ini -->
            <div class="col-lg-6 col-xl-3 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Transaksi Hari Ini</h5>
                        <h3 class="fw-bold">0</h3>
                        <span class="text-muted">Statistik penjualan per hari</span>
                    </div>
                </div>
            </div>

            <!-- Box Barang Masuk Hari Ini -->
            <div class="col-lg-6 col-xl-3 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Barang Masuk Hari Ini</h5>
                        <h3 class="fw-bold">0</h3>
                        <span class="text-muted">Statistik update stok</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- End::row-statistik -->

    </div>
</div>
<!-- End::app-content -->

<?php require_once APP_PATH . '/views/layout/footer.php'; ?>