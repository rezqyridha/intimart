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

// Ambil data barang untuk dropdown
$barangList = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = intval($_POST['id_barang'] ?? 0);
    $jumlah    = intval($_POST['jumlah'] ?? 0);
    $catatan   = trim($_POST['catatan'] ?? '');

    // Validasi minimal
    if ($id_barang <= 0 || $jumlah <= 0) {
        header("Location: add.php?msg=kosong&obj=pemesanan");
        exit;
    }

    // Simpan ke database
    $stmt = $koneksi->prepare("
        INSERT INTO pemesanan (id_barang, id_sales, jumlah, catatan)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiis", $id_barang, $id_user, $jumlah, $catatan);

    if ($stmt->execute()) {
        header("Location: index.php?msg=added&obj=pemesanan");
    } else {
        header("Location: add.php?msg=failed&obj=pemesanan");
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
                <h5 class="card-title mb-0">Ajukan Pemesanan Barang</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>

            <form method="post" action="add.php">
                <div class="card-body">

                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Pilih Barang</label>
                        <select name="id_barang" id="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = $barangList->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required min="1" placeholder="Masukkan jumlah">
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Tambahan catatan atau kebutuhan khusus"></textarea>
                    </div>

                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save me-1"></i> Ajukan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>