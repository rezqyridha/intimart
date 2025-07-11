<?php
require_once CONFIG_PATH . '/constants.php';
$current_page = basename($_SERVER['PHP_SELF']);
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

<!-- Start::nav -->
<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <ul class="main-menu">

        <!-- DASHBOARD -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= strpos($current_uri, '/sales/dashboard.php') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/sales/dashboard.php" class="side-menu__item">
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- TRANSAKSI -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <?php $trx_uri = [
            '/modules/shared/penjualan/',
            '/modules/shared/pembayaran/',
            '/modules/shared/retur_penjualan/',
            '/modules/shared/pemesanan/'
        ]; ?>
        <li class="slide has-sub <?= is_uri_match($trx_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-shopping-cart-full side-menu__icon"></i>
                <span class="side-menu__label">Penjualan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/penjualan/') !== false ? 'active' : '' ?>">Input Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/pembayaran/') !== false ? 'active' : '' ?>">Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/retur_penjualan/') !== false ? 'active' : '' ?>">Retur Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pemesanan/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/pemesanan/') !== false ? 'active' : '' ?>">Pemesanan</a></li>
            </ul>
        </li>

        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <?php $laporan_uri = [
            '/modules/shared/laporan/penjualan',
            '/modules/shared/laporan/pemesanan',
            '/modules/shared/laporan/retur_penjualan',
            '/modules/shared/laporan/target_sales'
        ]; ?>
        <li class="slide has-sub <?= is_uri_match($laporan_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-bar-chart side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php" class="side-menu__item <?= strpos($current_uri, 'laporan/penjualan') !== false ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/pemesanan.php" class="side-menu__item <?= strpos($current_uri, 'laporan/pemesanan') !== false ? 'active' : '' ?>">Pemesanan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/retur_penjualan.php" class="side-menu__item <?= strpos($current_uri, 'laporan/retur_penjualan') !== false ? 'active' : '' ?>">Retur Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/target_sales.php" class="side-menu__item <?= strpos($current_uri, 'laporan/target_sales') !== false ? 'active' : '' ?>">Target Sales</a></li>
            </ul>
        </li>
    </ul>
</nav>
<!-- End::nav -->