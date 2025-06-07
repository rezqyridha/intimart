<?php
require_once CONFIG_PATH . '/constants.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Start::nav -->
<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <div class="slide-left" id="slide-left">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
        </svg>
    </div>

    <ul class="main-menu">
        <!-- Dashboard -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/manajer/dashboard.php" class="side-menu__item">
                <i class="bi bi-house-door-fill side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- Barang & Gudang -->
        <li class="slide__category"><span class="category-name">Gudang</span></li>
        <li class="slide has-sub">
            <a href="#" class="side-menu__item">
                <i class="bi bi-box side-menu__icon"></i>
                <span class="side-menu__label">Barang</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/barang/index.php" class="side-menu__item">Data Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_masuk/index.php" class="side-menu__item">Barang Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_keluar/index.php" class="side-menu__item">Barang Keluar</a></li>
                <li><a href="<?= BASE_URL ?>/modules/kadaluarsa/index.php" class="side-menu__item">Kadaluarsa</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/restok/index.php" class="side-menu__item">Permintaan Restok</a></li>
            </ul>
        </li>

        <!-- Penjualan -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <li class="slide has-sub">
            <a href="#" class="side-menu__item">
                <i class="bi bi-cart-check side-menu__icon"></i>
                <span class="side-menu__label">Penjualan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/transaksi.php" class="side-menu__item">Data Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item">Retur Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/riwayat.php" class="side-menu__item">Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/rekonsiliasi.php" class="side-menu__item">Rekonsiliasi</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php" class="side-menu__item">Pengiriman</a></li>
            </ul>
        </li>

        <!-- Supplier & Target -->
        <li class="slide__category"><span class="category-name">Mitra</span></li>
        <li class="slide has-sub">
            <a href="#" class="side-menu__item">
                <i class="bi bi-truck side-menu__icon"></i>
                <span class="side-menu__label">Supplier & Sales</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/admin/supplier/index.php" class="side-menu__item">Supplier</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/target_sales/tambah.php" class="side-menu__item">Target Sales</a></li>
                <li><a href="<?= BASE_URL ?>/modules/gudang/distribusi.php" class="side-menu__item">Distribusi</a></li>
            </ul>
        </li>

        <!-- Laporan -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <li class="slide has-sub">
            <a href="#" class="side-menu__item">
                <i class="bi bi-bar-chart-line side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php" class="side-menu__item">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/keuangan.php" class="side-menu__item">Keuangan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/stok.php" class="side-menu__item">Stok</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/produk_terlaris.php" class="side-menu__item">Produk Terlaris</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/kinerja_sales.php" class="side-menu__item">Kinerja Sales</a></li>
            </ul>
        </li>

        <!-- Notifikasi -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/notifikasi/stok_minimum.php" class="side-menu__item d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-exclamation-triangle-fill side-menu__icon"></i>
                    Notifikasi Stok Tipis
                </span>
                <span class="badge bg-danger rounded-pill" id="notif-stok">3</span>
            </a>
        </li>
    </ul>

    <div class="slide-right" id="slide-right">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
        </svg>
    </div>
</nav>