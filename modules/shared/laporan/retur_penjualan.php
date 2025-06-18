<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];

if (!in_array($role, ['admin', 'manajer', 'sales'])) {
    header("Location: ../../unauthorized.php");
    exit;
}

// Filter
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$id_sales = $_GET['sales'] ?? '';

$salesList = [];
if ($role !== 'sales') {
    $salesList = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales'");
}

// Query retur
$query = "
    SELECT r.*, p.id_sales, p.id_barang, u.nama_lengkap AS nama_sales, 
           b.nama_barang, b.satuan
    FROM retur_penjualan r
    JOIN penjualan p ON r.id_penjualan = p.id
    JOIN user u ON p.id_sales = u.id
    JOIN barang b ON p.id_barang = b.id
    WHERE r.tanggal BETWEEN '$dari' AND '$sampai'
";

if ($role === 'sales') {
    $query .= " AND p.id_sales = $id_user";
} elseif ($id_sales !== '') {
    $query .= " AND p.id_sales = " . intval($id_sales);
}

$query .= " ORDER BY r.tanggal DESC";
$data = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <h3 class="mt-4 mb-3">↩️ Laporan Retur Penjualan</h3>

        <form method="GET" class="row g-2 align-items-end mb-4">
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
            </div>

            <?php if ($role !== 'sales'): ?>
                <div class="col-md-3">
                    <label>Sales</label>
                    <select name="sales" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($salesList as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $id_sales == $s['id'] ? 'selected' : '' ?>>
                                <?= $s['nama_lengkap'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="fa fa-search me-1"></i> Tampilkan
                </button>
                <button type="submit" formaction="cetak_retur_penjualan.php" formtarget="_blank" class="btn btn-danger w-50">
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
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $found = false; ?>
                        <?php while ($row = $data->fetch_assoc()):
                            $found = true;
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</td>
                                <td><?= htmlspecialchars($row['nama_sales']) ?></td>
                                <td><?= $row['jumlah'] ?></td>
                                <td><?= htmlspecialchars($row['alasan']) ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if (!$found): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data retur ditemukan.</td>
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