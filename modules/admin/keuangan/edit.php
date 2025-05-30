<?php
include '../session-start.php';
include '../config.php';

// Mendapatkan ID arus kas dari parameter URL
$id_arus_kas = isset($_GET['id_arus_kas']) ? intval($_GET['id_arus_kas']) : 0;

// Ambil data arus kas berdasarkan ID
$queryArusKas = "SELECT * FROM arus_kas WHERE id_arus_kas = ?";
$stmtArusKas = $conn->prepare($queryArusKas);
$stmtArusKas->bind_param("i", $id_arus_kas);
$stmtArusKas->execute();
$resultArusKas = $stmtArusKas->get_result();
$dataArusKas = $resultArusKas->fetch_assoc();
$stmtArusKas->close();

if (!$dataArusKas) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='data_arus_kas.php';</script>";
    exit;
}

// Menangani penyimpanan perubahan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_penjualan = $_POST['id_penjualan'];
    $id_laba_bersih = $_POST['id_laba_bersih'];

    // Ambil kembali nilai penjualan dan laba bersih
    $queryPenjualan = "SELECT total_penjualan FROM penjualan WHERE id_penjualan = ?";
    $stmtPenjualan = $conn->prepare($queryPenjualan);
    $stmtPenjualan->bind_param("i", $id_penjualan);
    $stmtPenjualan->execute();
    $resultPenjualan = $stmtPenjualan->get_result();
    $rowPenjualan = $resultPenjualan->fetch_assoc();
    $stmtPenjualan->close();

    $queryLabaBersih = "SELECT total_biaya FROM laba_bersih WHERE id_laba_bersih = ?";
    $stmtLabaBersih = $conn->prepare($queryLabaBersih);
    $stmtLabaBersih->bind_param("i", $id_laba_bersih);
    $stmtLabaBersih->execute();
    $resultLabaBersih = $stmtLabaBersih->get_result();
    $rowLabaBersih = $resultLabaBersih->fetch_assoc();
    $stmtLabaBersih->close();

    if ($rowPenjualan && $rowLabaBersih) {
        $total_penjualan = $rowPenjualan['total_penjualan'];
        $total_biaya = $rowLabaBersih['total_biaya'];
        $saldo_kas = $total_penjualan - $total_biaya;

        // Update data arus kas
        $updateQuery = "UPDATE arus_kas SET id_penjualan = ?, id_laba_bersih = ?, saldo_kas = ? WHERE id_arus_kas = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("iiii", $id_penjualan, $id_laba_bersih, $saldo_kas, $id_arus_kas);

        if ($updateStmt->execute()) {
            echo "<script>alert('Data berhasil diperbarui.'); window.location.href='data_arus_kas.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . $updateStmt->error . "'); window.location.href='edit_arus_kas.php?id_arus_kas=$id_arus_kas';</script>";
        }
        $updateStmt->close();
    }
}

// Ambil data penjualan untuk dropdown
$queryPenjualan = "SELECT id_penjualan, tgl, nm_brg, total_penjualan FROM penjualan";
$resultPenjualan = $conn->query($queryPenjualan);
$penjualanList = $resultPenjualan->fetch_all(MYSQLI_ASSOC);

// Ambil data laba bersih untuk dropdown
$queryLabaBersih = "SELECT id_laba_bersih, total_biaya FROM laba_bersih";
$resultLabaBersih = $conn->query($queryLabaBersih);
$labaBersihList = $resultLabaBersih->fetch_all(MYSQLI_ASSOC);
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
    <link rel="icon" href="../assets/images/brand-logos/pt.jpg" type="image/x-icon">

    <!-- Choices JS -->
    <script src="../assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Main Theme Js -->
    <script src="../assets/js/main.js"></script>

    <!-- Style Css -->
    <link href="../assets/css/styles.min.css" rel="stylesheet">

    <!-- Icons Css -->
    <link href="../assets/css/icons.css" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="../assets/libs/node-waves/waves.min.css" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="../assets/libs/simplebar/simplebar.min.css" rel="stylesheet">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="../assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="../assets/libs/@simonwep/pickr/themes/nano.min.css">

    <!-- Choices Css -->
    <link rel="stylesheet" href="../assets/libs/choices.js/public/assets/styles/choices.min.css">


    <link rel="stylesheet" href="../assets/libs/jsvectormap/css/jsvectormap.min.css">

    <link rel="stylesheet" href="../assets/libs/swiper/swiper-bundle.min.css">

</head>

