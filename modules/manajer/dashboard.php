<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'manajer') {
    header("Location: " . AUTH_PATH . "/login.php");
    exit;
}

$today = date('Y-m-d');
$todayDisplay = date('d-m-Y');
$tahunIni = date('Y');

// Statistik
$totalBarang = $koneksi->query("SELECT COUNT(*) AS total FROM barang")->fetch_assoc()['total'] ?? 0;
$totalPenjualan = $koneksi->query("SELECT COUNT(*) AS total FROM penjualan WHERE DATE(tanggal) = '$today'")->fetch_assoc()['total'] ?? 0;
$totalRetur = $koneksi->query("SELECT COUNT(*) AS total FROM retur_penjualan")->fetch_assoc()['total'] ?? 0;
$totalPembayaran = $koneksi->query("SELECT COUNT(*) AS total FROM pembayaran")->fetch_assoc()['total'] ?? 0;

// Grafik Penjualan
$penjualanBulanan = array_fill(1, 12, 0);
$resJual = $koneksi->query("SELECT MONTH(tanggal) AS bulan, SUM(harga_total) AS total FROM penjualan WHERE YEAR(tanggal) = $tahunIni GROUP BY bulan");
while ($row = $resJual->fetch_assoc()) {
    $penjualanBulanan[(int)$row['bulan']] = (int)$row['total'];
}
$dataPenjualan = array_values($penjualanBulanan);

// Grafik Pembayaran
$pembayaranBulanan = array_fill(1, 12, 0);
$resPay = $koneksi->query("SELECT MONTH(tanggal) AS bulan, SUM(jumlah_bayar) AS total FROM pembayaran WHERE YEAR(tanggal) = $tahunIni GROUP BY bulan");
while ($row = $resPay->fetch_assoc()) {
    $pembayaranBulanan[(int)$row['bulan']] = (int)$row['total'];
}
$dataPembayaran = array_values($pembayaranBulanan);

// Transaksi Terbaru
$lastTransaksi = $koneksi->query("
    SELECT p.tanggal, b.nama_barang, p.jumlah, p.harga_total
    FROM penjualan p
    JOIN barang b ON p.id_barang = b.id
    ORDER BY p.tanggal DESC
    LIMIT 5
");

$labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mt-4 mb-1">Selamat Datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></h4>
                <p class="text-muted mb-0">PT. INTIBOGA MANDIRI – Dashboard Manajer – <?= $todayDisplay ?></p>
            </div>
        </div>

        <!-- Statistik Row -->
        <div class="row mt-4">

            <!-- Box: Total Barang -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Barang</h6>
                            <h4 class="fw-bold mb-0"><?= $totalBarang ?></h4>
                            <small class="text-muted">Data produk tersedia</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box: Penjualan Hari Ini -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart-check-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Penjualan Hari Ini</h6>
                            <h4 class="fw-bold mb-0"><?= $totalPenjualan ?></h4>
                            <small class="text-muted">Tanggal <?= $todayDisplay ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box: Retur Penjualan -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-arrow-return-left fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Retur Penjualan</h6>
                            <h4 class="fw-bold mb-0"><?= $totalRetur ?></h4>
                            <small class="text-muted">Total retur</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box: Pembayaran -->
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Pembayaran</h6>
                            <h4 class="fw-bold mb-0"><?= $totalPembayaran ?></h4>
                            <small class="text-muted">Riwayat pembayaran</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik -->
        <div class="row mt-4">
            <!-- Grafik Penjualan -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">📈 Penjualan Bulanan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPenjualan"></canvas>
                    </div>
                </div>
            </div>
            <!-- Grafik Pembayaran -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">💳 Pembayaran Bulanan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPembayaran"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Terakhir -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">🧾 5 Transaksi Penjualan Terbaru</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($trx = $lastTransaksi->fetch_assoc()): ?>
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

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('chartPenjualan'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelBulan) ?>,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: <?= json_encode($dataPenjualan) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById('chartPembayaran'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labelBulan) ?>,
            datasets: [{
                label: 'Pembayaran (Rp)',
                data: <?= json_encode($dataPembayaran) ?>,
                borderColor: 'rgba(255, 99, 132, 1)',
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>