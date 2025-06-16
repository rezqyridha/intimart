<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$error = "";

// Ambil data yang akan diedit
$stmt = $koneksi->prepare("SELECT * FROM kas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

// Simpan perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis      = $_POST['jenis'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $jumlah     = (int) ($_POST['jumlah'] ?? 0);
    $tanggal    = $_POST['tanggal'] ?? date('Y-m-d');

    if ($jenis && $jumlah > 0 && $keterangan) {
        $stmt = $koneksi->prepare("UPDATE kas SET jenis=?, keterangan=?, jumlah=?, tanggal=? WHERE id=?");
        $stmt->bind_param("ssisi", $jenis, $keterangan, $jumlah, $tanggal, $id);

        if ($stmt->execute()) {
            header("Location: index.php?msg=updated&obj=kas");
            exit;
        } else {
            header("Location: index.php?msg=failed&obj=kas");
            exit;
        }
    } else {
        header("Location: index.php?msg=kosong&obj=kas");
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
                <div class="card-title mb-0">Edit Transaksi Keuangan</div>
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
                            <option value="masuk" <?= $data['jenis'] === 'masuk' ? 'selected' : '' ?>>Masuk</option>
                            <option value="keluar" <?= $data['jenis'] === 'keluar' ? 'selected' : '' ?>>Keluar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control" required value="<?= htmlspecialchars($data['keterangan']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required value="<?= $data['jumlah'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= $data['tanggal'] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save"></i> Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>