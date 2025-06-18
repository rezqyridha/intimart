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

<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <ul class="main-menu">
        <!-- DASHBOARD -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/admin/dashboard.php" class="side-menu__item">
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- USER -->
        <li class="slide__category"><span class="category-name">User & Akses</span></li>
        <li class="slide <?= str_contains($current_uri, '/user/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/admin/user/index.php" class="side-menu__item">
                <i class="ti-user side-menu__icon"></i>
                <span class="side-menu__label">Manajemen User</span>
            </a>
        </li>

        <!-- MASTER -->
        <li class="slide__category"><span class="category-name">Data Master</span></li>
        <li class="slide <?= str_contains($current_uri, '/supplier/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/supplier/index.php" class="side-menu__item">
                <i class="ti-truck side-menu__icon"></i>
                <span class="side-menu__label">Supplier</span>
            </a>
        </li>
        <li class="slide <?= str_contains($current_uri, '/pelanggan/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/pelanggan/index.php" class="side-menu__item">
                <i class="ti-id-badge side-menu__icon"></i>
                <span class="side-menu__label">Pelanggan</span>
            </a>
        </li>

        <!-- GUDANG & BARANG -->
        <li class="slide__category"><span class="category-name">Stok & Produk</span></li>
        <?php $stok_uri = ['/barang', '/barang_masuk', '/barang_keluar', '/stok', '/stok_fisik', '/produk_tidak_laku']; ?>
        <li class="slide has-sub <?= is_uri_match($stok_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-package side-menu__icon"></i>
                <span class="side-menu__label">Manajemen Stok</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/barang/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang/') ? 'active' : '' ?>">Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_masuk/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang_masuk/') ? 'active' : '' ?>">Barang Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_keluar/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang_keluar/') ? 'active' : '' ?>">Barang Keluar</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok/') ? 'active' : '' ?>">Stok Sistem</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok_fisik/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok_fisik/') ? 'active' : '' ?>">Stok Fisik</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item <?= str_contains($current_uri, '/produk_tidak_laku/') ? 'active' : '' ?>">Produk Tidak Laku</a></li>
            </ul>
        </li>


        <!-- TRANSAKSI -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <?php $trx_uri = ['/penjualan', '/retur_penjualan', '/pembayaran', '/rekonsiliasi_pembayaran', '/pengiriman']; ?>
        <li class="slide has-sub <?= is_uri_match($trx_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-credit-card side-menu__icon"></i>
                <span class="side-menu__label">Transaksi</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/index.php" class="side-menu__item <?= str_contains($current_uri, '/penjualan/') ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item <?= str_contains($current_uri, '/retur_penjualan/') ? 'active' : '' ?>">Retur</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/index.php" class="side-menu__item <?= str_contains($current_uri, '/pembayaran/') ? 'active' : '' ?>">Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/rekonsiliasi_pembayaran/index.php" class="side-menu__item <?= str_contains($current_uri, '/rekonsiliasi_pembayaran/') ? 'active' : '' ?>">Rekonsiliasi</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php" class="side-menu__item <?= str_contains($current_uri, '/pengiriman/') ? 'active' : '' ?>">Pengiriman</a></li>
            </ul>
        </li>


        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <?php $laporan_uri = ['/laporan']; ?>
        <li class="slide has-sub <?= is_uri_match($laporan_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-bar-chart side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/penjualan.php') ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/keuangan.php" class="side-menu__item <?= str_contains($current_uri, 'keuangan') ? 'active' : '' ?>">Keuangan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/stok_barang.php" class="side-menu__item <?= str_contains($current_uri, 'stok') ? 'active' : '' ?>">Stok</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/produk_terlaris.php" class="side-menu__item <?= str_contains($current_uri, 'produk_terlaris') ? 'active' : '' ?>">Produk Terlaris</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/kinerja_sales.php" class="side-menu__item <?= str_contains($current_uri, 'kinerja_sales') ? 'active' : '' ?>">Kinerja Sales</a></li>
            </ul>
        </li>

        <!-- NOTIFIKASI -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide <?= str_contains($current_uri, '/notifikasi_stok/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/notifikasi_stok/index.php" class="side-menu__item">
                <i class="ti-alert side-menu__icon"></i>
                <span class="side-menu__label">Notifikasi Stok</span>
                <span class="badge bg-danger rounded-pill ms-auto" id="notif-stok" style="display:none;"></span>
            </a>
        </li>

    </ul>
</nav>