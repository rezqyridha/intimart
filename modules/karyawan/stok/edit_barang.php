<?php
include '../session-start.php';
include '../config.php';

// Menangani data saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $id_pesan = $_POST['id_pesan'];      // Mengambil id_pesan dari form
    $id_penjualan = $_POST['id_penjualan']; // Mengambil id_penjualan dari form

    // Query untuk mendapatkan jml_brg dari pesan_barang dan jumlah dari penjualan
    $query = "
        SELECT p.jml_brg, j.jumlah
        FROM pesan_barang p
        JOIN penjualan j ON p.id_pesan = j.id_penjualan
        WHERE p.id_pesan = ? AND j.id_penjualan = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id_pesan, $id_penjualan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Menghitung saldo
        $saldo_akhir = $row['jml_brg'] - $row['jumlah'];

        // Menyimpan perubahan data ke dalam tabel stokbrg_periode
        $updateStmt = $conn->prepare("UPDATE stokbrg_periode SET saldo_akhir = ? WHERE id_pesan = ? AND id_penjualan = ?");
        $updateStmt->bind_param("iii", $saldo_akhir, $id_pesan, $id_penjualan);

        if ($updateStmt->execute()) {
            echo "<script>alert('Data berhasil diperbarui.'); window.location.href='data_saldo_stok.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . $updateStmt->error . "'); window.location.href='saldo_stok.php';</script>";
        }

        $updateStmt->close();
    } else {
        echo "<script>alert('Data tidak ditemukan.'); window.location.href='saldo_stok.php';</script>";
    }

    $stmt->close();
}

// Mendapatkan data id_pesan dan id_penjualan untuk dropdown
$id_pesan_edit = $_GET['id_pesan'] ?? null;
$id_penjualan_edit = $_GET['id_penjualan'] ?? null;

// Ambil data untuk edit
$query = "SELECT id_pesan, jml_brg FROM pesan_barang";
$result = $conn->query($query);
$pesanList = [];
while ($row = $result->fetch_assoc()) {
    $pesanList[] = $row;
}

$query = "SELECT id_penjualan, jumlah FROM penjualan";
$result = $conn->query($query);
$penjualanList = [];
while ($row = $result->fetch_assoc()) {
    $penjualanList[] = $row;
}

// Jika id_pesan_edit dan id_penjualan_edit ada, ambil data yang ada di stokbrg_periode
if ($id_pesan_edit && $id_penjualan_edit) {
    $query = "SELECT saldo_akhir FROM stokbrg_periode WHERE id_pesan = ? AND id_penjualan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id_pesan_edit, $id_penjualan_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stokData = $result->fetch_assoc();
    $saldo_akhir = $stokData['saldo_akhir'] ?? null;
    $stmt->close();
}
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
                                        Edit Stok Barang
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-4">
                                        <form action="" method="POST">
                                            <div class="row">
                                                <!-- ID Masuk -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="id_pesan" class="form-label">Jumlah Barang Masuk</label>
                                                    <select name="id_pesan" id="id_pesan" class="form-control" required>
                                                        <option value="">Pilih Jumlah Barang Masuk</option>
                                                        <?php foreach ($pesanList as $pesan): ?>
                                                            <option value="<?php echo htmlspecialchars($pesan['id_pesan']); ?>"
                                                                <?php echo $pesan['id_pesan'] == $id_pesan_edit ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($pesan['jml_brg']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- ID Penjualan -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="id_penjualan" class="form-label">Jumlah Barang Keluar</label>
                                                    <select name="id_penjualan" id="id_penjualan" class="form-control" required>
                                                        <option value="">Pilih Jumlah Barang Keluar</option>
                                                        <?php foreach ($penjualanList as $penjualan): ?>
                                                            <option value="<?php echo htmlspecialchars($penjualan['id_penjualan']); ?>"
                                                                <?php echo $penjualan['id_penjualan'] == $id_penjualan_edit ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($penjualan['jumlah']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                
                                                <!-- Submit Button -->
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">Perbarui</button>
                                                    <a href="data_saldo_stok.php" class="btn btn-secondary">Batal</a>
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