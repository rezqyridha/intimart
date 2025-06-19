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
        <li class="slide <?= strpos($current_uri, '/karyawan/dashboard.php') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/karyawan/dashboard.php" class="side-menu__item">
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- GUDANG -->
        <li class="slide__category"><span class="category-name">Gudang & Stok</span></li>
        <?php $stok_uri = [
            '/modules/shared/barang/',
            '/modules/shared/barang_masuk/',
            '/modules/shared/barang_keluar/',
            '/modules/shared/stok_fisik/',
            '/modules/shared/stok/',
            '/modules/shared/produk_tidak_laku/',
            '/modules/shared/kadaluarsa/',
            '/modules/shared/gudang/'
        ]; ?>
        <li class="slide has-sub <?= is_uri_match($stok_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-package side-menu__icon"></i>
                <span class="side-menu__label">Manajemen Stok</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/barang/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/barang/') !== false ? 'active' : '' ?>">Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_masuk/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/barang_masuk/') !== false ? 'active' : '' ?>">Barang Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_keluar/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/barang_keluar/') !== false ? 'active' : '' ?>">Barang Keluar</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok_fisik/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/stok_fisik/') !== false ? 'active' : '' ?>">Stok Fisik</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/stok/') !== false ? 'active' : '' ?>">Stok Sistem</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/produk_tidak_laku/') !== false ? 'active' : '' ?>">Produk Tidak Laku</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/kadaluarsa/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/kadaluarsa/') !== false ? 'active' : '' ?>">Kadaluarsa</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/gudang/index.php" class="side-menu__item <?= strpos($current_uri, '/modules/shared/gudang/') !== false ? 'active' : '' ?>">Gudang</a></li>
            </ul>
        </li>

        <!-- RESTOK -->
        <li class="slide__category"><span class="category-name">Permintaan</span></li>
        <li class="slide <?= strpos($current_uri, '/modules/shared/restock_supplier/') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/restock_supplier/index.php" class="side-menu__item">
                <i class="ti-reload side-menu__icon"></i>
                <span class="side-menu__label">Restok Supplier</span>
            </a>
        </li>

        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <?php $laporan_uri = [
            '/modules/shared/laporan/stok_barang',
            '/modules/shared/laporan/produk_tidak_laku',
            '/modules/shared/laporan/pengiriman'
        ]; ?>
        <li class="slide has-sub <?= is_uri_match($laporan_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-bar-chart side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/stok_barang.php" class="side-menu__item <?= strpos($current_uri, 'laporan/stok_barang') !== false ? 'active' : '' ?>">Stok Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/produk_tidak_laku.php" class="side-menu__item <?= strpos($current_uri, 'laporan/produk_tidak_laku') !== false ? 'active' : '' ?>">Produk Tidak Laku</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/pengiriman.php" class="side-menu__item <?= strpos($current_uri, 'laporan/pengiriman') !== false ? 'active' : '' ?>">Pengiriman</a></li>
            </ul>
        </li>

        <!-- NOTIFIKASI -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide <?= strpos($current_uri, '/modules/shared/notifikasi_stok/') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/notifikasi_stok/index.php" class="side-menu__item">
                <i class="ti-alert side-menu__icon"></i>
                <span class="side-menu__label">Stok Menipis</span>
                <span class="badge bg-danger ms-auto" id="notif-stok"></span>
            </a>
        </li>
    </ul>
</nav>
<!-- End::nav -->