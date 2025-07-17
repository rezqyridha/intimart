<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../unauthorized.php");
    exit;
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$tanggal = $_GET['tanggal'] ?? '';
$status = $_GET['status'] ?? '';
$metode = $_GET['metode'] ?? '';

$where = [];

// Tanggal filter
if (!empty($tanggal)) {
    $where[] = "DATE(rp.tanggal_rekonsiliasi) = '$tanggal'";
} elseif (!empty($dari) && !empty($sampai)) {
    $where[] = "DATE(rp.tanggal_rekonsiliasi) >= '$dari' AND DATE(rp.tanggal_rekonsiliasi) <= '$sampai'";
}

// Filter status
if ($status !== '' && $status !== 'Semua') {
    $where[] = "rp.status = '$status'";
}

// Filter metode
if ($metode !== '' && $metode !== 'Semua') {
    $where[] = "p.metode = '$metode'";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "
    SELECT rp.*, b.nama_barang, b.satuan, p.tanggal, p.metode, p.jumlah_bayar
    FROM rekonsiliasi_pembayaran rp
    JOIN pembayaran p ON rp.id_pembayaran = p.id
    JOIN penjualan j ON p.id_penjualan = j.id
    JOIN barang b ON j.id_barang = b.id
    $whereClause
    ORDER BY rp.tanggal_rekonsiliasi DESC
";

$result = $koneksi->query($query);



require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <h3 class="mt-4 mb-3">📋 Laporan Rekonsiliasi Pembayaran</h3>

        <form method="GET" class="row g-3 align-items-end mb-4">
            <div class="col-md-2">
                <label class="form-label">Dari</label>
                <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="sesuai" <?= $status === 'sesuai' ? 'selected' : '' ?>>Sesuai</option>
                    <option value="tidak sesuai" <?= $status === 'tidak sesuai' ? 'selected' : '' ?>>Tidak Sesuai</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Metode</label>
                <select name="metode" class="form-select">
                    <option value="">Semua</option>
                    <option value="tunai" <?= $metode === 'tunai' ? 'selected' : '' ?>>Tunai</option>
                    <option value="transfer" <?= $metode === 'transfer' ? 'selected' : '' ?>>Transfer</option>
                    <option value="qris" <?= $metode === 'qris' ? 'selected' : '' ?>>QRIS</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50"><i class="fa fa-search me-1"></i> Tampilkan</button>
                <button type="submit" class="btn btn-danger w-50" formaction="cetak_rekonsiliasi_pembayaran.php" formtarget="_blank">
                    <i class="fa fa-print me-1"></i> Cetak PDF
                </button>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Tgl Bayar</th>
                            <th>Tgl Rekon</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</td>
                                <td><?= ucfirst($row['metode']) ?></td>
                                <td>Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal_rekonsiliasi'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['status'] === 'sesuai' ? 'success' : 'secondary' ?>">
                                        <?= ucwords(str_replace('_', ' ', $row['status'])) ?>
                                    </span>
                                </td>
                                <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>