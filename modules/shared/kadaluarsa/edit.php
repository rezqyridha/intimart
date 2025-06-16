<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=kadaluarsa");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=kadaluarsa");
    exit;
}

// 🔄 PROSES UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang        = intval($_POST['id_barang'] ?? 0);
    $jumlah           = intval($_POST['jumlah'] ?? 0);
    $lokasi           = trim($_POST['lokasi'] ?? '');
    $tanggal_expired  = $_POST['tanggal_expired'] ?? '';

    if ($id_barang <= 0 || $jumlah <= 0 || !$tanggal_expired) {
        header("Location: edit.php?id=$id&msg=invalid&obj=kadaluarsa");
        exit;
    }

    $stmt = $koneksi->prepare("
        UPDATE barang_kadaluarsa 
        SET id_barang = ?, jumlah = ?, lokasi = ?, tanggal_expired = ?
        WHERE id = ?
    ");
    $stmt->bind_param("iissi", $id_barang, $jumlah, $lokasi, $tanggal_expired, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=kadaluarsa");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=kadaluarsa");
    }
    exit;
}

// 🔍 AMBIL DATA YANG AKAN DIEDIT
$data = $koneksi->query("
    SELECT * FROM barang_kadaluarsa WHERE id = $id
")->fetch_assoc();

if (!$data) {
    header("Location: index.php?msg=notfound&obj=kadaluarsa");
    exit;
}

// Ambil opsi barang
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
                <h5 class="card-title mb-0">Edit Barang Kadaluarsa</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>

            <form method="post">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = $barangResult->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>" <?= $data['id_barang'] == $b['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required value="<?= $data['jumlah'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" maxlength="100" value="<?= htmlspecialchars($data['lokasi']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Expired</label>
                        <input type="date" name="tanggal_expired" class="form-control" required value="<?= $data['tanggal_expired'] ?>">
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>