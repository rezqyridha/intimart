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

// Query piutang otomatis dari penjualan
$query = "
    SELECT p.id, p.tanggal, u.nama_lengkap AS nama_sales, b.nama_barang, b.satuan,
           p.harga_total, 
           COALESCE((SELECT SUM(jumlah_bayar) FROM pembayaran WHERE id_penjualan = p.id), 0) AS total_bayar,
           (p.harga_total - COALESCE((SELECT SUM(jumlah_bayar) FROM pembayaran WHERE id_penjualan = p.id), 0)) AS sisa
    FROM penjualan p
    JOIN barang b ON p.id_barang = b.id
    JOIN user u ON p.id_sales = u.id
    WHERE p.status_pelunasan != 'lunas' 
    AND p.tanggal BETWEEN '$dari' AND '$sampai'
";

if ($id_sales !== '') {
    $query .= " AND p.id_sales = " . intval($id_sales);
}

$query .= " ORDER BY p.tanggal DESC";
$data = $koneksi->query($query);

// List sales untuk filter
$salesList = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales'");

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <h3 class="mt-4 mb-3">📄 Laporan Piutang</h3>

        <form method="GET" action="" class="row g-3 align-items-end mb-4">
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
                            <?= $s['nama_lengkap'] ?>
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
                            <th>Total</th>
                            <th>Terbayar</th>
                            <th>Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $totalPiutang = 0; ?>
                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</td>
                                <td><?= htmlspecialchars($row['nama_sales']) ?></td>
                                <td>Rp <?= number_format($row['harga_total'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['sisa'], 0, ',', '.') ?></td>
                            </tr>
                            <?php $totalPiutang += $row['sisa']; ?>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-light fw-bold">
                            <td colspan="6" class="text-end">Total Piutang</td>
                            <td>Rp <?= number_format($totalPiutang, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>