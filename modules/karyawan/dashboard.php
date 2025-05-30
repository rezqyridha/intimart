<?php
require '../../session_start.php';
if ($_SESSION['role'] !== 'karyawan') {
    header("Location: ../../index.php?error=unauthorized");
    exit;
}
require '../../koneksi.php';

$q1 = $conn->query("SELECT COUNT(*) AS masuk FROM barang_masuk WHERE tanggal = CURDATE()");
$barang_masuk = $q1->fetch_assoc()['masuk'] ?? 0;

$q2 = $conn->query("SELECT COUNT(*) AS keluar FROM barang_keluar WHERE tanggal = CURDATE()");
$barang_keluar = $q2->fetch_assoc()['keluar'] ?? 0;

// Total Stok dari tabel 'stok'
$q3 = $conn->query("SELECT SUM(jumlah) AS total_stok FROM stok");
$stok_total = $q3->fetch_assoc()['total_stok'] ?? 0;


$username = $_SESSION['username'] ?? 'Karyawan';
$role = $_SESSION['role'] ?? 'karyawan';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard Sales | PT. INTIBOGA MANDIRI</title>
    <link rel="icon" href="../../assets/images/brand-logos/pt.jpg" type="image/x-icon">
    <link id="style" href="../../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/styles.min.css" rel="stylesheet">
    <link href="../../assets/css/icons.css" rel="stylesheet">
    <link href="../../assets/libs/node-waves/waves.min.css" rel="stylesheet">
    <link href="../../assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="../../assets/libs/@simonwep/pickr/themes/nano.min.css">
    <link rel="stylesheet" href="../../assets/libs/choices.js/public/../../assets/styles/choices.min.css">
    <link rel="stylesheet" href="../../assets/libs/jsvectormap/css/jsvectormap.min.css">
    <link rel="stylesheet" href="../../assets/libs/swiper/swiper-bundle.min.css">
</head>

<body>
    <div id="loader"><img src="../../assets/images/media/media-79.svg" alt=""></div>
    <div class="page">
        <header class="app-header">
            <div class="main-header-container container-fluid">
                <div class="header-content-left">
                    <div class="header-element">
                        <div class="horizontal-logo">
                            <a href="dashboard.php" class="header-logo">
                                <img src="../../assets/images/brand-logos/pt.jpg" alt="logo" class="desktop-logo">
                                <img src="../../assets/images/brand-logos/pt.jpg" alt="logo" class="desktop-dark" style="height: 50px;">
                            </a>
                        </div>
                    </div>
                    <div class="header-element">
                        <a class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="#"><span></span></a>
                    </div>
                </div>
                <div class="header-content-right">
                    <div class="header-element">
                        <a href="#" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="header-link-icon">
                                    <img src="../../assets/images/faces/1.jpg" alt="img" width="32" height="32" class="rounded-circle">
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                            <li>
                                <div class="header-navheading border-bottom">
                                    <h6 class="main-notification-title">Jabatan : <?= htmlspecialchars($role) ?></h6>
                                    <p class="main-notification-text mb-0">Username : <?= htmlspecialchars($username) ?></p>
                                </div>
                            </li>
                            <li><a class="dropdown-item d-flex" href="../../logout.php"><i class="fe fe-power me-2"></i>Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <aside class="app-sidebar sticky" id="sidebar">
            <div class="main-sidebar-header">
                <a href="dashboard.php" class="header-logo">
                    <img src="../../assets/images/brand-logos/pt.jpg" class="desktop-logo" alt="logo">
                </a>
            </div>
            <div class="main-sidebar" id="sidebar-scroll">
                <?php include 'navbar.php'; ?>
            </div>
        </aside>

        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb">
                    <div>
                        <h2 class="main-content-title fs-24 mb-1">Selamat Datang di Aplikasi PT. INTIBOGA MANDIRI</h2>
                    </div>
                </div>

                <div class="row row-sm">
                    <div class="col-lg-4 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body pb-3">
                                <h5 class="fs-14">Barang Masuk Hari Ini</h5>
                                <h3 class="fw-bold mb-0"><?= $barang_masuk ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body pb-3">
                                <h5 class="fs-14">Barang Keluar Hari Ini</h5>
                                <h3 class="fw-bold mb-0"><?= $barang_keluar ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card custom-card">
                            <div class="card-body pb-3">
                                <h5 class="fs-14">Total Stok Barang</h5>
                                <h3 class="fw-bold mb-0"><?= $stok_total ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer mt-auto py-3 bg-white text-center">
            <?php include '../../views/layout/copyright.php'; ?>
        </footer>
    </div>

    <div class="scrollToTop"><span class="arrow"><i class="fe fe-arrow-up"></i></span></div>
    <div id="responsive-overlay"></div>

    <script src="../../assets/libs/@popperjs/core/umd/popper.min.js"></script>
    <script src="../../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/defaultmenu.min.js"></script>
    <script src="../../assets/libs/node-waves/waves.min.js"></script>
    <script src="../../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../../assets/js/simplebar.js"></script>
    <script src="../../assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
    <script src="../../assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="../../assets/libs/jsvectormap/maps/world-merc.js"></script>
    <script src="../../assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="../../assets/js/index.js"></script>
    <script src="../../assets/js/custom-switcher.min.js"></script>
    <script src="../../assets/js/custom.js"></script>
</body>

</html>