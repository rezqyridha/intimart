<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=kadaluarsa");
    exit;
}

// Proses Simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang        = intval($_POST['id_barang'] ?? 0);
    $jumlah           = intval($_POST['jumlah'] ?? 0);
    $lokasi           = trim($_POST['lokasi'] ?? '');
    $tanggal_expired  = $_POST['tanggal_expired'] ?? '';

    // Validasi sederhana
    if ($id_barang <= 0 || $jumlah <= 0 || !$tanggal_expired) {
        header("Location: add.php?msg=invalid&obj=kadaluarsa");
        exit;
    }

    // Simpan ke database
    $stmt = $koneksi->prepare("
        INSERT INTO barang_kadaluarsa (id_barang, jumlah, lokasi, tanggal_expired)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $id_barang, $jumlah, $lokasi, $tanggal_expired);

    if ($stmt->execute()) {
        header("Location: index.php?msg=added&obj=kadaluarsa");
    } else {
        header("Location: add.php?msg=failed&obj=kadaluarsa");
    }
    exit;
}

// Ambil daftar barang
$barangResult = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Tambah Barang Kadaluarsa</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>

            <form method="post">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = $barangResult->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Expired</label>
                        <input type="date" name="tanggal_expired" class="form-control" required>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>