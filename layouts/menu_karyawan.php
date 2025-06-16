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

<!-- Start::nav -->
<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <ul class="main-menu">

        <!-- DASHBOARD -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= str_contains($current_uri, '/karyawan/dashboard.php') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/karyawan/dashboard.php" class="side-menu__item">
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- GUDANG -->
        <li class="slide__category"><span class="category-name">Gudang & Stok</span></li>
        <?php $stok_uri = ['/barang', '/barang_masuk', '/barang_keluar', '/stok', '/stok_fisik', '/produk_tidak_laku']; ?>
        <li class="slide has-sub <?= is_uri_match($stok_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-package side-menu__icon"></i>
                <span class="side-menu__label">Manajemen Stok</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/barang/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang/') ? 'active' : '' ?>">Data Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_masuk/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang_masuk/') ? 'active' : '' ?>">Barang Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_keluar/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang_keluar/') ? 'active' : '' ?>">Barang Keluar</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok_fisik/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok_fisik/') ? 'active' : '' ?>">Update Stok Fisik</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok/') ? 'active' : '' ?>">Stok Sistem</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item <?= str_contains($current_uri, '/produk_tidak_laku/') ? 'active' : '' ?>">Produk Tidak Laku</a></li>
            </ul>
        </li>

        <!-- TRANSAKSI -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <?php $trx_uri = ['/penjualan/', '/retur_penjualan/']; ?>
        <li class="slide has-sub <?= is_uri_match($trx_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-shopping-cart-full side-menu__icon"></i>
                <span class="side-menu__label">Penjualan & Retur</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/index.php" class="side-menu__item <?= str_contains($current_uri, 'penjualan/transaksi.php') ? 'active' : '' ?>">Input Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item <?= str_contains($current_uri, '/retur_penjualan') ? 'active' : '' ?>">Retur Penjualan</a></li>
            </ul>
        </li>

        <!-- DISTRIBUSI -->
        <li class="slide__category"><span class="category-name">Distribusi</span></li>
        <?php $distribusi_uri = ['/pengiriman', '/pembayaran']; ?>
        <li class="slide has-sub <?= is_uri_match($distribusi_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-truck side-menu__icon"></i>
                <span class="side-menu__label">Pengiriman & Pembayaran</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php" class="side-menu__item <?= str_contains($current_uri, '/pengiriman') ? 'active' : '' ?>">Pengiriman</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/index.php" class="side-menu__item <?= str_contains($current_uri, '/pembayaran') ? 'active' : '' ?>">Pembayaran</a></li>
            </ul>
        </li>

        <!-- SUPPLIER -->
        <li class="slide__category"><span class="category-name">Mitra</span></li>
        <li class="slide <?= str_contains($current_uri, '/supplier') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/supplier/index.php" class="side-menu__item">
                <i class="ti-user side-menu__icon"></i>
                <span class="side-menu__label">Supplier</span>
            </a>
        </li>

        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <li class="slide <?= str_contains($current_uri, '/laporan/stok') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/laporan/stok.php" class="side-menu__item">
                <i class="ti-bar-chart side-menu__icon"></i>
                <span class="side-menu__label">Laporan Stok</span>
            </a>
        </li>

        <!-- NOTIFIKASI -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide <?= str_contains($current_uri, '/notifikasi_stok') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/notifikasi_stok/index.php" class="side-menu__item">
                <i class="ti-alert side-menu__icon"></i>
                <span class="side-menu__label">Stok Menipis</span>
                <span class="badge bg-danger ms-auto" id="notif-stok"></span>
            </a>
        </li>

    </ul>
</nav>
<!-- End::nav -->