<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer', 'karyawan'])) {
    header("Location: ../../unauthorized.php");
    exit;
}

$query = "
    SELECT b.id, b.nama_barang, b.satuan, b.stok_minimum,
        IFNULL(masuk.total_masuk, 0) AS stok_masuk,
        IFNULL(keluar.total_keluar, 0) AS stok_keluar_manual,
        IFNULL(pj.total_terjual, 0) AS stok_terjual,
        IFNULL(retur.total_retur, 0) AS stok_retur,
        sf.jumlah_fisik,
        sf.koreksi,
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
        FROM retur_penjualan rp JOIN penjualan p ON rp.id_penjualan = p.id
        GROUP BY p.id_barang
    ) retur ON b.id = retur.id_barang
    LEFT JOIN (
        SELECT id_barang, jumlah_fisik, koreksi FROM stok_fisik WHERE koreksi = 1 ORDER BY tanggal DESC
    ) sf ON b.id = sf.id_barang
    WHERE sf.koreksi = 1
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
        <h3 class="mt-4 mb-3">📦 Laporan Stok Barang (Hasil Koreksi)</h3>

        <form method="GET" action="cetak_stok_barang.php" target="_blank" class="mb-3">
            <button type="submit" class="btn btn-danger">
                <i class="fa fa-print"></i> Cetak PDF
            </button>
        </form>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Stok Sistem</th>
                            <th>Stok Fisik</th>
                            <th>Minimum</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($row = $result->fetch_assoc()):
                            $sistem = $row['stok_akhir'];
                            $fisik = $row['jumlah_fisik'];
                            $status = 'Aman';
                            $badge = 'success';
                            if ($fisik <= 0) {
                                $status = 'Habis';
                                $badge = 'danger';
                            } elseif ($fisik <= $row['stok_minimum']) {
                                $status = 'Menipis';
                                $badge = 'warning';
                            }
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= $row['satuan'] ?></td>
                                <td><?= $sistem ?></td>
                                <td><?= $fisik ?> <span class="badge bg-info text-dark">Dikoreksi</span></td>
                                <td><?= $row['stok_minimum'] ?></td>
                                <td><span class="badge bg-<?= $badge ?>"><?= $status ?></span></td>
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