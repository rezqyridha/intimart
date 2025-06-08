<?php
require_once CONFIG_PATH . '/constants.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Start::nav -->
<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <div class="slide-left" id="slide-left">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
        </svg>
    </div>

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
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/admin/user/index.php" class="side-menu__item">
                <i class="ti-user side-menu__icon"></i>
                <span class="side-menu__label">Manajemen User</span>
            </a>
        </li>

        <!-- GUDANG & BARANG -->
        <li class="slide__category"><span class="category-name">Gudang & Barang</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="ti-package side-menu__icon"></i>
                <span class="side-menu__label">Stok & Produk</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/barang/index.php" class="side-menu__item">Data Barang</a>
                </li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_masuk/index.php" class="side-menu__item">Barang Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_keluar/index.php" class="side-menu__item">Barang Keluar</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok/index.php" class="side-menu__item">Stok Sistem</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok_fisik/index.php" class="side-menu__item">Stok Fisik</a></li>
                <li><a href="<?= BASE_URL ?>/modules/kadaluarsa/index.php" class="side-menu__item">Kadaluarsa</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/restok/index.php" class="side-menu__item">Permintaan Restok</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item">Produk Tidak Laku</a></li>
            </ul>
        </li>

        <!-- TRANSAKSI -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="ti-shopping-cart-full side-menu__icon"></i>
                <span class="side-menu__label">Penjualan & Pembayaran</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/transaksi.php" class="side-menu__item">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item">Retur Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/riwayat.php" class="side-menu__item">Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/rekonsiliasi.php" class="side-menu__item">Rekonsiliasi</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php" class="side-menu__item">Pengiriman</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/pemesanan/index.php" class="side-menu__item">Pemesanan</a></li>
            </ul>
        </li>

        <!-- SUPPLIER & TARGET -->
        <li class="slide__category"><span class="category-name">Mitra & Target</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="ti-truck side-menu__icon"></i>
                <span class="side-menu__label">Supplier & Distribusi</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/admin/supplier/index.php" class="side-menu__item">Supplier</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/target_sales/tambah.php" class="side-menu__item">Target Sales</a></li>
                <li><a href="<?= BASE_URL ?>/modules/gudang/distribusi.php" class="side-menu__item">Distribusi</a></li>
            </ul>
        </li>

        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="ti-bar-chart side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php" class="side-menu__item">Laporan Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/keuangan.php" class="side-menu__item">Laporan Keuangan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/stok.php" class="side-menu__item">Laporan Stok</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/produk_terlaris.php" class="side-menu__item">Laporan Produk Laris</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/kinerja_sales.php" class="side-menu__item">Laporan Kinerja Sales</a></li>
            </ul>
        </li>

        <!-- NOTIFIKASI -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/notifikasi/stok_minimum.php" class="side-menu__item">
                <i class="ti-alert side-menu__icon"></i>
                <span class="side-menu__label">Stok Menipis</span>
                <span class="badge bg-danger rounded-pill ms-auto" id="notif-stok">3</span> <!-- Contoh -->
            </a>
        </li>

    </ul>

    <div class="slide-right" id="slide-right">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
        </svg>
    </div>
</nav>
<!-- End::nav -->