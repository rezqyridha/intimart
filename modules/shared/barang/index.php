<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';

require_once '../../../koneksi.php';       // koneksi ke DB
require_once '../../../views/layout/header.php'; // header & sidebar

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php?error=unauthorized");
    exit;
}

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
    <link rel="icon" href="/intimart/assets/images/brand-logos/pt.jpg" type="image/x-icon">

    <!-- Choices JS -->
    <script src="/intimart/assets/libs/choices.js/public//intimart/assets/scripts/choices.min.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="/intimart/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Main Theme Js -->
    <script src="/intimart/assets/js/main.js"></script>

    <!-- Style Css -->
    <link href="/intimart/assets/css/styles.min.css" rel="stylesheet">

    <!-- Icons Css -->
    <link href="/intimart/assets/css/icons.css" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="/intimart/assets/libs/node-waves/waves.min.css" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="/intimart/assets/libs/simplebar/simplebar.min.css" rel="stylesheet">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="/intimart/assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="/intimart/assets/libs/@simonwep/pickr/themes/nano.min.css">

    <!-- Choices Css -->
    <link rel="icon" href="/intimart/assets/images/brand-logos/pt.jpg" type="image/x-icon">
    <link href="/intimart/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/intimart/assets/css/styles.min.css" rel="stylesheet">
    <link href="/intimart/assets/css/icons.css" rel="stylesheet">
    <link href="/intimart/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">

</head>

<body>

    <?php include APP_PATH . '/views/layout/header.php'; ?>
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
    <?php include APP_PATH . '/views/layout/footer.php'; ?>
    </div>
</body>

</html>