<body>

    <!-- Loader -->
    <div id="loader">
        <img src="../assets/images/media/media-79.svg" alt="">
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
                            <a href="index.html" class="header-logo">
                                <img src="../assets/images/brand-logos/pt.jpg" alt="logo" class="desktop-logo">
                                <img src="../assets/images/brand-logos/pt.jpg" alt="logo" class="toggle-logo">
                                <img src="../assets/images/brand-logos/pt.jpg" alt="logo" class="desktop-dark" style="height: 50px;">
                                <img src="../assets/images/brand-logos/pt.jpg" alt="logo" class="toggle-dark" style="height: 50px;">
                                <img src="../assets/images/brand-logos/pt.jpg" alt="logo" class="desktop-white">
                                <img src="../assets/images/brand-logos/pt.jpg" alt="logo" class="toggle-white">
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
                                    <img src="../assets/images/faces/1.jpg" alt="img" width="32" height="32" class="rounded-circle">
                                </div>

                            </div>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                            <Li>
                                <div class="header-navheading border-bottom">
                                    <h6 class="main-notification-title">Dimas Rizal Maulana</h6>
                                    <p class="main-notification-text mb-0">Admin</p>
                                </div>
                            </Li>
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
                <a href="index.html" class="header-logo">
                    <img src="../assets/images/brand-logos/pt.jpg" class="desktop-white" alt="logo">
                    <img src="../assets/images/brand-logos/pt.jpg" class="toggle-white" alt="logo">
                    <img src="../assets/images/brand-logos/pt.jpg" class="desktop-logo" alt="logo">
                    <img src="../assets/images/brand-logos/pt.jpg" class="toggle-dark" alt="logo" style="width: 50px;">
                    <img src="../assets/images/brand-logos/pt.jpg" class="toggle-logo" alt="logo">
                    <img src="../assets/images/brand-logos/pt.jpg" class="desktop-dark" alt="logo" style="height: 50px;">
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">
                <?php include 'navbar.php'; ?>
            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">

                <!-- Start::page-header -->

                <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb">
                    <div>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Edit</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Halaman Edit Stok Fisik</li>
                        </ol>
                    </div>
                </div>

                <!-- End::page-header -->

                <!-- Start::row-1 -->
                <div class="row row-sm">
                    <!-- Content Body -->
                    <!-- Start:: row-1 -->
                    <div class="row row-sm">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        Edit Stok Fisik
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-4">
                                        <!-- Form Edit -->
                                        <form action="" method="POST">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="id_penjualan" class="form-label">Total Pemasukan</label>
                                                    <select class="form-control" name="id_penjualan" required>
                                                        <option value="">Pilih Total Pemasukan</option>
                                                        <?php foreach ($penjualanList as $penjualan): ?>
                                                            <option value="<?php echo $penjualan['id_penjualan']; ?>" <?php echo ($dataArusKas['id_penjualan'] == $penjualan['id_penjualan']) ? 'selected' : ''; ?>>
                                                                <?php echo $penjualan['nm_brg'] . " - Total: " . number_format($penjualan['total_penjualan'], 0, ',', '.') . " - Tanggal: " . date('d-m-Y', strtotime($penjualan['tgl'])); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="id_laba_bersih" class="form-label">Total Pengeluaran</label>
                                                    <select class="form-control" name="id_laba_bersih" required>
                                                        <option value="">Pilih Total Pengeluaran</option>
                                                        <?php foreach ($labaBersihList as $labaBersih): ?>
                                                            <option value="<?php echo $labaBersih['id_laba_bersih']; ?>" <?php echo ($dataArusKas['id_laba_bersih'] == $labaBersih['id_laba_bersih']) ? 'selected' : ''; ?>>
                                                                Total Biaya: <?php echo number_format($labaBersih['total_biaya'], 0, ',', '.'); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">Perbarui</button>
                                                    <a href="data_arus_kas.php" class="btn btn-secondary">Batal</a> <!-- Tombol Batal -->
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End:: row-1 -->
                </div>
                <!-- End::row-1 -->

            </div>
        </div>
        <!-- End::app-content -->

        <!-- Footer Start -->
        <footer class="footer mt-auto py-3 bg-white text-center">
            <?php include 'copyright.php'; ?>
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
    <script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Defaultmenu JS -->
    <script src="../assets/js/defaultmenu.min.js"></script>

    <!-- Node Waves JS-->
    <script src="../assets/libs/node-waves/waves.min.js"></script>

    <!-- Sticky JS -->
    <script src="../assets/js/sticky.js"></script>

    <!-- Simplebar JS -->
    <script src="../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../assets/js/simplebar.js"></script>

    <!-- Color Picker JS -->
    <script src="../assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>


    <!-- JSVector Maps JS -->
    <script src="../assets/libs/jsvectormap/js/jsvectormap.min.js"></script>

    <!-- JSVector Maps MapsJS -->
    <script src="../assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!-- Apex Charts JS -->
    <script src="../assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Main-Dashboard -->
    <script src="../assets/js/index.js"></script>


    <!-- Custom-Switcher JS -->
    <script src="../assets/js/custom-switcher.min.js"></script>

    <!-- Custom JS -->
    <script src="../assets/js/custom.js"></script>

</body>

</html>