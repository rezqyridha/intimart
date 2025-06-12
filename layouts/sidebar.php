<?php
$role = $_SESSION['role'] ?? 'guest';
$menuFile = __DIR__ . "/menu_$role.php";


// Hitung jumlah barang stok menipis
/*
$notif_stok = 0;
try {
    $qNotif = $koneksi->query("
        SELECT COUNT(*) AS total FROM (
            SELECT 
                b.id,
                b.stok_minimum,
                (
                    IFNULL(bm.total_masuk, 0)
                    - (IFNULL(bk.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(rj.total_retur, 0))
                ) AS stok_akhir
            FROM barang b
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) AS total_masuk FROM barang_masuk GROUP BY id_barang
            ) bm ON bm.id_barang = b.id
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) AS total_keluar FROM barang_keluar GROUP BY id_barang
            ) bk ON bk.id_barang = b.id
            LEFT JOIN (
                SELECT id_barang, SUM(jumlah) AS total_terjual FROM penjualan GROUP BY id_barang
            ) pj ON pj.id_barang = b.id
            LEFT JOIN (
                SELECT p.id_barang, SUM(rp.jumlah) AS total_retur
                FROM retur_penjualan rp
                INNER JOIN penjualan p ON rp.id_penjualan = p.id
                GROUP BY p.id_barang
            ) rj ON rj.id_barang = b.id
        ) AS stok_summary
        WHERE stok_akhir <= stok_minimum
    ");
    $notif_stok = $qNotif->fetch_assoc()['total'] ?? 0;
} catch (Exception $e) {
    $notif_stok = 0;
}
    */

?>
<!-- Sidebar -->
<aside class="app-sidebar sticky sidebar-dark" id="sidebar">
    <!-- Sidebar Header / Logo -->
    <div class="main-sidebar-header">
        <a href="<?= BASE_URL ?>/modules/<?= $role ?>/dashboard.php" class="header-logo">
            <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="logo sidebar" style="height: 40px;">
        </a>
    </div>

    <!-- Sidebar Content / Menu -->
    <div class="main-sidebar" id="sidebar-scroll">
        <?php
        if (file_exists($menuFile)) {
            require_once $menuFile;
        } else {
            echo "<div class='text-danger p-3'>Menu untuk role <strong>$role</strong> tidak ditemukan.</div>";
        }
        ?>
    </div>
</aside>