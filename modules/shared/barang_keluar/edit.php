<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

$id = $_GET['id'] ?? null;
$submit = $_POST['submit'] ?? null;

// === HANDLE UPDATE ===
if ($submit) {
    $id_barang  = trim($_POST['id_barang'] ?? '');
    $jumlah     = trim($_POST['jumlah'] ?? '');
    $tanggal    = trim($_POST['tanggal'] ?? '');
    $jenis      = trim($_POST['jenis'] ?? '');
    $tujuan     = trim($_POST['tujuan'] ?? null);
    $keterangan = trim($_POST['keterangan'] ?? null);

    $allowedJenis = ['internal', 'rusak', 'hilang', 'retur_supplier'];

    if (
        !$id || !$id_barang || !$jumlah || !$tanggal || !$jenis ||
        !is_numeric($jumlah) || $jumlah <= 0 || !in_array($jenis, $allowedJenis)
    ) {
        header("Location: edit.php?id=$id&msg=invalid&obj=barang_keluar");
        exit;
    }

    $stmt = $koneksi->prepare("UPDATE barang_keluar SET 
        id_barang = ?, tanggal = ?, jumlah = ?, jenis = ?, tujuan = ?, keterangan = ?
        WHERE id = ?");
    $stmt->bind_param("isssssi", $id_barang, $tanggal, $jumlah, $jenis, $tujuan, $keterangan, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=barang_keluar");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=barang_keluar");
    }
    exit;
}

// === TAMPILKAN FORM ===
if (!$id || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM barang_keluar WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

$barang = $koneksi->query("SELECT id, nama_barang FROM barang ORDER BY nama_barang ASC");

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Edit Data Barang Keluar</div>
            </div>

            <form method="post" action="" class="card-body">
                <input type="hidden" name="submit" value="1">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_barang" class="form-label">Nama Barang</label>
                        <select name="id_barang" id="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = $barang->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>" <?= $data['id_barang'] == $b['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['nama_barang']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" value="<?= $data['jumlah'] ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="jenis" class="form-label">Jenis Barang Keluar</label>
                        <select name="jenis" id="jenis" class="form-select" required>
                            <?php
                            $jenisList = ['internal', 'rusak', 'hilang', 'retur_supplier'];
                            foreach ($jenisList as $j):
                            ?>
                                <option value="<?= $j ?>" <?= $data['jenis'] === $j ? 'selected' : '' ?>>
                                    <?= ucwords(str_replace('_', ' ', $j)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="tujuan" class="form-label">Tujuan (Opsional)</label>
                        <input type="text" name="tujuan" id="tujuan" class="form-control" value="<?= htmlspecialchars($data['tujuan'] ?? '') ?>">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>