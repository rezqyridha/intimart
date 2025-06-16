<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'sales') {
    header("Location: " . BASE_URL . "/forbidden.php");
    exit;
}

$today = date('Y-m-d');
$todayDisplay = date('d-m-Y');
$tahunIni = date('Y');
$idSales = $_SESSION['id_user'];
$nama = $_SESSION['nama_lengkap'] ?? 'User';

// Data Statistik Kinerja
$totalJual = $koneksi->query("SELECT COUNT(*) AS total FROM penjualan WHERE DATE(tanggal) = '$today' AND id_sales = $idSales")->fetch_assoc()['total'] ?? 0;
$totalTidakLaku = $koneksi->query("SELECT COUNT(*) AS total FROM produk_tidak_laku WHERE status IN ('diperiksa', 'tindaklanjut')")->fetch_assoc()['total'] ?? 0;
$totalTarget = $koneksi->query("SELECT COUNT(*) AS total FROM target_sales")->fetch_assoc()['total'] ?? 0;
$totalKirim = $koneksi->query("SELECT COUNT(*) AS total FROM pengiriman WHERE status_pengiriman = 'dikirim'")->fetch_assoc()['total'] ?? 0;

// Grafik Penjualan Bulanan
$penjualan = array_fill(1, 12, 0);
$res = $koneksi->query("
    SELECT MONTH(tanggal) AS bulan, SUM(harga_total) AS total 
    FROM penjualan 
    WHERE id_sales = $idSales AND YEAR(tanggal) = $tahunIni 
    GROUP BY bulan
");
while ($row = $res->fetch_assoc()) {
    $penjualan[(int)$row['bulan']] = (int)$row['total'];
}
$labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$dataBulan = array_values($penjualan);

// Transaksi Terakhir oleh sales
$lastSales = $koneksi->query("
    SELECT p.tanggal, b.nama_barang, p.jumlah, p.harga_total
    FROM penjualan p
    JOIN barang b ON p.id_barang = b.id
    WHERE p.id_sales = $idSales
    ORDER BY p.tanggal DESC
    LIMIT 5
");

// Barang Stok Minimum
$stok = $koneksi->query("
    SELECT 
        b.nama_barang,
        b.stok_minimum,
        (
            IFNULL(masuk.total_masuk, 0)
            - (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0))
        ) AS stok_akhir
    FROM barang b
    LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_masuk FROM barang_masuk GROUP BY id_barang) masuk ON b.id = masuk.id_barang
    LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_keluar FROM barang_keluar GROUP BY id_barang) keluar ON b.id = keluar.id_barang
    LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_terjual FROM penjualan GROUP BY id_barang) pj ON b.id = pj.id_barang
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

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mt-4 mb-1">Selamat Datang, <strong><?= $nama ?></strong></h4>
                <p class="text-muted mb-0">PT. INTIBOGA MANDIRI – Dashboard Sales – <?= $todayDisplay ?></p>
            </div>
        </div>

        <!-- Statistik Kinerja -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mt-2">
            <?php
            $boxes = [
                ['Penjualan Hari Ini', $totalJual, 'primary', 'cart-check'],
                ['Produk Tidak Laku', $totalTidakLaku, 'danger', 'archive'],
                ['Target Aktif', $totalTarget, 'warning text-dark', 'graph-up'],
                ['Pengiriman Berjalan', $totalKirim, 'info', 'truck'],
            ];
            foreach ($boxes as [$label, $val, $bg, $icon]): ?>
                <div class="col">
                    <div class="card shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-<?= $bg ?> text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-<?= $icon ?> fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0"><?= $label ?></h6>
                                <h4 class="fw-bold mb-0"><?= $val ?></h4>
                                <small class="text-muted"><?= $label ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Grafik Penjualan Bulanan -->
        <div class="card custom-card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">📈 Grafik Penjualan Bulanan</h6>
            </div>
            <div class="card-body"><canvas id="grafikPenjualan"></canvas></div>
        </div>

        <!-- Transaksi Terakhir -->
        <div class="card custom-card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">🧾 5 Transaksi Terakhir Anda</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $lastSales->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= $row['jumlah'] ?></td>
                                <td>Rp <?= number_format($row['harga_total'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notifikasi Stok Rendah -->
        <div class="card custom-card mt-4 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">🚨 Barang Stok Minimum</h6>
            </div>
            <div class="card-body">
                <?php if ($stok->num_rows): ?>
                    <ul class="list-group">
                        <?php while ($s = $stok->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($s['nama_barang']) ?>
                                <span class="badge bg-danger">Sisa: <?= $s['stok_akhir'] ?> | Min: <?= $s['stok_minimum'] ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted mb-0">Tidak ada barang dengan stok di bawah minimum.</p>
                <?php endif; ?>
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
    new Chart(document.getElementById("grafikPenjualan"), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelBulan) ?>,
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: <?= json_encode($dataBulan) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
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