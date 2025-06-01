<?php
require_once '../../session_start.php';
if ($_SESSION['role'] !== 'manajer') {
    header("Location: /intimart/index.php?error=unauthorized");
    exit;
}

require_once APP_PATH . '/views/layout/header.php';
?>

<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb">
            <div>
                <h2 class="main-content-title fs-24 mb-1">Dashboard Manajer</h2>
                <p class="text-muted">Selamat datang, <?= htmlspecialchars($username) ?>.</p>
            </div>
        </div>

        <div class="row row-sm">

            <!-- Laba Bersih Bulan Ini -->
            <div class="col-lg-6 col-xl-4 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fs-14">Laba Bersih</h5>
                        <h3 class="fw-bold text-success">Rp 0</h3> <!-- Dummy -->
                    </div>
                </div>
            </div>

            <!-- Piutang Tersisa -->
            <div class="col-lg-6 col-xl-4 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fs-14">Total Piutang</h5>
                        <h3 class="fw-bold text-danger">Rp 0</h3> <!-- Dummy -->
                    </div>
                </div>
            </div>

            <!-- Penjualan Bulan Ini -->
            <div class="col-lg-6 col-xl-4 col-md-6 col-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <h5 class="fs-14">Penjualan Bulan Ini</h5>
                        <h3 class="fw-bold text-primary">Rp 0</h3> <!-- Dummy -->
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layout/footer.php'; ?>