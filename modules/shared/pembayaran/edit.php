<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
if ($role !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Validasi ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=pembayaran");
    exit;
}

// Ambil data pembayaran
$stmt = $koneksi->prepare("SELECT * FROM pembayaran WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=pembayaran");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penjualan = $_POST['id_penjualan'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $metode = $_POST['metode'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'] ?? null;

    // Cek pembayaran duplikat
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM pembayaran WHERE id_penjualan = ? AND id != ?");
    $cek->bind_param("ii", $id_penjualan, $id);
    $cek->execute();
    $cek->bind_result($total_duplikat);
    $cek->fetch();
    $cek->close();

    if ($total_duplikat > 0) {
        header("Location: index.php?msg=duplicate&obj=pembayaran");
        exit;
    }

    // Ambil total dari penjualan
    $q = $koneksi->prepare("SELECT harga_total FROM penjualan WHERE id = ?");
    $q->bind_param("i", $id_penjualan);
    $q->execute();
    $q->bind_result($harga_total);
    $q->fetch();
    $q->close();

    if ($jumlah_bayar > $harga_total) {
        header("Location: index.php?msg=invalid&obj=pembayaran");
        exit;
    }

    // Update data pembayaran
    $stmt = $koneksi->prepare("UPDATE pembayaran SET id_penjualan=?, jumlah_bayar=?, metode=?, tanggal=?, keterangan=? WHERE id=?");
    $stmt->bind_param("idsssi", $id_penjualan, $jumlah_bayar, $metode, $tanggal, $keterangan, $id);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        header("Location: index.php?msg=updated&obj=pembayaran");
    } else {
        header("Location: index.php?msg=failed&obj=pembayaran");
    }
    exit;
}

// Ambil data untuk dropdown
$penjualan = $koneksi->query("
    SELECT p.id, b.nama_barang, b.satuan, p.tanggal, p.harga_total
    FROM penjualan p
    JOIN barang b ON p.id_barang = b.id
    ORDER BY p.tanggal DESC
");
?>

<?php require_once LAYOUTS_PATH . '/head.php'; ?>
<?php require_once LAYOUTS_PATH . '/header.php'; ?>
<?php require_once LAYOUTS_PATH . '/topbar.php'; ?>
<?php require_once LAYOUTS_PATH . '/sidebar.php'; ?>

<div class="main-content app-content">
    <div class="container-fluid">

        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Edit Data Pembayaran</div>
            </div>

            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Transaksi Penjualan</label>
                        <select name="id_penjualan" class="form-select" required>
                            <option value="">-- Pilih Transaksi --</option>
                            <?php while ($row = $penjualan->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>" <?= $row['id'] == $data['id_penjualan'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>) - <?= date('d-m-Y', strtotime($row['tanggal'])) ?> - Rp <?= number_format($row['harga_total'], 0, ',', '.') ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="number" name="jumlah_bayar" class="form-control" value="<?= $data['jumlah_bayar'] ?>" min="100" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode</label>
                        <select name="metode" class="form-select" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="tunai" <?= $data['metode'] === 'tunai' ? 'selected' : '' ?>>Tunai</option>
                            <option value="transfer" <?= $data['metode'] === 'transfer' ? 'selected' : '' ?>>Transfer</option>
                            <option value="qris" <?= $data['metode'] === 'qris' ? 'selected' : '' ?>>QRIS</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"><?= $data['keterangan'] ?></textarea>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary"><i class="fe fe-arrow-left"></i> Kembali</a>
                        <button class="btn btn-primary"><i class="fe fe-save"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>