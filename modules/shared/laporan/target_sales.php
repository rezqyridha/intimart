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
$bulan = $_GET['bulan'] ?? '';
$salesFilter = $_GET['sales'] ?? '';

// Query sales untuk dropdown (admin/manajer)
$salesList = [];
if (in_array($role, ['admin', 'manajer'])) {
    $salesList = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales'");
}

// Query utama laporan
$query = "
    SELECT 
        ts.id, ts.id_sales, ts.bulan, ts.target,
        u.nama_lengkap,
        (
            SELECT IFNULL(SUM(p.harga_total), 0)
            FROM penjualan p
            WHERE p.id_sales = ts.id_sales
              AND DATE_FORMAT(p.tanggal, '%Y-%m') = ts.bulan
        ) AS realisasi
    FROM target_sales ts
    JOIN user u ON ts.id_sales = u.id
    WHERE 1=1
";


if ($bulan !== '') {
    $query .= " AND ts.bulan = '$bulan'";
}

if ($role === 'sales') {
    $query .= " AND ts.id_sales = $id_user";
} elseif ($salesFilter !== '') {
    $query .= " AND ts.id_sales = " . intval($salesFilter);
}

$query .= " ORDER BY ts.bulan DESC, u.nama_lengkap";

$data = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <h3 class="mt-4 mb-3">📊 Laporan Target vs Realisasi Sales</h3>

        <form method="GET" class="row g-2 align-items-end mb-4">
            <div class="col-md-3">
                <label>Bulan</label>
                <input type="month" name="bulan" class="form-control" value="<?= $bulan ?>">
            </div>

            <?php if (in_array($role, ['admin', 'manajer'])): ?>
                <div class="col-md-3">
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
            <?php endif; ?>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="fa fa-search me-1"></i> Tampilkan
                </button>
                <button type="submit" formaction="cetak_target_sales.php" formtarget="_blank" class="btn btn-danger w-50">
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
                            <th>Nama Sales</th>
                            <th>Bulan</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>% Pencapaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $found = false; ?>
                        <?php while ($row = $data->fetch_assoc()):
                            $found = true;
                            $persen = $row['target'] > 0 ? round($row['realisasi'] / $row['target'] * 100, 2) : 0;
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= date('M Y', strtotime($row['bulan'] . '-01')) ?></td>
                                <td>Rp <?= number_format($row['target'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['realisasi'], 0, ',', '.') ?></td>
                                <td><?= $persen ?>%</td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if (!$found): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data.</td>
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