<?php
include '../session-start.php';
include '../config.php';

// Ambil ID pemesanan dari parameter URL
$id_pemesanan = $_GET['id_pesan'];

// Menangani data saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $tgl_pemesanan = $_POST['tgl_pemesanan'];
    $no_pemesanan = $_POST['no_pemesanan'];
    $nm_pengaju = $_POST['nm_pengaju'];
    $nm_supplier = $_POST['nm_supplier'];
    $alamat_pengiriman = $_POST['alamat_pengiriman'];
    $kode_barang = $_POST['kode_barang'];
    $nm_brg = $_POST['nm_brg'];
    $jml_brg = (int)$_POST['jml_brg'];
    $hrg_perunit = (int)$_POST['hrg_perunit'];

    // Menghitung total harga berdasarkan jumlah barang dan harga per unit
    $total_hrg = $jml_brg * $hrg_perunit;

    $tgl_pengiriman_inginkan = $_POST['tgl_pengiriman_inginkan'];

    // Perbarui data di dalam database
    $stmt = $conn->prepare("UPDATE pesan_barang SET tgl_pemesanan = ?, no_pemesanan = ?, nm_pengaju = ?, nm_supplier = ?, alamat_pengiriman = ?, kode_barang = ?, nm_brg = ?, jml_brg = ?, hrg_perunit = ?, total_hrg = ?, tgl_pengiriman_inginkan = ? WHERE id_pesan = ?");
    $stmt->bind_param("sssssssiisss", $tgl_pemesanan, $no_pemesanan, $nm_pengaju, $nm_supplier, $alamat_pengiriman, $kode_barang, $nm_brg, $jml_brg, $hrg_perunit, $total_hrg, $tgl_pengiriman_inginkan, $id_pemesanan);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui.'); window.location.href='data_pemesanan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.location.href='edit_data_pemesanan.php?id=" . $id_pemesanan . "';</script>";
    }

    $stmt->close();
}

// Ambil data pemesanan untuk ditampilkan pada formulir
$stmt = $conn->prepare("SELECT * FROM pesan_barang WHERE id_pesan = ?");
$stmt->bind_param("s", $id_pemesanan);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$stmt->close();
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




                <<!-- Start::page-header -->

                    <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb">
                        <div>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Edit</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Halaman Edit Pemesanan</li>
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
                                            Edit Pemesanan
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row gy-4">
                                            <form action="" method="POST">
                                                <div class="row">


                                                    <!-- Input Tanggal Pemesanan -->
                                                    <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="tgl_pemesanan" class="form-label">Tanggal Pemesanan</label>
                                                        <input type="date" class="form-control" name="tgl_pemesanan" value="<?= $data['tgl_pemesanan'] ?>" required>
                                                    </div>

                                                    <!-- Input No Pemesanan -->
                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="no_pemesanan" class="form-label">No Pemesanan</label>
                                                        <input type="text" class="form-control" name="no_pemesanan" value="<?= $data['no_pemesanan'] ?>" required>
                                                    </div>

                                                    <!-- Input Nama Pengaju -->
                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="nm_pengaju" class="form-label">Nama Pengaju</label>
                                                        <input type="text" class="form-control" name="nm_pengaju" value="<?= $data['nm_pengaju'] ?>" required>
                                                    </div>

                                                    <!-- Input Nama Supplier -->
                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="nm_supplier" class="form-label">Nama Supplier</label>
                                                        <input type="text" class="form-control" name="nm_supplier" value="<?= $data['nm_supplier'] ?>" required>
                                                    </div>

                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="alamat_pengiriman" class="form-label">Alamat Pengiriman</label>
                                                        <input type="text" class="form-control" name="alamat_pengiriman" value="<?= $data['alamat_pengiriman'] ?>" required>
                                                    </div>

                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="kode_barang" class="form-label">Kode Barang</label>
                                                        <input type="text" class="form-control" name="kode_barang" value="<?= $data['kode_barang'] ?>" required>
                                                    </div>

                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="nm_brg" class="form-label">Nama Barang</label>
                                                        <input type="text" class="form-control" name="nm_brg" value="<?= $data['nm_brg'] ?>" required>
                                                    </div>

                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="jml_brg" class="form-label">Jumla Barang</label>
                                                        <input type="text" class="form-control" name="jml_brg" value="<?= $data['jml_brg'] ?>" required>
                                                    </div>

                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="hrg_perunit" class="form-label">Harga Per Dus</label>
                                                        <input type="text" class="form-control" name="hrg_perunit" value="<?= $data['hrg_perunit'] ?>" required>
                                                    </div>

                                                    <div class=" col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                        <label for="tgl_pengiriman_inginkan" class="form-label">Tanggal Pengiriman yang Diinginkan</label>
                                                        <input type="date" class="form-control" name="tgl_pengiriman_inginkan" value="<?= $data['tgl_pengiriman_inginkan'] ?>" required>
                                                    </div>

                                                </div>
                                                <!-- Submit Button -->
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary">Perbarui</button>
                                                        <a href="data_pemesanan.php" class="btn btn-secondary">Batal</a>
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