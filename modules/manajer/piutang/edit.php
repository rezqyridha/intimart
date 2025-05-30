<?php
include '../session-start.php';
include '../config.php';

// Cek apakah ID ada di URL
$id = $_GET['id_pelanggan'] ?? null;

// Jika ID tidak ada, tampilkan pesan error atau redirect
if (!$id) {
    echo "<script>alert('ID tidak ditemukan'); window.location.href='piutang.php';</script>";
    exit;
}

// Ambil data berdasarkan ID
$query = "SELECT * FROM piutang WHERE id_pelanggan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='piutang.php';</script>";
    exit;
}

$stmt->close();

// Ambil harga per dus berdasarkan id_penjualan
if (isset($row['id_penjualan'])) {
    $id_penjualan = $row['id_penjualan'];  // Dapatkan id_penjualan yang terkait dengan piutang
    $query_harga = "SELECT hrg_perdus FROM penjualan WHERE id_penjualan = ?";
    $stmt_harga = $conn->prepare($query_harga);
    $stmt_harga->bind_param("i", $id_penjualan);
    $stmt_harga->execute();
    $stmt_harga->bind_result($hrg_perdus);
    $stmt_harga->fetch();
    $stmt_harga->close();
} else {
    echo "<script>alert('ID Penjualan tidak ditemukan'); window.location.href='piutang.php';</script>";
    exit;
}

// Jika data form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $nm_pelanggan = $_POST['nm_pelanggan'] ?? '';
    $nm_toko = $_POST['nm_toko'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $tempo_pembayaran = $_POST['tempo_pembayaran'] ?? '';
    $nama_barang = $_POST['nama_barang'] ?? '';
    $jumlah_barang = $_POST['jumlah_barang'] ?? '0'; // Default 0 jika kosong
    $id_penjualan = $_POST['id_penjualan'] ?? $id_penjualan;  // Pastikan menggunakan id_penjualan yang benar

    // Upload foto KTP jika ada perubahan
    $foto_ktp = $row['foto_ktp']; // Default foto KTP lama
    if (!empty($_FILES['foto_ktp']['name']) && $_FILES['foto_ktp']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $file_extension = pathinfo($_FILES['foto_ktp']['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            echo "<script>alert('Format foto KTP tidak valid (hanya JPG, JPEG, PNG, PDF).'); window.history.back();</script>";
            exit;
        }

        // Set foto_ktp baru jika diupload
        $foto_ktp = 'uploads/' . time() . '_' . basename($_FILES['foto_ktp']['name']);
        $upload_dir = '../' . $foto_ktp;

        if (!is_dir('../uploads')) {
            mkdir('../uploads', 0777, true);
        }

        if (!move_uploaded_file($_FILES['foto_ktp']['tmp_name'], $upload_dir)) {
            echo "<script>alert('Gagal mengupload foto KTP.'); window.history.back();</script>";
            exit;
        }
    }

    // Hitung total berdasarkan harga per dus
    $total = $hrg_perdus * $jumlah_barang;

    // Update data ke dalam database
    $stmt = $conn->prepare("UPDATE piutang SET nm_pelanggan = ?, nm_toko = ?, no_hp = ?, alamat = ?, foto_ktp = ?, tempo_pembayaran = ?, nama_barang = ?, jumlah_barang = ?, total = ?, id_penjualan = ? WHERE id_pelanggan = ?");
    if (!$stmt) {
        echo "<script>alert('Terjadi kesalahan pada query: " . $conn->error . "'); window.history.back();</script>";
        exit;
    }

    $stmt->bind_param("ssssssssiis", $nm_pelanggan, $nm_toko, $no_hp, $alamat, $foto_ktp, $tempo_pembayaran, $nama_barang, $jumlah_barang, $total, $id_penjualan, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!\\nTotal Piutang: Rp " . number_format($total, 0, ',', '.') . "'); window.location.href='data_piutang.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
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
                            <li class="breadcrumb-item active" aria-current="page">Halaman Edit Piutang</li>
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
                                        Edit Piutang
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-4">
                                        <!-- Form Edit -->
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="nm_pelanggan" class="form-label">Nama Pelanggan</label>
                                                    <input type="text" class="form-control" name="nm_pelanggan" value="<?= htmlspecialchars($row['nm_pelanggan']); ?>" required>
                                                </div>

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="nm_toko" class="form-label">Nama Toko</label>
                                                    <input type="text" class="form-control" name="nm_toko" value="<?= htmlspecialchars($row['nm_toko']); ?>" required>
                                                </div>

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="no_hp" class="form-label">No HP</label>
                                                    <input type="text" class="form-control" name="no_hp" value="<?= htmlspecialchars($row['no_hp']); ?>" required>
                                                </div>

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="alamat" class="form-label">Alamat</label>
                                                    <input type="text" class="form-control" name="alamat" value="<?= htmlspecialchars($row['alamat']); ?>" required>
                                                </div>

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="foto_ktp" class="form-label">Foto KTP</label>
                                                    <input type="file" class="form-control" name="foto_ktp">
                                                    <?php if ($row['foto_ktp']): ?>
                                                        <br><a href="../<?= $row['foto_ktp']; ?>" target="_blank">Lihat Foto KTP</a>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="tempo_pembayaran" class="form-label">Tempo Pembayaran</label>
                                                    <select class="form-control" name="tempo_pembayaran" required>
                                                        <option value="">Pilih Tempo</option>
                                                        <option value="2 Minggu" <?= $row['tempo_pembayaran'] == '2 Minggu' ? 'selected' : ''; ?>>2 Minggu</option>
                                                        <option value="4 Minggu" <?= $row['tempo_pembayaran'] == '4 Minggu' ? 'selected' : ''; ?>>4 Minggu</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="nama_barang" class="form-label">Nama Barang</label>
                                                    <input type="text" class="form-control" name="nama_barang" value="<?= htmlspecialchars($row['nama_barang']); ?>" required>
                                                </div>

                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="jumlah_barang" class="form-label">Jumlah Barang</label>
                                                    <input type="text" class="form-control" name="jumlah_barang" value="<?= htmlspecialchars($row['jumlah_barang']); ?>" required>
                                                </div>

                                                <!-- Menampilkan Harga Per Dus -->
                                                <div class="col-md-6 col-xl-6 col-xxl-6 mb-3">
                                                    <label for="harga_perdus" class="form-label">Harga Per Dus</label>
                                                    <input type="text" class="form-control" name="harga_perdus" value="<?= number_format($hrg_perdus, 0, ',', '.'); ?>" readonly>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">Perbarui</button>
                                                    <a href="data_piutang.php" class="btn btn-secondary">Batal</a>
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