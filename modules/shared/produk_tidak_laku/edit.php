<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// === HANDLE SIMPAN ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id              = intval($_POST['id']);
    $id_barang       = intval($_POST['id_barang']);
    $periode_awal    = $_POST['periode_awal'] ?? '';
    $periode_akhir   = $_POST['periode_akhir'] ?? '';
    $jumlah_terjual  = intval($_POST['jumlah_terjual'] ?? 0);
    $status          = $_POST['status'] ?? 'diperiksa';
    $keterangan      = trim($_POST['keterangan'] ?? '');

    // Validasi minimal
    if ($id && $id_barang && $periode_awal && $periode_akhir) {
        $stmt = $koneksi->prepare("
            UPDATE produk_tidak_laku
            SET id_barang = ?, periode_awal = ?, periode_akhir = ?, jumlah_terjual = ?, status = ?, keterangan = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ississi", $id_barang, $periode_awal, $periode_akhir, $jumlah_terjual, $status, $keterangan, $id);
        $stmt->execute();

        header("Location: index.php?msg=updated&obj=tidaklaku");
        exit;
    } else {
        header("Location: index.php?msg=invalid&obj=tidaklaku");
        exit;
    }
}

// === TAMPILKAN FORM ===
$id = intval($_GET['id'] ?? 0);
$data = $koneksi->query("SELECT * FROM produk_tidak_laku WHERE id = $id")->fetch_assoc();
if (!$data) die("Data tidak ditemukan.");

$barang = $koneksi->query("SELECT id, nama_barang FROM barang ORDER BY nama_barang ASC");

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5 shadow-sm">
            <div class="card-header">
                <div class="card-title">Edit Evaluasi Produk Tidak Laku</div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <div class="mb-3">
                        <label>Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <option value="" hidden>-- Pilih Barang --</option>
                            <?php while ($b = $barang->fetch_assoc()) : ?>
                                <option value="<?= $b['id'] ?>" <?= $b['id'] == $data['id_barang'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['nama_barang']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3 row">
                        <div class="col">
                            <label>Periode Awal</label>
                            <input type="date" name="periode_awal" class="form-control" value="<?= $data['periode_awal'] ?>" required>
                        </div>
                        <div class="col">
                            <label>Periode Akhir</label>
                            <input type="date" name="periode_akhir" class="form-control" value="<?= $data['periode_akhir'] ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Terjual</label>
                        <input type="number" name="jumlah_terjual" class="form-control" value="<?= $data['jumlah_terjual'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="diperiksa" <?= $data['status'] === 'diperiksa' ? 'selected' : '' ?>>Diperiksa</option>
                            <option value="tindaklanjut" <?= $data['status'] === 'tindaklanjut' ? 'selected' : '' ?>>Tindaklanjut</option>
                            <option value="selesai" <?= $data['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($data['keterangan']) ?></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan Perubahan</button>
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>