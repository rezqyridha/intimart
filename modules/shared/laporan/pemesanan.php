<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
$idUser = $_SESSION['id_user'] ?? 0;

if (!in_array($role, ['admin', 'manajer', 'sales'])) {
    header("Location: ../../unauthorized.php");
    exit;
}

// Filter
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? '';
$salesFilter = $_GET['sales'] ?? '';

// Ambil data sales untuk filter dropdown
$salesList = [];
if ($role !== 'sales') {
    $salesList = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales'");
}

// Query pemesanan
$query = "
    SELECT p.*, b.nama_barang, b.satuan, u.nama_lengkap AS nama_sales
    FROM pemesanan p
    JOIN barang b ON p.id_barang = b.id
    JOIN user u ON p.id_sales = u.id
    WHERE DATE(p.tanggal_pemesanan) BETWEEN '$dari' AND '$sampai'
";

// Batasan role
if ($role === 'sales') {
    $query .= " AND p.id_sales = $idUser";
} elseif ($salesFilter !== '') {
    $query .= " AND p.id_sales = " . intval($salesFilter);
}

if ($statusFilter !== '') {
    $query .= " AND p.status = '$statusFilter'";
}

$query .= " ORDER BY p.tanggal_pemesanan DESC";
$data = $koneksi->query($query);

// View
require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <h3 class="mt-4 mb-3">📝 Laporan Pemesanan</h3>

        <form method="GET" class="row g-3 align-items-end mb-4">
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
            </div>
            <div class="col-md-2">
                <label>Sales</label>
                <select name="sales" class="form-select">
                    <option value="">Semua</option>
                    <?php foreach ($salesList as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $salesFilter == $s['id'] ? 'selected' : '' ?>>
                            <?= $s['nama_lengkap'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="menunggu" <?= $statusFilter === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="disetujui" <?= $statusFilter === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="ditolak" <?= $statusFilter === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="fa fa-search me-1"></i> Tampilkan
                </button>
                <button type="submit" formaction="cetak_pemesanan.php" formtarget="_blank" class="btn btn-danger w-50">
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
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Sales</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                            <th>Status</th>
                            <th>Tgl Direspon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($row['tanggal_pemesanan'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</td>
                                <td><?= $row['nama_sales'] ?></td>
                                <td><?= $row['jumlah'] ?></td>
                                <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= match ($row['status']) {
                                                                'menunggu' => 'warning',
                                                                'disetujui' => 'success',
                                                                'ditolak' => 'danger',
                                                                default => 'secondary'
                                                            } ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $row['tanggal_direspon'] ? date('d-m-Y H:i', strtotime($row['tanggal_direspon'])) : '-' ?>
                                </td>
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