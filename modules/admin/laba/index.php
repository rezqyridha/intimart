<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

//  Validasi admin-only
if ($_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}


// Proses generate otomatis jika diminta
if (isset($_GET['generate']) && $_GET['generate'] === '1') {
    $queryPeriode = "SELECT DATE_FORMAT(tanggal, '%Y-%m') AS periode FROM kas GROUP BY periode";
    $periodeResult = $koneksi->query($queryPeriode);

    while ($p = $periodeResult->fetch_assoc()) {
        $periode = $p['periode'];
        $cek = $koneksi->query("SELECT 1 FROM laba WHERE periode = '$periode'");
        if ($cek->num_rows === 0) {
            $q = $koneksi->query("
                SELECT 
                    SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) AS total_masuk,
                    SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) AS total_keluar
                FROM kas
                WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$periode'
            ");
            $res = $q->fetch_assoc();
            $pendapatan = $res['total_masuk'] ?? 0;
            $pengeluaran = $res['total_keluar'] ?? 0;
            $laba = $pendapatan - $pengeluaran;

            $koneksi->query("
                INSERT INTO laba (periode, total_pendapatan, total_pengeluaran, laba_bersih)
                VALUES ('$periode', '$pendapatan', '$pengeluaran', '$laba')
            ");
        }
    }
    header("Location: index.php?success=1");
    exit;
}

//  Ambil daftar tahun unik dari laba
$tahunList = [];
$tahunQ = $koneksi->query("SELECT DISTINCT LEFT(periode, 4) AS tahun FROM laba ORDER BY tahun DESC");
while ($row = $tahunQ->fetch_assoc()) {
    $tahunList[] = $row['tahun'];
}
$tahunDipilih = $_GET['tahun'] ?? date('Y');

//  Ambil data laba berdasarkan tahun yang dipilih
$query = "SELECT * FROM laba WHERE LEFT(periode, 4) = '$tahunDipilih' ORDER BY periode ASC";
$result = $koneksi->query($query);

$dataLaba = [];
$labelBulan = [];
$pendapatan = [];
$pengeluaran = [];
$labaBersih = [];

while ($row = $result->fetch_assoc()) {
    $dataLaba[] = $row;
    $labelBulan[] = $row['periode'];
    $pendapatan[] = (float) $row['total_pendapatan'];
    $pengeluaran[] = (float) $row['total_pengeluaran'];
    $labaBersih[] = (float) $row['laba_bersih'];
}

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';

?>

<div class="main-content app-content">
    <div class="container-fluid">

        <h3 class="mt-4 mb-4 d-flex justify-content-between">
            <span>📈 Laporan Laba Tahun <?= $tahunDipilih ?></span>
            <div>
                <form method="GET" class="d-inline">
                    <select name="tahun" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                        <?php foreach ($tahunList as $tahun): ?>
                            <option value="<?= $tahun ?>" <?= $tahun == $tahunDipilih ? 'selected' : '' ?>><?= $tahun ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="?generate=1" class="btn btn-success btn-sm ms-2">
                    🔄 Generate Otomatis
                </a>
            </div>
        </h3>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">✅ Laba berhasil digenerate dari data kas.</div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <canvas id="chartLaba"></canvas>
            </div>
        </div>

        <div class="card custom-card shadow-sm">
            <div class="card-header">
                <div class="card-title">Tabel Data Laba</div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Periode</th>
                            <th>Total Pendapatan</th>
                            <th>Total Pengeluaran</th>
                            <th>Laba Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($dataLaba as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['periode'] ?></td>
                                <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['total_pengeluaran'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['laba_bersih'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartLaba').getContext('2d');
    const chartLaba = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelBulan) ?>,
            datasets: [{
                    label: 'Pendapatan',
                    backgroundColor: '#198754',
                    data: <?= json_encode($pendapatan) ?>
                },
                {
                    label: 'Pengeluaran',
                    backgroundColor: '#dc3545',
                    data: <?= json_encode($pengeluaran) ?>
                },
                {
                    label: 'Laba Bersih',
                    backgroundColor: '#0d6efd',
                    data: <?= json_encode($labaBersih) ?>
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Grafik Laba Bulanan - Tahun <?= $tahunDipilih ?>'
                }
            }
        }
    });
</script>