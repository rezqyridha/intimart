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

        <!-- Dashboard -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= str_contains($current_page, 'dashboard.php') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/karyawan/dashboard.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- Barang & Gudang -->
        <li class="slide__category"><span class="category-name">Gudang</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-package side-menu__icon"></i>
                <span class="side-menu__label">Barang & Gudang</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/barang/index.php" class="side-menu__item">Lihat Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_masuk/index.php" class="side-menu__item">Tambah Barang Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_keluar/index.php" class="side-menu__item">Tambah Barang Keluar</a></li>
                <li><a href="<?= BASE_URL ?>/modules/karyawan/stok/fisik.php" class="side-menu__item">Update Stok Fisik</a></li>
                <li><a href="<?= BASE_URL ?>/modules/karyawan/stok/sistem.php" class="side-menu__item">Update Stok Sistem</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/kadaluarsa/index.php" class="side-menu__item">Tambah Kadaluarsa</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/restok/index.php" class="side-menu__item">Ajukan Restok</a></li>
            </ul>
        </li>

        <!-- Transaksi -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-shopping-cart-full side-menu__icon"></i>
                <span class="side-menu__label">Penjualan & Retur</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/transaksi.php" class="side-menu__item">Lihat Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item">Lihat Retur</a></li>
            </ul>
        </li>

        <!-- Pengiriman & Pembayaran -->
        <li class="slide__category"><span class="category-name">Distribusi</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-truck side-menu__icon"></i>
                <span class="side-menu__label">Pengiriman & Pembayaran</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php" class="side-menu__item">Lihat Pengiriman</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/riwayat.php" class="side-menu__item">Lihat Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/gudang/distribusi.php" class="side-menu__item">Input Distribusi</a></li>
            </ul>
        </li>

        <!-- Mitra -->
        <li class="slide__category"><span class="category-name">Mitra</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/admin/supplier/index.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-user side-menu__icon"></i>
                <span class="side-menu__label">Supplier</span>
            </a>
        </li>

        <!-- Produk Evaluasi -->
        <li class="slide__category"><span class="category-name">Evaluasi Produk</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-close side-menu__icon"></i>
                <span class="side-menu__label">Produk Tidak Laku</span>
            </a>
        </li>

        <!-- Laporan -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/shared/laporan/stok.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-bar-chart side-menu__icon"></i>
                <span class="side-menu__label">Laporan Stok</span>
            </a>
        </li>

        <!-- Notifikasi -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/notifikasi/stok_minimum.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-alert side-menu__icon"></i>
                <span class="side-menu__label">Stok Tipis</span>
                <span class="badge bg-danger ms-auto">!</span>
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