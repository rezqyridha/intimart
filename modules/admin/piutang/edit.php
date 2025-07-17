<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=piutang");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

// Ambil data piutang dan penjualan sekaligus
$stmt = $koneksi->prepare("
    SELECT p.*, pj.harga_total, pj.tanggal AS tgl_penjualan, b.nama_barang, u.nama_lengkap AS nama_sales
    FROM piutang p
    JOIN penjualan pj ON p.id_penjualan = pj.id
    JOIN barang b ON pj.id_barang = b.id
    JOIN user u ON pj.id_sales = u.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

// Jika status sudah lunas, kunci edit
if ($data['status'] === 'lunas') {
    header("Location: index.php?msg=locked&obj=piutang");
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = trim($_POST['tanggal'] ?? '');
    $jumlah  = trim($_POST['jumlah'] ?? '');
    $status  = trim($_POST['status'] ?? '');

    if (empty($tanggal) || empty($jumlah) || empty($status)) {
        header("Location: edit.php?id=$id&msg=kosong&obj=piutang");
        exit;
    }

    if (!is_numeric($jumlah) || $jumlah <= 0) {
        header("Location: edit.php?id=$id&msg=invalid&obj=piutang");
        exit;
    }

    // Validasi jumlah tidak lebih dari harga_total penjualan
    if ($jumlah > $data['harga_total']) {
        header("Location: edit.php?id=$id&msg=overlimit&obj=piutang");
        exit;
    }

    // Update
    $stmt = $koneksi->prepare("UPDATE piutang SET tanggal = ?, jumlah = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $tanggal, $jumlah, $status, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=piutang");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=piutang");
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
                <div class="card-title mb-0">Edit Data Piutang</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Penjualan</label>
                        <input type="text" class="form-control bg-light" readonly
                            value="[<?= $data['tgl_penjualan'] ?>] <?= $data['nama_barang'] ?> | Sales: <?= $data['nama_sales'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= $data['tanggal'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required min="1000" step="500" value="<?= $data['jumlah'] ?>">
                        <small class="text-muted">Maksimal: Rp <?= number_format($data['harga_total'], 0, ',', '.') ?></small>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="belum lunas" <?= $data['status'] === 'belum lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                            <option value="lunas" <?= $data['status'] === 'lunas' ? 'selected' : '' ?>>Lunas</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>