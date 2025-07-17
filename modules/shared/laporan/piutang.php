<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: ../../unauthorized.php");
    exit;
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$id_sales = $_GET['sales'] ?? '';

// Ambil data piutang manual
$query = "
    SELECT p.*, u.nama_lengkap AS nama_sales, b.nama_barang, b.satuan
    FROM piutang p
    JOIN user u ON p.id_sales = u.id
    LEFT JOIN penjualan pj ON p.id_penjualan = pj.id
    LEFT JOIN barang b ON pj.id_barang = b.id
    WHERE p.tanggal BETWEEN ? AND ?
";

$params = [$dari, $sampai];
$types = 'ss';

if (!empty($id_sales)) {
    $query .= " AND p.id_sales = ?";
    $types .= 'i';
    $params[] = $id_sales;
}

$query .= " ORDER BY p.tanggal DESC";
$stmt = $koneksi->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$data = $stmt->get_result();

// Sales list
$salesList = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales'");

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <h3 class="mt-4 mb-3">📄 Laporan Piutang</h3>

        <form method="GET" class="row g-3 align-items-end mb-4">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sales</label>
                <select name="sales" class="form-select">
                    <option value="">Semua</option>
                    <?php foreach ($salesList as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $id_sales == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nama_lengkap']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50"><i class="fa fa-search"></i> Tampilkan</button>
                <button type="submit" class="btn btn-danger w-50" name="cetak" formaction="cetak_piutang.php" formtarget="_blank">
                    <i class="fa fa-print"></i> Cetak PDF
                </button>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Sales</th>
                            <th>Jumlah Piutang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $total = 0; ?>
                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang'] ?? '-') ?> (<?= $row['satuan'] ?? '-' ?>)</td>
                                <td><?= htmlspecialchars($row['nama_sales']) ?></td>
                                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['status'] === 'lunas' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php $total += $row['jumlah']; ?>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-light fw-bold">
                            <td colspan="4" class="text-end">Total Piutang</td>
                            <td colspan="2">Rp <?= number_format($total, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>