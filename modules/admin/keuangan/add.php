<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis      = $_POST['jenis'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $jumlah     = (int) ($_POST['jumlah'] ?? 0);
    $tanggal    = $_POST['tanggal'] ?? date('Y-m-d');
    $created_by = $_SESSION['id_user'] ?? 0;

    if ($jenis && $jumlah > 0 && $keterangan) {
        $stmt = $koneksi->prepare("INSERT INTO kas (jenis, keterangan, jumlah, tanggal, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $jenis, $keterangan, $jumlah, $tanggal, $created_by);

        if ($stmt->execute()) {
            header("Location: index.php?msg=added&obj=kas");
            exit;
        } else {
            header("Location: index.php?msg=failed&obj=kas");
            exit;
        }
    } else {
        header("Location: index.php?msg=failed&obj=kas");
        exit;
    }
}

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Tambah Transaksi Keuangan</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis Transaksi</label>
                        <select name="jenis" id="jenis" class="form-select" required>
                            <option value="" hidden>-- Pilih Jenis --</option>
                            <option value="masuk">Masuk</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>