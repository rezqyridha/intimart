<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'manajer', 'sales', 'karyawan'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Semua role boleh mengakses modul ini
$query = "
    SELECT 
    b.id,
    b.nama_barang,
    b.satuan,
    b.stok_minimum,
    IFNULL(masuk.total_masuk, 0) AS stok_masuk,
    IFNULL(keluar.total_keluar, 0) AS stok_keluar_manual,
    IFNULL(pj.total_terjual, 0) AS stok_terjual,
    IFNULL(retur.total_retur, 0) AS stok_retur,
    (
        IFNULL(masuk.total_masuk, 0)
        - (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0))
    ) AS stok_akhir
FROM barang b
LEFT JOIN (
    SELECT id_barang, SUM(jumlah) AS total_masuk
    FROM barang_masuk
    GROUP BY id_barang
) masuk ON b.id = masuk.id_barang
LEFT JOIN (
    SELECT id_barang, SUM(jumlah) AS total_keluar
    FROM barang_keluar
    GROUP BY id_barang
) keluar ON b.id = keluar.id_barang
LEFT JOIN (
    SELECT id_barang, SUM(jumlah) AS total_terjual
    FROM penjualan
    GROUP BY id_barang
) pj ON b.id = pj.id_barang
LEFT JOIN (
    SELECT p.id_barang, SUM(rp.jumlah) AS total_retur
    FROM retur_penjualan rp
    JOIN penjualan p ON rp.id_penjualan = p.id
    GROUP BY p.id_barang
) retur ON b.id = retur.id_barang

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
        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Stok Real-Time</div>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered border table-hover table-striped mb-0 align-middle" id="tabel-stok">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Masuk</th>
                                <th>Keluar Manual</th>
                                <th>Terjual</th>
                                <th>Retur</th>
                                <th>Stok Akhir</th>
                                <th>Minimum</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $result->fetch_assoc()):
                                $status = "Aman";
                                $class = "text-success";
                                if ($row['stok_akhir'] <= 0) {
                                    $status = "Habis";
                                    $class = "text-danger fw-bold";
                                } elseif ($row['stok_akhir'] <= $row['stok_minimum']) {
                                    $status = "Menipis";
                                    $class = "text-warning fw-bold";
                                }
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= $row['satuan'] ?></td>
                                    <td><?= $row['stok_masuk'] ?></td>
                                    <td><?= $row['stok_keluar_manual'] ?? 0 ?></td>
                                    <td><?= $row['stok_terjual'] ?? 0 ?></td>
                                    <td><?= $row['stok_retur'] ?? 0 ?></td>
                                    <td><?= $row['stok_akhir'] ?></td>
                                    <td><?= $row['stok_minimum'] ?></td>
                                    <td class="<?= $class ?>"><?= $status ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<script>
    document.getElementById("searchBox").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-stok tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>