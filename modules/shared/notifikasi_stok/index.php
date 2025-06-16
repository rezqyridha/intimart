<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// Role yang diizinkan
$role = $_SESSION['role'];
if (!in_array($role, ['admin', 'manajer', 'sales', 'karyawan'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Ambil barang yang stoknya menipis (stok akhir < stok minimum)
$query = "
    SELECT 
        b.id, 
        b.nama_barang, 
        b.satuan, 
        b.stok_minimum,
        (COALESCE(bm.jumlah, 0) - (COALESCE(bk.jumlah, 0) + COALESCE(pj.jumlah, 0) - COALESCE(rj.jumlah, 0))) AS stok_akhir
    FROM barang b
    LEFT JOIN (
        SELECT id_barang, SUM(jumlah) AS jumlah FROM barang_masuk GROUP BY id_barang
    ) bm ON bm.id_barang = b.id
    LEFT JOIN (
        SELECT id_barang, SUM(jumlah) AS jumlah FROM barang_keluar GROUP BY id_barang
    ) bk ON bk.id_barang = b.id
    LEFT JOIN (
        SELECT id_barang, SUM(jumlah) AS jumlah FROM penjualan GROUP BY id_barang
    ) pj ON pj.id_barang = b.id
    LEFT JOIN (
        SELECT p.id_barang, SUM(r.jumlah) AS jumlah
        FROM retur_penjualan r
        JOIN penjualan p ON r.id_penjualan = p.id
        GROUP BY p.id_barang
    ) rj ON rj.id_barang = b.id
    WHERE (COALESCE(bm.jumlah, 0) - (COALESCE(bk.jumlah, 0) + COALESCE(pj.jumlah, 0) - COALESCE(rj.jumlah, 0))) < b.stok_minimum
    ORDER BY stok_akhir ASC
";

$result = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Data Barang Stok Menipis</h5>
            </div>

            <div class="card-body">
                <?php if ($result->num_rows === 0): ?>
                    <div class="alert alert-success">🎉 Tidak ada stok menipis saat ini.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead class="table-warning">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Stok Tersisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $highlightId = isset($_GET['highlight']) ? intval($_GET['highlight']) : 0;
                                while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr id="barang-<?= $row['id'] ?>" class="<?= $row['id'] === $highlightId ? 'table-warning fw-bold highlight-row' : '' ?>">
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                                        <td><span class="badge bg-danger"><?= $row['stok_akhir'] ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>