<?php
require_once __DIR__ . '/../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: " . AUTH_PATH . "/login.php");
    exit;
}

$today = date('Y-m-d');
$todayDisplay = date('d-m-Y');
$tahunIni = date('Y');

// Statistik Ringkas
$totalUser   = $koneksi->query("SELECT COUNT(*) AS total FROM user")->fetch_assoc()['total'] ?? 0;
$totalBarang = $koneksi->query("SELECT COUNT(*) AS total FROM barang")->fetch_assoc()['total'] ?? 0;
$totalJual   = $koneksi->query("SELECT COUNT(*) AS total FROM penjualan WHERE DATE(tanggal) = '$today'")->fetch_assoc()['total'] ?? 0;
$totalMasuk  = $koneksi->query("SELECT COUNT(*) AS total FROM barang_masuk WHERE DATE(tanggal) = '$today'")->fetch_assoc()['total'] ?? 0;

// Grafik Penjualan
$penjualanBulanan = array_fill(1, 12, 0);
$result = $koneksi->query("SELECT MONTH(tanggal) AS bulan, SUM(harga_total) AS total FROM penjualan WHERE YEAR(tanggal) = $tahunIni GROUP BY bulan");
while ($row = $result->fetch_assoc()) {
    $penjualanBulanan[(int)$row['bulan']] = (int)$row['total'];
}
$labelBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$dataBulan = array_values($penjualanBulanan);

// Grafik Kas
$kasMasuk = array_fill(1, 12, 0);
$kasKeluar = array_fill(1, 12, 0);
$resMasuk = $koneksi->query("SELECT MONTH(tanggal) AS bulan, SUM(jumlah) AS total FROM kas WHERE jenis = 'masuk' AND YEAR(tanggal) = $tahunIni GROUP BY bulan");
while ($row = $resMasuk->fetch_assoc()) {
    $kasMasuk[(int)$row['bulan']] = (int)$row['total'];
}
$resKeluar = $koneksi->query("SELECT MONTH(tanggal) AS bulan, SUM(jumlah) AS total FROM kas WHERE jenis = 'keluar' AND YEAR(tanggal) = $tahunIni GROUP BY bulan");
while ($row = $resKeluar->fetch_assoc()) {
    $kasKeluar[(int)$row['bulan']] = (int)$row['total'];
}

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mt-4 mb-1">Selamat Datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></h4>
                <p class="text-muted mb-0">PT. INTIBOGA MANDIRI – Dashboard Admin – <?= $todayDisplay ?></p>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row g-3">
            <?php
            $box = [
                ['Jumlah Pengguna', $totalUser, 'bg-primary', 'bi-people-fill'],
                ['Total Barang', $totalBarang, 'bg-success', 'bi-box-fill'],
                ['Transaksi Hari Ini', $totalJual, 'bg-warning', 'bi-cart-fill'],
                ['Barang Masuk Hari Ini', $totalMasuk, 'bg-info', 'bi-box-arrow-in-down']
            ];
            foreach ($box as [$label, $value, $bg, $icon]): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="<?= $bg ?> text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi <?= $icon ?> fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0"><?= $label ?></h6>
                                <h4 class="fw-bold mb-0"><?= $value ?></h4>
                                <small class="text-muted"><?= $label ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Grafik Penjualan -->
        <div class="card custom-card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">📈 Grafik Penjualan Bulanan – <?= $tahunIni ?></h6>
            </div>
            <div class="card-body"><canvas id="grafikPenjualan"></canvas></div>
        </div>

        <!-- Grafik Kas -->
        <div class="card custom-card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">💰 Grafik Kas Masuk vs Keluar – <?= $tahunIni ?></h6>
            </div>
            <div class="card-body"><canvas id="grafikKas"></canvas></div>
        </div>

        <!-- Notifikasi Stok -->
        <?php
        $stok = $koneksi->query("
        SELECT 
            b.nama_barang,
            b.stok_minimum,
            (
                IFNULL(masuk.total_masuk, 0)
                - (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0))
            ) AS stok_akhir
        FROM barang b
        LEFT JOIN (
            SELECT id_barang, SUM(jumlah) AS total_masuk FROM barang_masuk GROUP BY id_barang
        ) masuk ON b.id = masuk.id_barang
        LEFT JOIN (
            SELECT id_barang, SUM(jumlah) AS total_keluar FROM barang_keluar GROUP BY id_barang
        ) keluar ON b.id = keluar.id_barang
        LEFT JOIN (
            SELECT id_barang, SUM(jumlah) AS total_terjual FROM penjualan GROUP BY id_barang
        ) pj ON b.id = pj.id_barang
        LEFT JOIN (
            SELECT p.id_barang, SUM(rp.jumlah) AS total_retur
            FROM retur_penjualan rp
            JOIN penjualan p ON rp.id_penjualan = p.id
            GROUP BY p.id_barang
        ) retur ON b.id = retur.id_barang
        HAVING stok_akhir < b.stok_minimum
        ORDER BY stok_akhir ASC
        LIMIT 5
    ");
        ?>
        <div class="card custom-card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">🚨 Stok Minimum</h6>
            </div>
            <div class="card-body">
                <?php if ($stok->num_rows): ?>
                    <ul class="list-group">
                        <?php while ($s = $stok->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($s['nama_barang']) ?>
                                <span class="badge bg-danger">Stok Akhir: <?= $s['stok_akhir'] ?> | Min: <?= $s['stok_minimum'] ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted mb-0">Tidak ada barang di bawah stok minimum.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Transaksi Terakhir -->
        <?php
        $lastSales = $koneksi->query("SELECT p.tanggal, b.nama_barang, p.jumlah, p.harga_total
                                      FROM penjualan p JOIN barang b ON p.id_barang = b.id
                                      ORDER BY p.tanggal DESC LIMIT 5");
        ?>
        <div class="card custom-card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">🧾 5 Transaksi Terakhir</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($trx = $lastSales->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($trx['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($trx['nama_barang']) ?></td>
                                    <td><?= $trx['jumlah'] ?></td>
                                    <td>Rp <?= number_format($trx['harga_total'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?= json_encode($labelBulan) ?>;
    new Chart(document.getElementById('grafikPenjualan'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Total Penjualan',
                data: <?= json_encode($dataBulan) ?>,
                backgroundColor: 'rgba(54,162,235,0.6)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('grafikKas'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                    label: 'Kas Masuk',
                    data: <?= json_encode(array_values($kasMasuk)) ?>,
                    backgroundColor: 'rgba(40,167,69,0.6)'
                },
                {
                    label: 'Kas Keluar',
                    data: <?= json_encode(array_values($kasKeluar)) ?>,
                    backgroundColor: 'rgba(220,53,69,0.6)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>