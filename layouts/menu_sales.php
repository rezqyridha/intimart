<?php
require_once CONFIG_PATH . '/constants.php';
$current_page = basename($_SERVER['PHP_SELF']);
$current_uri = $_SERVER['REQUEST_URI'];

function is_uri_match(array $patterns): bool
{
    global $current_uri;
    foreach ($patterns as $pattern) {
        if (str_contains($current_uri, $pattern)) return true;
    }
    return false;
}
?>

<!-- Sidebar Menu untuk Sales -->
<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <ul class="main-menu">

        <!-- DASHBOARD -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= str_contains($current_uri, '/sales/dashboard.php') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/sales/dashboard.php" class="side-menu__item">
                <i class="bi bi-house-door-fill side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- PENJUALAN -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <?php $trx_uri = ['/penjualan/', '/retur_penjualan/']; ?>
        <li class="slide has-sub <?= is_uri_match($trx_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="bi bi-cart-check side-menu__icon"></i>
                <span class="side-menu__label">Penjualan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/penjualan/index.php"
                        class="side-menu__item <?= str_contains($current_uri, 'penjualan/transaksi.php') ? 'active' : '' ?>">
                        Input Penjualan
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php"
                        class="side-menu__item <?= str_contains($current_uri, 'retur_penjualan') ? 'active' : '' ?>">
                        Retur Penjualan
                    </a>
                </li>
            </ul>
        </li>

        <!-- EVALUASI PRODUK -->
        <li class="slide__category"><span class="category-name">Evaluasi</span></li>
        <li class="slide <?= str_contains($current_uri, '/produk_tidak_laku') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item">
                <i class="bi bi-archive side-menu__icon"></i>
                <span class="side-menu__label">Produk Tidak Laku</span>
            </a>
        </li>

        <!-- PENGIRIMAN -->
        <li class="slide__category"><span class="category-name">Distribusi</span></li>
        <li class="slide <?= str_contains($current_uri, '/pengiriman') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php" class="side-menu__item">
                <i class="bi bi-truck side-menu__icon"></i>
                <span class="side-menu__label">Riwayat Pengiriman</span>
            </a>
        </li>

        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <?php $laporan_uri = ['/laporan/penjualan', '/laporan/keuangan', '/laporan/stok']; ?>
        <li class="slide has-sub <?= is_uri_match($laporan_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="bi bi-bar-chart-line-fill side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php"
                        class="side-menu__item <?= str_contains($current_uri, 'laporan/penjualan') ? 'active' : '' ?>">
                        Laporan Penjualan
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/laporan/keuangan.php"
                        class="side-menu__item <?= str_contains($current_uri, 'laporan/keuangan') ? 'active' : '' ?>">
                        Laporan Keuangan
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/laporan/stok.php"
                        class="side-menu__item <?= str_contains($current_uri, 'laporan/stok') ? 'active' : '' ?>">
                        Laporan Stok
                    </a>
                </li>
            </ul>
        </li>

        <!-- NOTIFIKASI -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide <?= str_contains($current_uri, '/notifikasi_stok') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/notifikasi_stok/index.php" class="side-menu__item">
                <i class="bi bi-exclamation-diamond-fill side-menu__icon"></i>
                <span class="side-menu__label">Stok Menipis</span>
                <span class="badge bg-danger ms-auto" id="notif-stok"></span>
            </a>
        </li>

    </ul>
</nav>