<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: index.php?msg=unauthorized&obj=tidaklaku");
    exit;
}

$id = $_GET['id'] ?? '';
if (!is_numeric($id) || $id === '') {
    header("Location: index.php?msg=invalid&obj=tidaklaku");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("
    SELECT p.*, b.nama_barang, b.satuan 
    FROM produk_tidak_laku p 
    JOIN barang b ON p.id_barang = b.id 
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=notfound&obj=tidaklaku");
    exit;
}

// Jika disubmit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $periode_awal  = trim($_POST['periode_awal'] ?? '');
    $periode_akhir = trim($_POST['periode_akhir'] ?? '');
    $jumlah        = trim($_POST['jumlah_terjual'] ?? '');
    $keterangan    = trim($_POST['keterangan'] ?? '');
    $status        = trim($_POST['status'] ?? '');

    if (
        $periode_awal === '' || $periode_akhir === '' || $keterangan === '' ||
        !in_array($status, ['diperiksa', 'tindaklanjut', 'selesai'])
    ) {
        header("Location: edit.php?id=$id&msg=kosong&obj=tidaklaku");
        exit;
    }

    if (!is_numeric($jumlah) || $jumlah < 0) $jumlah = 0;

    // Validasi duplikat periode
    $cek = $koneksi->prepare("
        SELECT id FROM produk_tidak_laku 
        WHERE id_barang = ? AND periode_awal = ? AND periode_akhir = ? AND id != ?
    ");
    $cek->bind_param("issi", $data['id_barang'], $periode_awal, $periode_akhir, $id);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {
        header("Location: edit.php?id=$id&msg=duplicate&obj=tidaklaku");
        exit;
    }

    // Update data
    $stmt = $koneksi->prepare("
        UPDATE produk_tidak_laku 
        SET periode_awal = ?, periode_akhir = ?, jumlah_terjual = ?, keterangan = ?, status = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssissi", $periode_awal, $periode_akhir, $jumlah, $keterangan, $status, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=tidaklaku");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=tidaklaku");
    }
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
                <h5 class="card-title mb-0">Edit Produk Tidak Laku</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>

            <form method="post" class="card-body">
                <div class="mb-3">
                    <label class="form-label">Barang</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_barang']) ?> (<?= $data['satuan'] ?>)" readonly>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Periode Awal</label>
                        <input type="date" name="periode_awal" class="form-control" required value="<?= $data['periode_awal'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Periode Akhir</label>
                        <input type="date" name="periode_akhir" class="form-control" required value="<?= $data['periode_akhir'] ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Terjual</label>
                    <input type="number" name="jumlah_terjual" class="form-control" value="<?= $data['jumlah_terjual'] ?>" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" required><?= htmlspecialchars($data['keterangan']) ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="diperiksa" <?= $data['status'] === 'diperiksa' ? 'selected' : '' ?>>Diperiksa</option>
                        <option value="tindaklanjut" <?= $data['status'] === 'tindaklanjut' ? 'selected' : '' ?>>Tindak Lanjut</option>
                        <option value="selesai" <?= $data['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>