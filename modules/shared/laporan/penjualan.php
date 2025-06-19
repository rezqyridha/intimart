<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

//  Role akses multi-role
if (!in_array($_SESSION['role'], ['admin', 'manajer', 'sales'])) {
    header("Location: ../../unauthorized.php");
    exit;
}

//  Filter tanggal
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$id_sales = $_GET['sales'] ?? '';

//  Query penjualan berdasarkan peran
$query = "
SELECT p.*, b.nama_barang, u.nama_lengkap AS nama_sales
FROM penjualan p
JOIN barang b ON p.id_barang = b.id
JOIN user u ON u.id = p.id_sales
WHERE p.tanggal BETWEEN '$dari' AND '$sampai'
";

// Role sales: hanya tampilkan data miliknya
if ($_SESSION['role'] === 'sales') {
    $query .= " AND p.id_sales = " . $_SESSION['id_user'];
} elseif ($id_sales !== '') {
    $query .= " AND p.id_sales = " . intval($id_sales);
}

$query .= " ORDER BY p.tanggal DESC";
$data = $koneksi->query($query);

// Ambil daftar sales untuk dropdown filter (jika bukan sales)
$salesList = [];
if ($_SESSION['role'] !== 'sales') {
    $salesList = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales'");
}

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <h3 class="mt-4 mb-3">📦 Laporan Penjualan</h3>

        <form method="GET" action="" class="row g-3 align-items-end mb-4">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
            </div>

            <?php if ($_SESSION['role'] !== 'sales'): ?>
                <div class="col-md-3">
                    <label class="form-label">Sales</label>
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
                <button type="submit" class="btn btn-primary w-50" name="tampilkan">
                    <i class="fa fa-search"></i> Tampilkan
                </button>
                <button type="submit" class="btn btn-danger w-50" name="cetak" formaction="cetak_penjualan.php" formtarget="_blank">
                    <i class="fa fa-print"></i> Cetak PDF
                </button>
            </div>
        </form>



        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Sales</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $grand_total = 0; ?>
                        <?php while ($row = $data->fetch_assoc()): ?>
                            <?php $grand_total += $row['harga_total']; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['nama_sales']) ?></td>
                                <td><?= $row['jumlah'] ?></td>
                                <td>Rp <?= number_format($row['harga_total'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= strtolower($row['status_pelunasan']) === 'lunas' ? 'success' : 'warning' ?>">
                                        <?= $row['status_pelunasan'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-light fw-bold">
                            <td colspan="5" class="text-end">Total</td>
                            <td colspan="2">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>