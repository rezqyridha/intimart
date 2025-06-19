<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
if (!in_array($role, ['admin', 'manajer', 'karyawan'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Periode 30 hari terakhir
$tanggalHariIni = date('Y-m-d');
$tanggalAwal    = date('Y-m-d', strtotime('-30 days'));

// Ambil produk yang tidak terjual dalam 30 hari terakhir
$query = "
    SELECT b.id, b.nama_barang, b.satuan
    FROM barang b
    LEFT JOIN penjualan p 
        ON b.id = p.id_barang AND p.tanggal >= CURDATE() - INTERVAL 30 DAY
    WHERE p.id IS NULL
    ORDER BY b.nama_barang
";
$result = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    Laporan Produk Tidak Laku (<?= date('d M Y', strtotime($tanggalAwal)) ?> s.d. <?= date('d M Y', strtotime($tanggalHariIni)) ?>)
                </div>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0" id="tabel-produk">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): $no = 1; ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Semua produk pernah terjual dalam 30 hari terakhir.</td>
                                </tr>
                            <?php endif; ?>
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

<script>
    document.getElementById("searchBox").addEventListener("input", function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll("#tabel-produk tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(keyword) ? "" : "none";
        });
    });
</script>