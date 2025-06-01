<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';
require_once APP_PATH . '/koneksi.php'; // koneksi ke database

// Cek role admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: /intimart/forbidden.php");
    exit;
}

// Format tanggal
$today = date('Y-m-d');         // untuk query SQL
$todayDisplay = date('d-m-Y');  // untuk tampilan UI

// Statistik: jumlah user
$resultUser = $conn->query("SELECT COUNT(*) AS total_user FROM user");
$totalUser = $resultUser->fetch_assoc()['total_user'] ?? 0;

// Statistik: total barang
$resultBarang = $conn->query("SELECT COUNT(*) AS total_barang FROM barang");
$totalBarang = $resultBarang->fetch_assoc()['total_barang'] ?? 0;

// Statistik: transaksi hari ini
$resultJual = $conn->query("SELECT COUNT(*) AS total_jual FROM penjualan WHERE DATE(tanggal) = '$today'");
$totalJual = $resultJual->fetch_assoc()['total_jual'] ?? 0;

// Statistik: barang masuk hari ini
$resultMasuk = $conn->query("SELECT COUNT(*) AS total_masuk FROM barang_masuk WHERE DATE(tanggal) = '$today'");
$totalMasuk = $resultMasuk->fetch_assoc()['total_masuk'] ?? 0;

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
                        <h3 class="fw-bold"><?= $totalUser ?></h3>
                        <span class="text-muted">Data dari tabel user</span>
                    </div>
                </div>
            </div>

            <!-- Box Total Barang -->
            <div class="col-lg-6 col-xl-3 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Total Barang</h5>
                        <h3 class="fw-bold"><?= $totalBarang ?></h3>
                        <span class="text-muted">Data dari tabel barang</span>
                    </div>
                </div>
            </div>

            <!-- Box Transaksi Hari Ini -->
            <div class="col-lg-6 col-xl-3 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Transaksi Hari Ini</h5>
                        <h3 class="fw-bold"><?= $totalJual ?></h3>
                        <span class="text-muted">Penjualan ditanggal <?= $todayDisplay ?></span>
                    </div>
                </div>
            </div>

            <!-- Box Barang Masuk Hari Ini -->
            <div class="col-lg-6 col-xl-3 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body pb-3">
                        <h5 class="fs-14 mb-2">Barang Masuk Hari Ini</h5>
                        <h3 class="fw-bold"><?= $totalMasuk ?></h3>
                        <span class="text-muted">Stok masuk ditanggal <?= $todayDisplay ?></span>
                    </div>
                </div>
            </div>

        </div>
        <!-- End::row-statistik -->

    </div>
</div>
<!-- End::app-content -->

<?php require_once APP_PATH . '/views/layout/footer.php'; ?>