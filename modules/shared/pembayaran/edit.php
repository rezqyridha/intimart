<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=pembayaran");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=pembayaran");
    exit;
}

// Ambil data lama pembayaran
$stmt = $koneksi->prepare("SELECT * FROM pembayaran WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=pembayaran");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penjualan  = (int)($_POST['id_penjualan'] ?? 0);
    $jumlah_bayar  = (float)($_POST['jumlah_bayar'] ?? 0);
    $tanggal       = $_POST['tanggal'] ?? '';
    $keterangan    = trim($_POST['keterangan'] ?? '');

    if ($id_penjualan <= 0 || $jumlah_bayar <= 0 || empty($tanggal)) {
        header("Location: edit.php?id=$id&msg=kosong&obj=pembayaran");
        exit;
    }

    // Ambil total dari penjualan
    $cek = $koneksi->prepare("SELECT harga_total FROM penjualan WHERE id = ?");
    $cek->bind_param("i", $id_penjualan);
    $cek->execute();
    $cek->bind_result($harga_total);
    $cek->fetch();
    $cek->close();

    if (!$harga_total) {
        header("Location: edit.php?id=$id&msg=invalid&obj=pembayaran");
        exit;
    }

    // Tentukan status pelunasan
    $status = ($jumlah_bayar >= $harga_total) ? 'lunas' : 'belum lunas';

    // Update data pembayaran
    $stmt = $koneksi->prepare("UPDATE pembayaran SET id_penjualan=?, jumlah_bayar=?, tanggal=?, keterangan=? WHERE id=?");
    $stmt->bind_param("idssi", $id_penjualan, $jumlah_bayar, $tanggal, $keterangan, $id);

    if ($stmt->execute()) {
        // Update status pelunasan di penjualan
        $stmt2 = $koneksi->prepare("UPDATE penjualan SET status_pelunasan=? WHERE id=?");
        $stmt2->bind_param("si", $status, $id_penjualan);
        $stmt2->execute();
        $stmt2->close();

        header("Location: index.php?msg=updated&obj=pembayaran");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=pembayaran");
    }
    $stmt->close();
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
                <div class="card-title mb-0">Edit Data Pembayaran</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="id_penjualan" class="form-label">Transaksi Penjualan</label>
                        <select name="id_penjualan" id="id_penjualan" class="form-select" required>
                            <option value="">-- Pilih Transaksi --</option>
                            <?php
                            $penjualan = $koneksi->query("
                                SELECT p.id, b.nama_barang, b.satuan, p.tanggal, p.harga_total
                                FROM penjualan p
                                JOIN barang b ON p.id_barang = b.id
                                ORDER BY p.tanggal DESC
                            ");
                            while ($row = $penjualan->fetch_assoc()):
                                $selected = ($row['id'] == $data['id_penjualan']) ? 'selected' : '';
                            ?>
                                <option value="<?= $row['id'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>) - <?= date('d-m-Y', strtotime($row['tanggal'])) ?> - Rp <?= number_format($row['harga_total'], 0, ',', '.') ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_bayar" class="form-label">Jumlah Pembayaran</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" required value="<?= $data['jumlah_bayar'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Pembayaran</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= $data['tanggal'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"><?= htmlspecialchars($data['keterangan']) ?></textarea>
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