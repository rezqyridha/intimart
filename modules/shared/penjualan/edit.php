<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=penjualan");
    exit;
}

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = trim($_POST['id_barang'] ?? '');
    $jumlah    = trim($_POST['jumlah'] ?? '');
    $tanggal   = trim($_POST['tanggal'] ?? '');

    if (!$id_barang || !$jumlah || !$tanggal || !is_numeric($jumlah) || $jumlah <= 0) {
        header("Location: edit.php?id=$id&msg=kosong&obj=penjualan");
        exit;
    }

    // Ambil harga terbaru
    $barang = $koneksi->query("SELECT harga_jual FROM barang WHERE id = $id_barang")->fetch_assoc();
    if (!$barang) {
        header("Location: edit.php?id=$id&msg=invalid&obj=penjualan");
        exit;
    }
    $harga_jual = $barang['harga_jual'];

    // Hitung ulang stok tersedia (kecuali jumlah penjualan ini sendiri)
    $stok_masuk = $koneksi->query("SELECT SUM(jumlah) AS total FROM barang_masuk WHERE id_barang = $id_barang")->fetch_assoc()['total'] ?? 0;
    $stok_keluar = $koneksi->query("SELECT SUM(jumlah) AS total FROM barang_keluar WHERE id_barang = $id_barang")->fetch_assoc()['total'] ?? 0;
    $stok_penjualan_lain = $koneksi->query("SELECT SUM(jumlah) AS total FROM penjualan WHERE id_barang = $id_barang AND id != $id")->fetch_assoc()['total'] ?? 0;

    $stok_tersedia = $stok_masuk - ($stok_keluar + $stok_penjualan_lain);

    if ($jumlah > $stok_tersedia) {
        header("Location: edit.php?id=$id&msg=failed&obj=penjualan");
        exit;
    }

    $harga_total = $jumlah * $harga_jual;

    $stmt = $koneksi->prepare("UPDATE penjualan SET id_barang = ?, jumlah = ?, total = ?, tanggal = ? WHERE id = ?");
    $stmt->bind_param("iidsi", $id_barang, $jumlah, $harga_total, $tanggal, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=penjualan");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=penjualan");
    }
    exit;
}

// Tampilkan data awal
$data = $koneksi->query("SELECT * FROM penjualan WHERE id = $id")->fetch_assoc();
if (!$data) {
    header("Location: index.php?msg=invalid&obj=penjualan");
    exit;
}

$barangList = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header">
                <div class="card-title mb-0">Edit Penjualan</div>
            </div>

            <form method="post" action="" class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = $barangList->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>" <?= $b['id'] == $data['id_barang'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" value="<?= $data['jumlah'] ?>" min="1" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>