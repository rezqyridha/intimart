<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=barang");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=barang");
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$barang = $result->fetch_assoc();
$stmt->close();

if (!$barang) {
    header("Location: index.php?msg=invalid&obj=barang");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang  = trim($_POST['nama_barang'] ?? '');
    $satuan       = trim($_POST['satuan'] ?? '');
    $harga_beli   = (int)($_POST['harga_beli'] ?? 0);
    $harga_jual   = (int)($_POST['harga_jual'] ?? 0);
    $stok_minimum = (int)($_POST['stok_minimum'] ?? 0);

    if ($nama_barang === '' || $satuan === '' || $harga_beli <= 0 || $harga_jual <= 0) {
        header("Location: edit.php?id=$id&msg=kosong&obj=barang");
        exit;
    }

    // Cek apakah data berubah
    if (
        $nama_barang === $barang['nama_barang'] &&
        $satuan === $barang['satuan'] &&
        $harga_beli == $barang['harga_beli'] &&
        $harga_jual == $barang['harga_jual'] &&
        $stok_minimum == $barang['stok_minimum']
    ) {
        header("Location: edit.php?id=$id&msg=nochange&obj=barang");
        exit;
    }

    $stmt = $koneksi->prepare("UPDATE barang SET nama_barang=?, satuan=?, harga_beli=?, harga_jual=?, stok_minimum=? WHERE id=?");
    $stmt->bind_param("ssiiii", $nama_barang, $satuan, $harga_beli, $harga_jual, $stok_minimum, $id);

    $stmt->execute() ?
        header("Location: index.php?msg=updated&obj=barang") :
        header("Location: edit.php?id=$id&msg=failed&obj=barang");

    $stmt->close();
    exit;
}

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card custom-card shadow-sm mt-5">
                    <div class="card-header">
                        <div class="card-title">Edit Barang</div>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3"><label class="form-label">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                            </div>
                            <div class="mb-3"><label class="form-label">Satuan</label>
                                <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($barang['satuan']) ?>" required>
                            </div>
                            <div class="mb-3"><label class="form-label">Harga Beli</label>
                                <input type="number" name="harga_beli" class="form-control" value="<?= $barang['harga_beli'] ?>" required>
                            </div>
                            <div class="mb-3"><label class="form-label">Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" value="<?= $barang['harga_jual'] ?>" required>
                            </div>
                            <div class="mb-3"><label class="form-label">Stok Minimum</label>
                                <input type="number" name="stok_minimum" class="form-control" value="<?= $barang['stok_minimum'] ?>" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-light">← Kembali</a>
                                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>