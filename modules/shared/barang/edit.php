<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=barang");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=barang");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=barang");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang   = trim($_POST['nama_barang'] ?? '');
    $satuan        = trim($_POST['satuan'] ?? '');
    $harga_beli    = floatval($_POST['harga_beli'] ?? 0);
    $harga_jual    = floatval($_POST['harga_jual'] ?? 0);
    $stok_minimum  = intval($_POST['stok_minimum'] ?? 0);

    // Validasi input
    if ($nama_barang === '' || $satuan === '' || $harga_beli <= 0 || $harga_jual <= 0) {
        header("Location: edit.php?id=$id&msg=kosong&obj=barang");
        exit;
    }

    // Cek duplikasi nama barang (selain data ini)
    $stmt = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE nama_barang = ? AND id != ?");
    $stmt->bind_param("si", $nama_barang, $id);
    $stmt->execute();
    $stmt->bind_result($duplikat);
    $stmt->fetch();
    $stmt->close();

    if ($duplikat > 0) {
        header("Location: edit.php?id=$id&msg=duplicate&obj=barang");
        exit;
    }

    // Update data
    $stmt = $koneksi->prepare("UPDATE barang SET nama_barang=?, satuan=?, harga_beli=?, harga_jual=?, stok_minimum=? WHERE id=?");
    $stmt->bind_param("ssddii", $nama_barang, $satuan, $harga_beli, $harga_jual, $stok_minimum, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=barang");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=barang");
    }
    $stmt->close();
    exit;
}
?>

<?php require_once LAYOUTS_PATH . '/head.php'; ?>
<?php require_once LAYOUTS_PATH . '/header.php'; ?>
<?php require_once LAYOUTS_PATH . '/topbar.php'; ?>
<?php require_once LAYOUTS_PATH . '/sidebar.php'; ?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Edit Data Barang</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control" required value="<?= htmlspecialchars($data['nama_barang']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" name="satuan" id="satuan" class="form-control" required value="<?= htmlspecialchars($data['satuan']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli</label>
                        <input type="number" step="0.01" name="harga_beli" id="harga_beli" class="form-control" required value="<?= $data['harga_beli'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual</label>
                        <input type="number" step="0.01" name="harga_jual" id="harga_jual" class="form-control" required value="<?= $data['harga_jual'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="stok_minimum" class="form-label">Stok Minimum</label>
                        <input type="number" name="stok_minimum" id="stok_minimum" class="form-control" required value="<?= $data['stok_minimum'] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>