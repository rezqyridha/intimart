<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$id = $_GET['id'] ?? null;
$submit = $_POST['submit'] ?? null;

// === Handle Update ===
if ($submit) {
    $id_barang  = trim($_POST['id_barang'] ?? '');
    $jumlah     = trim($_POST['jumlah'] ?? '');
    $tanggal    = trim($_POST['tanggal'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? null);

    if (!$id || !$id_barang || !$jumlah || !$tanggal || !is_numeric($jumlah) || $jumlah <= 0) {
        header("Location: edit.php?id=$id&msg=invalid&obj=barang_masuk");
        exit;
    }

    $stmt = $koneksi->prepare("UPDATE barang_masuk SET id_barang = ?, jumlah = ?, tanggal = ?, keterangan = ? WHERE id = ?");
    $stmt->bind_param("iissi", $id_barang, $jumlah, $tanggal, $keterangan, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=barang_masuk");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=barang_masuk");
    }
    exit;
}

// === Tampilkan Form ===
if (!$id || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=barang_masuk");
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM barang_masuk WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=barang_masuk");
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
                <div class="card-title mb-0">Edit Data Barang Masuk</div>
            </div>

            <form method="post" action="" class="card-body">
                <input type="hidden" name="submit" value="1">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = $barangList->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>" <?= $data['id_barang'] == $b['id'] ? 'selected' : '' ?>>
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
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
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