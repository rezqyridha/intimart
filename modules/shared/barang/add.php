<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/koneksi.php';

// Validasi role admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: /intimart/index.php?error=unauthorized");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'] ?? 'User';
$navbarPath = $_SERVER['DOCUMENT_ROOT'] . "/intimart/modules/$role/navbar.php";

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_barang']);
    $satuan = trim($_POST['satuan']);
    $harga_beli = preg_replace('/[^\d]/', '', $_POST['harga_beli']);
    $harga_jual = preg_replace('/[^\d]/', '', $_POST['harga_jual']);
    $stok_minimum = (int) $_POST['stok_minimum'];

    if ($nama && $satuan && $harga_beli && $harga_jual && $stok_minimum >= 0) {
        $cek = $conn->prepare("SELECT COUNT(*) FROM barang WHERE nama_barang = ? AND satuan = ?");
        $cek->bind_param("ss", $nama, $satuan);
        $cek->execute();
        $cek->bind_result($ada);
        $cek->fetch();
        $cek->close();

        if ($ada > 0) {
            header("Location: add.php?msg=duplikat");
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO barang (nama_barang, satuan, harga_beli, harga_jual, stok_minimum) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddi", $nama, $satuan, $harga_beli, $harga_jual, $stok_minimum);

        if ($stmt->execute()) {
            header("Location: index.php?msg=added");
        } else {
            header("Location: add.php?msg=failed");
        }
        exit;
    } else {
        header("Location: add.php?msg=kosong");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <title>Tambah Barang | INTIBOGA</title>
    <link rel="icon" href="/intimart/assets/images/brand-logos/pt.jpg" type="image/x-icon">

    <!-- Styles -->
    <link href="/intimart/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/intimart/assets/css/styles.min.css" rel="stylesheet">
    <link href="/intimart/assets/css/icons.css" rel="stylesheet">
    <link href="/intimart/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <div class="page">

        <?php include $_SERVER['DOCUMENT_ROOT'] . '/intimart/views/layout/header.php'; ?>

        <div class="main-content app-content">
            <div class="container-fluid">
                <h4 class="mb-4">Tambah Data Barang</h4>
                <div class="card custom-card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Satuan</label>
                                <input type="text" name="satuan" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Harga Beli</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="harga_beli" id="harga_beli" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="harga_jual" id="harga_jual" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Stok Minimum</label>
                                <input type="number" name="stok_minimum" class="form-control" required min="0">
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="/intimart/modules/shared/barang/index.php" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer mt-auto py-3 bg-white text-center">
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/intimart/views/layout/footer.php'; ?>
        </footer>
    </div>

</body>

</html>