<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=retur");
    exit;
}

// Handle POST (Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id'] ?? '';
    $jumlah   = trim($_POST['jumlah'] ?? '');
    $alasan   = trim($_POST['alasan'] ?? '');
    $tanggal  = trim($_POST['tanggal'] ?? '');

    if ($id === '' || $jumlah === '' || $alasan === '' || $tanggal === '') {
        header("Location: edit.php?id=$id&msg=kosong&obj=retur");
        exit;
    }

    if (!is_numeric($jumlah) || $jumlah <= 0) {
        header("Location: edit.php?id=$id&msg=invalid&obj=retur");
        exit;
    }

    // Ambil data penjualan & retur saat ini
    $q = $koneksi->prepare("SELECT r.id_penjualan, p.jumlah AS jml_penjualan FROM retur_penjualan r 
                            JOIN penjualan p ON r.id_penjualan = p.id 
                            WHERE r.id = ?");
    $q->bind_param("i", $id);
    $q->execute();
    $result = $q->get_result();
    $data = $result->fetch_assoc();
    $q->close();

    if (!$data) {
        header("Location: index.php?msg=invalid&obj=retur");
        exit;
    }

    $id_penjualan = $data['id_penjualan'];
    $jml_penjualan = $data['jml_penjualan'];

    // Validasi: jumlah retur tidak boleh melebihi jumlah penjualan
    if ($jumlah > $jml_penjualan) {
        header("Location: edit.php?id=$id&msg=melebihi&obj=retur&maks=$jml_penjualan");
        exit;
    }

    // Update retur
    $stmt = $koneksi->prepare("UPDATE retur_penjualan SET jumlah = ?, alasan = ?, tanggal = ? WHERE id = ?");
    $stmt->bind_param("issi", $jumlah, $alasan, $tanggal, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=retur");
    } else {
        header("Location: index.php?msg=failed&obj=retur");
    }
    exit;
}

// Ambil data untuk form edit
$id = $_GET['id'] ?? '';
if ($id === '' || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=retur");
    exit;
}

$q = $koneksi->prepare("SELECT r.*, b.nama_barang, b.satuan, p.tanggal AS tgl_jual
                        FROM retur_penjualan r
                        JOIN penjualan p ON r.id_penjualan = p.id
                        JOIN barang b ON p.id_barang = b.id
                        WHERE r.id = ?");
$q->bind_param("i", $id);
$q->execute();
$result = $q->get_result();
$data = $result->fetch_assoc();
$q->close();

if (!$data) {
    header("Location: index.php?msg=notfound&obj=retur");
    exit;
}

// Layout
require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Retur Penjualan</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>

            <form method="post" action="edit.php">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Barang & Tanggal Penjualan</label>
                        <input type="text" class="form-control bg-light" readonly
                            value="<?= htmlspecialchars($data['nama_barang']) ?> (<?= $data['satuan'] ?>) - <?= date('d-m-Y', strtotime($data['tgl_jual'])) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Retur</label>
                        <input type="number" name="jumlah" class="form-control" value="<?= $data['jumlah'] ?>" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan Retur</label>
                        <textarea name="alasan" class="form-control" required><?= htmlspecialchars($data['alasan']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Retur</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
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