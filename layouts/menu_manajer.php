<?php
require_once CONFIG_PATH . '/constants.php';
$current_uri = $_SERVER['REQUEST_URI'];

function is_uri_match(array $patterns): bool
{
    global $current_uri;
    foreach ($patterns as $pattern) {
        if (strpos($current_uri, $pattern) !== false) return true;
    }
    return false;
}
?>

<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <ul class="main-menu">

        <!-- DASHBOARD -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= str_contains($current_uri, '/manajer/dashboard.php') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/manajer/dashboard.php" class="side-menu__item">
                <i class="bi bi-house-door-fill side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- MODUL VIEW-ONLY -->
        <li class="slide__category"><span class="category-name">Modul View-Only</span></li>
        <?php $modul_uri = ['/stok_fisik', '/produk_tidak_laku', '/gudang', '/restock_supplier', '/rekonsiliasi_pembayaran']; ?>
        <li class="slide has-sub <?= is_uri_match($modul_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="bi bi-eye side-menu__icon"></i>
                <span class="side-menu__label">Modul Tambahan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/stok_fisik/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok_fisik') ? 'active' : '' ?>">Stok Fisik</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item <?= str_contains($current_uri, '/produk_tidak_laku/') ? 'active' : '' ?>">Produk Tidak Laku</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/gudang/index.php" class="side-menu__item <?= str_contains($current_uri, '/gudang') ? 'active' : '' ?>">Gudang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/restock_supplier/index.php" class="side-menu__item <?= str_contains($current_uri, '/restock_supplier') ? 'active' : '' ?>">Restok Supplier</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/rekonsiliasi_pembayaran/index.php" class="side-menu__item <?= str_contains($current_uri, '/rekonsiliasi_pembayaran') ? 'active' : '' ?>">Rekonsiliasi Pembayaran</a></li>
            </ul>
        </li>

        <!-- TRANSAKSI -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <?php $trx_uri = [
            '/penjualan/index.php',
            '/pembayaran/index.php',
            '/retur_penjualan/index.php',
            '/pemesanan/index.php'
        ]; ?>
        <li class="slide has-sub <?= is_uri_match($trx_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="bi bi-clipboard-data side-menu__icon"></i>
                <span class="side-menu__label">Data Transaksi</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/index.php" class="side-menu__item <?= $current_uri === '/intimart/modules/shared/penjualan/index.php' ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/index.php" class="side-menu__item <?= $current_uri === '/intimart/modules/shared/pembayaran/index.php' ? 'active' : '' ?>">Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item <?= $current_uri === '/intimart/modules/shared/retur_penjualan/index.php' ? 'active' : '' ?>">Retur Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pemesanan/index.php" class="side-menu__item <?= $current_uri === '/intimart/modules/shared/pemesanan/index.php' ? 'active' : '' ?>">Pemesanan</a></li>
            </ul>
        </li>

        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <?php $laporan_uri = ['/laporan/']; ?>
        <li class="slide has-sub <?= is_uri_match($laporan_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="bi bi-bar-chart-line side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/penjualan.php') ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/pemesanan.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/pemesanan.php') ? 'active' : '' ?>">Pemesanan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/retur_penjualan.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/retur_penjualan.php') ? 'active' : '' ?>">Retur Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/target_sales.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/target_sales.php') ? 'active' : '' ?>">Target Sales</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/stok_barang.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/stok_barang.php') ? 'active' : '' ?>">Stok Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/produk_tidak_laku.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/produk_tidak_laku.php') ? 'active' : '' ?>">Produk Tidak Laku</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/piutang.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/piutang.php') ? 'active' : '' ?>">Piutang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/pengiriman.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/pengiriman.php') ? 'active' : '' ?>">Pengiriman</a></li>
            </ul>
        </li>

    </ul>
</nav>