<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$role = $_SESSION['role'] ?? '';
$id_user = $_SESSION['id_user'] ?? 0;

if ($role !== 'sales') {
    header("Location: index.php?msg=unauthorized&obj=pemesanan");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=pemesanan");
    exit;
}

// Ambil data pemesanan milik sendiri + status = menunggu
$stmt = $koneksi->prepare("
    SELECT p.*, b.nama_barang, b.satuan
    FROM pemesanan p
    JOIN barang b ON p.id_barang = b.id
    WHERE p.id = ? AND p.id_sales = ? AND p.status = 'menunggu'
");
$stmt->bind_param("ii", $id, $id_user);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    header("Location: index.php?msg=locked_or_notfound&obj=pemesanan");
    exit;
}

// Ambil list barang untuk pilihan ulang
$barangList = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");

// Proses submit update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = intval($_POST['id_barang'] ?? 0);
    $jumlah    = intval($_POST['jumlah'] ?? 0);
    $catatan   = trim($_POST['catatan'] ?? '');

    if ($id_barang <= 0 || $jumlah <= 0) {
        header("Location: edit.php?id=$id&msg=kosong&obj=pemesanan");
        exit;
    }

    $stmt = $koneksi->prepare("
        UPDATE pemesanan
        SET id_barang = ?, jumlah = ?, catatan = ?
        WHERE id = ? AND id_sales = ? AND status = 'menunggu'
    ");
    $stmt->bind_param("iisii", $id_barang, $jumlah, $catatan, $id, $id_user);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=pemesanan");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=pemesanan");
    }
    exit;
}
?>

<?php
require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Pemesanan</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>

            <form method="post" action="edit.php?id=<?= $id ?>">
                <div class="card-body">

                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Pilih Barang</label>
                        <select name="id_barang" id="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = $barangList->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>" <?= $b['id'] == $data['id_barang'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required min="1"
                            value="<?= $data['jumlah'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3"><?= htmlspecialchars($data['catatan']) ?></textarea>
                    </div>

                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>