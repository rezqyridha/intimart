<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/koneksi.php';

// Validasi role
if ($_SESSION['role'] !== 'admin') {
    header("Location: /intimart/index.php?error=unauthorized");
    exit;
}

// Validasi ID barang
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?msg=invalid");
    exit;
}

$id = (int)$_GET['id'];

// Ambil data barang lama
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

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_barang']);
    $satuan = trim($_POST['satuan']);
    $harga_beli = preg_replace('/[^\d]/', '', $_POST['harga_beli']);
    $harga_jual = preg_replace('/[^\d]/', '', $_POST['harga_jual']);
    $stok_minimum = (int) $_POST['stok_minimum'];

    // Cek apakah tidak ada perubahan
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

    // Cek duplikat
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

    // Lakukan update
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
    <link rel="icon" href="/intimart/assets/images/brand-logos/pt.jpg" type="image/x-icon">
    <link href="/intimart/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/intimart/assets/css/styles.min.css" rel="stylesheet">
    <link href="/intimart/assets/css/icons.css" rel="stylesheet">
    <link href="/intimart/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <div class="page">

        <?php
        $role = $_SESSION['role'];
        $username = $_SESSION['username'] ?? 'User';
        $navbarPath = $_SERVER['DOCUMENT_ROOT'] . "/intimart/modules/$role/navbar.php";

        include $_SERVER['DOCUMENT_ROOT'] . '/intimart/views/layout/header.php';
        ?>

        <div class="main-content app-content">
            <div class="container-fluid">
                <h4 class="mb-4">Edit Data Barang</h4>
                <div class="card custom-card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" required value="<?= htmlspecialchars($barang['nama_barang']) ?>">
                            </div>
                            <div class="mb-3">
                                <label>Satuan</label>
                                <input type="text" name="satuan" class="form-control" required value="<?= htmlspecialchars($barang['satuan']) ?>">
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
                                <input type="number" name="stok_minimum" class="form-control" required min="0" value="<?= $barang['stok_minimum'] ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
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


    <!-- Cleave.js untuk format uang -->
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