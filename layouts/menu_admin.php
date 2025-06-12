<?php
require_once CONFIG_PATH . '/constants.php';
$current_page = basename($_SERVER['PHP_SELF']);
$current_uri = $_SERVER['REQUEST_URI'];
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
        <li class="slide__category"><span class="category-name">User & Hak Akses</span></li>
        <li class="slide <?= str_contains($current_uri, '/user/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/admin/user/index.php" class="side-menu__item">
                <i class="ti-user side-menu__icon"></i>
                <span class="side-menu__label">Manajemen User</span>
            </a>
        </li>

        <!-- MASTER DATA -->
        <li class="slide__category"><span class="category-name">Master Data</span></li>
        <?php $is_master = str_contains($current_uri, '/pelanggan') || str_contains($current_uri, '/supplier'); ?>
        <li class="slide has-sub <?= $is_master ? 'open' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-briefcase side-menu__icon"></i>
                <span class="side-menu__label">Data Pelengkap</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/pelanggan/index.php" class="side-menu__item <?= str_contains($current_uri, '/pelanggan/') ? 'active' : '' ?>">Pelanggan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/supplier/index.php" class="side-menu__item <?= str_contains($current_uri, '/supplier/') ? 'active' : '' ?>">Supplier</a></li>
            </ul>
        </li>

        <!-- GUDANG & BARANG -->
        <li class="slide__category"><span class="category-name">Gudang & Produk</span></li>
        <?php $is_stok = str_contains($current_uri, '/barang') || str_contains($current_uri, '/stok') || str_contains($current_uri, '/produk_tidak_laku'); ?>
        <li class="slide has-sub <?= $is_stok ? 'open' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-package side-menu__icon"></i>
                <span class="side-menu__label">Stok & Produk</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/barang/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang/') ? 'active' : '' ?>">Data Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_masuk/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang_masuk/') ? 'active' : '' ?>">Barang Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_keluar/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang_keluar/') ? 'active' : '' ?>">Barang Keluar</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok/index.php') ? 'active' : '' ?>">Stok Sistem</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok_fisik/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok_fisik/') ? 'active' : '' ?>">Stok Fisik</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item <?= str_contains($current_uri, '/produk_tidak_laku/') ? 'active' : '' ?>">Produk Tidak Laku</a></li>
            </ul>
        </li>

        <!-- TRANSAKSI -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <?php $is_trans = str_contains($current_uri, '/penjualan') || str_contains($current_uri, '/retur_penjualan') || str_contains($current_uri, '/pembayaran') || str_contains($current_uri, '/pengiriman') || str_contains($current_uri, '/rekonsiliasi_pembayaran'); ?>
        <li class="slide has-sub <?= $is_trans ? 'open' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-shopping-cart-full side-menu__icon"></i>
                <span class="side-menu__label">Penjualan & Pengiriman</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/index.php" class="side-menu__item <?= str_contains($current_uri, '/penjualan/') ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item <?= str_contains($current_uri, '/retur_penjualan/') ? 'active' : '' ?>">Retur Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/index.php" class="side-menu__item <?= str_contains($current_uri, '/pembayaran/') ? 'active' : '' ?>">Riwayat Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/rekonsiliasi_pembayaran/index.php" class="side-menu__item <?= str_contains($current_uri, '/rekonsiliasi_pembayaran/') ? 'active' : '' ?>">Rekonsiliasi Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php" class="side-menu__item <?= str_contains($current_uri, '/pengiriman/') ? 'active' : '' ?>">Pengiriman</a></li>
            </ul>
        </li>

        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <?php $is_laporan = str_contains($current_uri, '/laporan'); ?>
        <li class="slide has-sub <?= $is_laporan ? 'open' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-bar-chart side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/penjualan') ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/keuangan.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/keuangan') ? 'active' : '' ?>">Keuangan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/stok.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/stok') ? 'active' : '' ?>">Stok</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/produk_terlaris.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/produk_terlaris') ? 'active' : '' ?>">Produk Terlaris</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/kinerja_sales.php" class="side-menu__item <?= str_contains($current_uri, '/laporan/kinerja_sales') ? 'active' : '' ?>">Kinerja Sales</a></li>
            </ul>
        </li>

        <!-- NOTIFIKASI -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <!-- <li class="slide <?= str_contains($current_uri, '/notifikasi_stok/index.php') ? 'active' : '' ?>">-->
        <a href="<?= BASE_URL ?>/modules/shared/notifikasi_stok/index.php" class="side-menu__item">
            <i class="ti-alert side-menu__icon"></i>
            <span class="side-menu__label">Stok Menipis</span>
            <span class="badge bg-danger rounded-pill ms-auto" id="notif-stok" style="display:none;"></span>
        </a>
        </li>

    </ul>
</nav>