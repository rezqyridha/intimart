<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer', 'karyawan'])) {
    header("Location: ../../unauthorized.php");
    exit;
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? '';

// Query analisis performa penjualan per barang
$query = "
    SELECT 
        b.id, b.nama_barang, b.satuan,
        COALESCE(SUM(p.jumlah), 0) AS total_terjual,
        MAX(p.tanggal) AS terakhir_terjual
    FROM barang b
    LEFT JOIN penjualan p ON b.id = p.id_barang AND p.tanggal BETWEEN '$dari' AND '$sampai'
    GROUP BY b.id
    ORDER BY b.nama_barang ASC
";
$result = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <h3 class="mt-4 mb-1">📊 Analisis Produk Tidak Laku</h3>
        <p class="text-muted mb-4">Laporan ini menunjukkan status kelakuan produk berdasarkan penjualan selama periode tertentu.</p>

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
                    <option value="laku" <?= $statusFilter === 'laku' ? 'selected' : '' ?>>✅ Laku</option>
                    <option value="tidak" <?= $statusFilter === 'tidak' ? 'selected' : '' ?>>❌ Tidak Laku</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="fa fa-search me-1"></i> Tampilkan
                </button>
                <button type="submit" formaction="cetak_produk_tidak_laku.php" formtarget="_blank" class="btn btn-danger w-50">
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
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Total Terjual</th>
                            <th>Terakhir Terjual</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $found = false;
                        while ($row = $result->fetch_assoc()):
                            $isLaku = $row['total_terjual'] > 0;

                            if ($statusFilter === 'laku' && !$isLaku) continue;
                            if ($statusFilter === 'tidak' && $isLaku) continue;

                            $found = true;
                            $tgl = $row['terakhir_terjual'] ? date('d-m-Y', strtotime($row['terakhir_terjual'])) : '-';
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= $row['satuan'] ?></td>
                                <td><?= $row['total_terjual'] ?></td>
                                <td><?= $tgl ?></td>
                                <td>
                                    <span class="badge bg-<?= $isLaku ? 'success' : 'danger' ?>">
                                        <?= $isLaku ? 'Laku' : 'Tidak Laku' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if (!$found): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data sesuai filter.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <p class="text-muted small mt-3">
                    Keterangan: Barang dengan total penjualan <strong>0</strong> selama periode dipilih dikategorikan sebagai
                    <span class="badge bg-danger">Tidak Laku</span>.
                </p>
            </div>
        </div>

    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>