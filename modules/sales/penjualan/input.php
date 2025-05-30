<?php
include '../session-start.php';
include '../config.php';

$total_penjualan = 0; // Inisialisasi total penjualan

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $id_manager = $_POST['id_manager'];
    $nm_toko = $_POST['nm_toko'];
    $tgl = $_POST['tgl'];
    $no_transaksi = $_POST['no_transaksi'];
    $nm_brg = $_POST['nm_brg'];
    $jumlah = (int)$_POST['jumlah'];
    $hrg_perdus = (int)$_POST['hrg_perdus'];

    // Hitung total penjualan otomatis
    $total_penjualan = $jumlah * $hrg_perdus;

    $nm_sales = $_POST['nm_sales'];
    $area_penjualan = $_POST['area_penjualan'];

    // Masukkan data ke dalam database
    $stmt = $conn->prepare("INSERT INTO penjualan (id_manager, nm_toko, tgl, no_transaksi, nm_brg, jumlah, hrg_perdus, total_penjualan, nm_sales, area_penjualan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssiiiss", $id_manager, $nm_toko, $tgl, $no_transaksi, $nm_brg, $jumlah, $hrg_perdus, $total_penjualan, $nm_sales, $area_penjualan);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data berhasil disimpan!\\nTotal Penjualan: Rp " . number_format($total_penjualan, 0, ',', '.') . "');
                window.location.href='penjualan.php';
              </script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.location.href='penjualan.php';</script>";
    }

    $stmt->close();
}

// Mengambil data manager untuk dropdown
$queryManager = "SELECT id_manager, nm_manager, jabatan FROM manager";
$resultManager = $conn->query($queryManager);
$managerList = [];
while ($row = $resultManager->fetch_assoc()) {
    $managerList[] = $row;
}

$conn->close();
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
    <link rel="icon" href="../assets/images/brand-logos/PUPR.png" type="image/x-icon">

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
                                <img src="../assets/images/brand-logos/DISDIK.png" alt="logo" class="desktop-logo">
                                <img src="../assets/images/brand-logos/DISDIK.png" alt="logo" class="toggle-logo">
                                <img src="../assets/images/brand-logos/DISDIK.png" alt="logo" class="desktop-dark" style="height: 50px;">
                                <img src="../assets/images/brand-logos/DISDIK.png" alt="logo" class="toggle-dark" style="height: 50px;">
                                <img src="../assets/images/brand-logos/DISDIK.png" alt="logo" class="desktop-white">
                                <img src="../assets/images/brand-logos/DISDIK.png" alt="logo" class="toggle-white">
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
                            <li><a class="dropdown-item d-flex border-bottom" href="profile.html"><i class="fe fe-user fs-16 align-middle me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item d-flex" href="../logout.php"><i class="fe fe-power fs-16 align-middle me-2"></i>Log Out</a></li>
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
                    <img src="../assets/images/brand-logos/DISDIK.png" class="desktop-white" alt="logo">
                    <img src="../assets/images/brand-logos/DISDIK.png" class="toggle-white" alt="logo">
                    <img src="../assets/images/brand-logos/DISDIK.png" class="desktop-logo" alt="logo">
                    <img src="../assets/images/brand-logos/DISDIK.png" class="toggle-dark" alt="logo" style="width: 50px;">
                    <img src="../assets/images/brand-logos/DISDIK.png" class="toggle-logo" alt="logo">
                    <img src="../assets/images/brand-logos/DISDIK.png" class="desktop-dark" alt="logo" style="height: 50px;">
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
                                        INPUT PENJUALAN DARI SALES
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-4">
                                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                        <form action="" method="POST">
                                            <div class="row">

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="id_manager" class="form-label">Pilih Manager</label>
                                                    <select id="id_manager" name="id_manager" class="form-control" required>
                                                        <option value="">Pilih Manager</option>
                                                        <?php foreach ($managerList as $manager): ?>
                                                            <option value="<?php echo htmlspecialchars($manager['id_manager']); ?>">
                                                                <?php echo htmlspecialchars($manager['nm_manager']) . " - " . htmlspecialchars($manager['jabatan']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- Input NIP -->
                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="nm_toko" class="form-label">Nama Toko</label>
                                                    <input type="text" class="form-control" name="nm_toko" placeholder="Masukkan nama toko" required>
                                                </div>

                                                <!-- Input Jabatan -->
                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="tgl" class="form-label">Tanggal Terjual</label>
                                                    <input type="date" class="form-control" name="tgl" placeholder="" required>
                                                </div>



                                                <!-- Input Kontak Pegawai -->
                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="no_transaksi" class="form-label">No Transaksi</label>
                                                    <input type="text" class="form-control" name="no_transaksi" placeholder="Masukkan no transaksi" required>
                                                </div>

                                                <!-- Input Kontak Pegawai -->
                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="nm_brg" class="form-label">Nama Barang</label>
                                                    <input type="text" class="form-control" name="nm_brg" placeholder="Masukkan nama barang" required>
                                                </div>

                                                <!-- Input Kontak Pegawai -->
                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="jumlah" class="form-label">Jumlah</label>
                                                    <input type="text" class="form-control" name="jumlah" placeholder="Masukkan jumlah" required>
                                                </div>



                                                <!-- Input Kontak Pegawai -->
                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="hrg_perdus" class="form-label">Harga Per Dus</label>
                                                    <input type="text" class="form-control" name="hrg_perdus" placeholder="Masukkan harga" required>
                                                </div>





                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="nm_sales" class="form-label">Nama Sales</label>
                                                    <input type="text" class="form-control" name="nm_sales" placeholder="Masukkan nama sales" required>
                                                </div>

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="area_penjualan" class="form-label">Area Penjualan</label>
                                                    <input type="text" class="form-control" name="area_penjualan" placeholder="Masukkan area penjualan" required>
                                                </div>
                                            </div>
                                            <!-- Submit Button -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
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

    <script>
        $(document).ready(function() {
            function hitungTotal() {
                var jumlah = parseInt($("#jumlah").val()) || 0;
                var hrgPerdus = parseInt($("#hrg_perdus").val()) || 0;
                var total = jumlah * hrgPerdus;
                $("#total_penjualan").val(total.toLocaleString()); // Format angka
                return total;
            }

            $("#jumlah, #hrg_perdus").on("input", function() {
                hitungTotal();
            });

            $("#formPenjualan").on("submit", function(e) {
                e.preventDefault(); // Mencegah submit default

                var formData = $(this).serialize();
                var total = hitungTotal();

                $.ajax({
                    type: "POST",
                    url: "proses_simpan.php", // Ganti dengan script PHP untuk menyimpan
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            title: "Data Berhasil Disimpan!",
                            text: "Total Penjualan: Rp " + total.toLocaleString(),
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "penjualan.php"; // Redirect setelah OK
                        });
                    },
                    error: function() {
                        Swal.fire("Error!", "Terjadi kesalahan saat menyimpan.", "error");
                    }
                });
            });
        });
    </script>

</body>

</html>