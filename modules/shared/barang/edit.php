<?php
require '../../session_start.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php?error=unauthorized");
    exit;
}
require '../../koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?msg=invalid");
    exit;
}

$id = (int)$_GET['id'];

// Ambil data lama
$stmt = $conn->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$barang = $result->fetch_assoc();
$stmt->close();

if (!$barang) {
    header("Location: index.php?msg=invalid");
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_barang']);
    $satuan = trim($_POST['satuan']);
    $harga_beli = preg_replace('/[^\d]/', '', $_POST['harga_beli']);
    $harga_jual = preg_replace('/[^\d]/', '', $_POST['harga_jual']);
    $stok_minimum = $_POST['stok_minimum'];

    if (
        $nama === $barang['nama_barang'] &&
        $satuan === $barang['satuan'] &&
        $harga_beli == $barang['harga_beli'] &&
        $harga_jual == $barang['harga_jual'] &&
        $stok_minimum == $barang['stok_minimum']
    ) {
        header("Location: index.php?msg=nochange");
        exit;
    }

    // Cek duplikat nama + satuan selain yang sedang diedit
    $stmt = $conn->prepare("SELECT COUNT(*) FROM barang WHERE nama_barang = ? AND satuan = ? AND id != ?");
    $stmt->bind_param("ssi", $nama, $satuan, $id);
    $stmt->execute();
    $stmt->bind_result($duplikat);
    $stmt->fetch();
    $stmt->close();

    if ($duplikat > 0) {
        header("Location: index.php?msg=duplicate");
        exit;
    }

    // Lanjut update
    $stmt = $conn->prepare("UPDATE barang SET nama_barang=?, satuan=?, harga_beli=?, harga_jual=?, stok_minimum=? WHERE id=?");
    $stmt->bind_param("ssddii", $nama, $satuan, $harga_beli, $harga_jual, $stok_minimum, $id);
    $msg = $stmt->execute() ? 'success' : 'error';
    $stmt->close();

    header("Location: index.php?msg=$msg");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <title>Edit Barang | INTIBOGA</title>
    <link rel="icon" href="../../assets/images/brand-logos/pt.jpg" type="image/x-icon">
    <link href="../../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/styles.min.css" rel="stylesheet">
    <link href="../../assets/css/icons.css" rel="stylesheet">
    <link href="../../assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <div class="page">

        <?php include '../layouts/header.php'; ?>
        <aside class="app-sidebar sticky" id="sidebar">
            <div class="main-sidebar-header">
                <a href="dashboard.php" class="header-logo">
                    <img src="../../assets/images/brand-logos/pt.jpg" class="desktop-logo" alt="logo">
                </a>
            </div>
            <div class="main-sidebar" id="sidebar-scroll">
                <?php include '../admin/navbar.php'; ?>
            </div>
        </aside>

        <div class="main-content app-content">
            <div class="container-fluid">
                <h4 class="mb-4">Edit Data Barang</h4>
                <div class="card custom-card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Satuan</label>
                                <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($barang['satuan']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Harga Beli</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="harga_beli" id="harga_beli" class="form-control" required value="<?= number_format($barang['harga_beli'], 0, '', '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="harga_jual" id="harga_jual" class="form-control" required value="<?= number_format($barang['harga_jual'], 0, '', '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Stok Minimum</label>
                                <input type="number" name="stok_minimum" class="form-control" value="<?= $barang['stok_minimum'] ?>" required min="0">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer mt-auto py-3 bg-white text-center">
            <?php include '../../views/layout/copyright.php'; ?>
        </footer>
    </div>

    <script src="../../assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="../../assets/js/notifier.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script>
        new Cleave('#harga_beli', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: '',
            delimiter: ''
        });
        new Cleave('#harga_jual', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: '',
            delimiter: ''
        });
    </script>

</body>

</html>