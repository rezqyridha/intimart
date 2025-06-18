<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../unauthorized.php");
    exit;
}

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$jenis = $_GET['jenis'] ?? '';

$where = "WHERE tanggal BETWEEN '$dari' AND '$sampai'";
if ($jenis !== '') {
    $where .= " AND jenis = '$jenis'";
}

$query = "SELECT * FROM kas $where ORDER BY tanggal DESC";
$data = $koneksi->query($query);

$total_masuk = $koneksi->query("SELECT SUM(jumlah) FROM kas WHERE jenis='masuk' AND tanggal BETWEEN '$dari' AND '$sampai'")->fetch_row()[0] ?? 0;
$total_keluar = $koneksi->query("SELECT SUM(jumlah) FROM kas WHERE jenis='keluar' AND tanggal BETWEEN '$dari' AND '$sampai'")->fetch_row()[0] ?? 0;
$saldo = $total_masuk - $total_keluar;

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <h3 class="mt-4 mb-3">💰 Laporan Keuangan (Kas)</h3>

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
                <label class="form-label">Jenis</label>
                <select name="jenis" class="form-select">
                    <option value="">Semua</option>
                    <option value="masuk" <?= $jenis === 'masuk' ? 'selected' : '' ?>>Masuk</option>
                    <option value="keluar" <?= $jenis === 'keluar' ? 'selected' : '' ?>>Keluar</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary w-50"><i class="fa fa-search"></i> Tampilkan</button>
                <button type="submit" class="btn btn-danger w-50" formaction="cetak_kas.php" formtarget="_blank">
                    <i class="fa fa-print"></i> Cetak PDF
                </button>
            </div>
        </form>

        <div class="alert alert-info">
            Total Masuk: <strong>Rp <?= number_format($total_masuk, 0, ',', '.') ?></strong> |
            Total Keluar: <strong>Rp <?= number_format($total_keluar, 0, ',', '.') ?></strong> |
            Saldo: <strong>Rp <?= number_format($saldo, 0, ',', '.') ?></strong>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><span class="badge bg-<?= $row['jenis'] === 'masuk' ? 'success' : 'danger' ?>"><?= ucfirst($row['jenis']) ?></span></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
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