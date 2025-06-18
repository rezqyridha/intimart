<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
if (!in_array($role, ['admin', 'manajer', 'karyawan'])) {
    header("Location: ../../unauthorized.php");
    exit;
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? '';

// Ambil data pengiriman
$query = "
    SELECT p.*, 
        GROUP_CONCAT(CONCAT(b.nama_barang, ' (', pd.jumlah, ' ', b.satuan, ')') SEPARATOR ', ') AS detail_barang
    FROM pengiriman p
    JOIN pengiriman_detail pd ON p.id = pd.id_pengiriman
    JOIN barang b ON pd.id_barang = b.id
    WHERE p.tanggal_kirim BETWEEN '$dari' AND '$sampai'
";

if ($statusFilter !== '') {
    $query .= " AND p.status_pengiriman = '$statusFilter'";
}

$query .= " GROUP BY p.id ORDER BY p.tanggal_kirim DESC";
$result = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <h3 class="mt-4 mb-3">🚚 Laporan Pengiriman Barang</h3>

        <form method="GET" class="row g-2 align-items-end mb-4">
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
            </div>
            <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="diproses" <?= $statusFilter === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="dikirim" <?= $statusFilter === 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                    <option value="diterima" <?= $statusFilter === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="fa fa-search me-1"></i> Tampilkan
                </button>
                <button type="submit" formaction="cetak_pengiriman.php" formtarget="_blank" class="btn btn-danger w-50">
                    <i class="fa fa-print me-1"></i> Cetak PDF
                </button>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Kirim</th>
                            <th>Barang (Jumlah Satuan)</th>
                            <th>Tujuan</th>
                            <th>Estimasi Tiba</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $found = false; ?>
                        <?php while ($row = $result->fetch_assoc()):
                            $found = true;
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal_kirim'])) ?></td>
                                <td><?= htmlspecialchars($row['detail_barang']) ?></td>
                                <td><?= htmlspecialchars($row['tujuan']) ?></td>
                                <td><?= $row['estimasi_tiba'] ? date('d-m-Y', strtotime($row['estimasi_tiba'])) : '-' ?></td>
                                <td>
                                    <span class="badge bg-<?= match ($row['status_pengiriman']) {
                                                                'diproses' => 'secondary',
                                                                'dikirim' => 'warning',
                                                                'diterima' => 'success',
                                                                default => 'light'
                                                            } ?>">
                                        <?= ucfirst($row['status_pengiriman']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if (!$found): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data pengiriman ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>