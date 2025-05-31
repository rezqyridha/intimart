<?php
require '../../session_start.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php?error=unauthorized");
    exit;
}
require '../../koneksi.php';

// Ambil data barang
$query = "SELECT * FROM barang ORDER BY nama_barang ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PT. INTIBOGA MANDIRI</title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

    <!-- Favicon -->
    <link rel="icon" href="../../assets/images/brand-logos/pt.jpg" type="image/x-icon">

    <!-- Choices JS -->
    <script src="../../assets/libs/choices.js/public/../../assets/scripts/choices.min.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="../../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Main Theme Js -->
    <script src="../../assets/js/main.js"></script>

    <!-- Style Css -->
    <link href="../../assets/css/styles.min.css" rel="stylesheet">

    <!-- Icons Css -->
    <link href="../../assets/css/icons.css" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="../../assets/libs/node-waves/waves.min.css" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="../../assets/libs/simplebar/simplebar.min.css" rel="stylesheet">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="../../assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="../../assets/libs/@simonwep/pickr/themes/nano.min.css">

    <!-- Choices Css -->
    <link rel="stylesheet" href="../../assets/libs/choices.js/public/../../assets/styles/choices.min.css">

    <link rel="stylesheet" href="../../assets/libs/sweetalert2/sweetalert2.min.css">
    <!--
    <link rel="stylesheet" href="../../assets/libs/jsvectormap/css/jsvectormap.min.css">

    <link rel="stylesheet" href="../../assets/libs/swiper/swiper-bundle.min.css">
-->



</head>

<body>

    <!-- Loader -->
    <div id="loader">
        <img src="../../assets/images/media/media-79.svg" alt="">
    </div>
    <!-- Loader -->

    <div class="page">
        <!-- app-header -->
        <header class="app-header">

            <!-- Start::main-header-container -->
            <div class="main-header-container container-fluid">

                <!-- Start::header-content-left -->
                <div class="header-content-left">

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <div class="horizontal-logo">
                            <a href="dashboard.php" class="header-logo">
                                <img src="../../assets/images/brand-logos/pt.jpg" alt="logo" class="desktop-logo">
                                <img src="../../assets/images/brand-logos/pt.jpg" alt="logo" class="toggle-logo">
                                <img src="../../assets/images/brand-logos/pt.jpg" alt="logo" class="desktop-dark" style="height: 50px;">
                                <img src="../../assets/images/brand-logos/pt.jpg" alt="logo" class="toggle-dark" style="height: 50px;">
                                <img src="../../assets/images/brand-logos/pt.jpg" alt="logo" class="desktop-white">
                                <img src="../../assets/images/brand-logos/pt.jpg" alt="logo" class="toggle-white">
                            </a>
                        </div>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <!-- Start::header-link -->
                        <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
                        <!-- End::header-link -->
                    </div>
                    <!-- End::header-element -->


                </div>
                <!-- End::header-content-left -->

                <!-- Start::header-content-right -->
                <div class="header-content-right">

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <!-- Start::header-link|dropdown-toggle -->
                        <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="header-link-icon">
                                    <img src="../../assets/images/faces/1.jpg" alt="img" width="32" height="32" class="rounded-circle">
                                </div>

                            </div>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                            <Li>
                                <div class="header-navheading border-bottom">
                                    <h6 class="main-notification-title">Jabatan : <?= htmlspecialchars($role) ?></h6>
                                    <p class="main-notification-text mb-0">Username : <?= htmlspecialchars($username) ?></p>
                                </div>
                            </Li>
                            <li><a class="dropdown-item d-flex" href="../../logout.php"><i class="fe fe-power fs-16 align-middle me-2"></i>Log Out</a></li>
                        </ul>
                    </div>
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-right -->

            </div>
            <!-- End::main-header-container -->

        </header>
        <!-- /app-header -->

        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="dashboard.php" class="header-logo">
                    <img src="../../assets/images/brand-logos/pt.jpg" class="desktop-white" alt="logo">
                    <img src="../../assets/images/brand-logos/pt.jpg" class="toggle-white" alt="logo">
                    <img src="../../assets/images/brand-logos/pt.jpg" class="desktop-logo" alt="logo">
                    <img src="../../assets/images/brand-logos/pt.jpg" class="toggle-dark" alt="logo" style="width: 50px;">
                    <img src="../../assets/images/brand-logos/pt.jpg" class="toggle-logo" alt="logo">
                    <img src="../../assets/images/brand-logos/pt.jpg" class="desktop-dark" alt="logo" style="height: 50px;">
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">
                <?php include '../admin/navbar.php'; ?>
            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <!-- Main Content -->
        <div class="main-content app-content">
            <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Manajemen Data Barang</h4>
                    <a href="add.php" class="btn btn-primary">+ Tambah Barang</a>
                </div>

                <div class="card custom-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-nowrap">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Satuan</th>
                                        <th>Harga Beli</th>
                                        <th>Harga Jual</th>
                                        <th>Stok Minimum</th>
                                        <th width="130">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php $no = 1;
                                        while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                <td><?= htmlspecialchars($row['satuan']) ?></td>
                                                <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                                                <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                                <td><?= (int)$row['stok_minimum'] ?></td>
                                                <td>
                                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                    <a href="#" onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-danger">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Belum ada data barang.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End::app-content -->

    <!-- Footer Start -->
    <footer class="footer mt-auto py-3 bg-white text-center">
        <?php include '../../views/layout/copyright.php'; ?>
    </footer>
    <!-- Footer End -->
    </div>


    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="fe fe-arrow-up"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->

    <!-- Popper JS -->
    <script src="../../assets/libs/@popperjs/core/umd/popper.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="../../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Defaultmenu JS -->
    <script src="../../assets/js/defaultmenu.min.js"></script>

    <!-- Node Waves JS-->
    <script src="../../assets/libs/node-waves/waves.min.js"></script>

    <!-- Sticky JS -->
    <script src="../../assets/js/sticky.js"></script>

    <!-- Simplebar JS -->
    <script src="../../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../../assets/js/simplebar.js"></script>

    <!-- Color Picker JS -->
    <script src="../../assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>

    <!--
    <script src="../../assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="../../assets/libs/jsvectormap/maps/world-merc.js"></script>
    <script src="../../assets/libs/apexcharts/apexcharts.min.js"></script>
    -->

    <!-- Main-Dashboard -->
    <script src="../../assets/js/index.js"></script>


    <!-- Custom-Switcher JS -->
    <script src="../../assets/js/custom-switcher.min.js"></script>

    <!-- Custom JS -->
    <script src="../../assets/js/custom.js"></script>

    <script src="../../assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <script src="../../assets/js/notifier.js"></script>

</body>

</html>