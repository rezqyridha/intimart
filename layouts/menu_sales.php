<?php
require_once CONFIG_PATH . '/constants.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Menu untuk Sales -->
<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <div class="slide-left" id="slide-left">
        <i class="fe fe-chevron-left"></i>
    </div>

    <ul class="main-menu">

        <!-- Dashboard -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= str_contains($currentPath, '/modules/sales/dashboard.php') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/sales/dashboard.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="bi bi-house-door-fill side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- Transaksi -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <li class="slide has-sub <?= str_contains($currentPath, '/penjualan') || str_contains($currentPath, '/retur_penjualan') ? 'active' : '' ?>">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="bi bi-cart-check side-menu__icon"></i>
                <span class="side-menu__label">Penjualan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/penjualan/transaksi.php"
                        class="side-menu__item <?= str_contains($currentPath, 'penjualan/transaksi.php') ? 'active' : '' ?>">
                        Input Penjualan
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php"
                        class="side-menu__item <?= str_contains($currentPath, 'retur_penjualan') ? 'active' : '' ?>">
                        Retur Penjualan
                    </a>
                </li>
            </ul>
        </li>

        <!-- Evaluasi Produk -->
        <li class="slide__category"><span class="category-name">Evaluasi</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php"
                class="side-menu__item <?= str_contains($currentPath, 'produk_tidak_laku') ? 'active' : '' ?>">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="bi bi-archive side-menu__icon"></i>
                <span class="side-menu__label">Produk Tidak Laku</span>
            </a>
        </li>

        <!-- Pengiriman -->
        <li class="slide__category"><span class="category-name">Distribusi</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php"
                class="side-menu__item <?= str_contains($currentPath, 'pengiriman') ? 'active' : '' ?>">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="bi bi-truck side-menu__icon"></i>
                <span class="side-menu__label">Riwayat Pengiriman</span>
            </a>
        </li>

        <!-- Laporan -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <li class="slide has-sub <?= str_contains($currentPath, '/laporan') ? 'active' : '' ?>">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="bi bi-bar-chart-line-fill side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php"
                        class="side-menu__item <?= str_contains($currentPath, 'laporan/penjualan.php') ? 'active' : '' ?>">
                        Laporan Penjualan
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/laporan/keuangan.php"
                        class="side-menu__item <?= str_contains($currentPath, 'laporan/keuangan.php') ? 'active' : '' ?>">
                        Laporan Keuangan
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/modules/shared/laporan/stok.php"
                        class="side-menu__item <?= str_contains($currentPath, 'laporan/stok.php') ? 'active' : '' ?>">
                        Laporan Stok
                    </a>
                </li>
            </ul>
        </li>

        <!-- Notifikasi -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide">
            <a href="<?= BASE_URL ?>/modules/notifikasi/stok_minimum.php"
                class="side-menu__item <?= str_contains($currentPath, 'stok_minimum') ? 'active' : '' ?>">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="bi bi-exclamation-diamond-fill side-menu__icon"></i>
                <span class="side-menu__label">Stok Menipis</span>
                <span class="badge bg-danger ms-auto" id="notif-stok">3</span>
            </a>
        </li>

    </ul>

    <div class="slide-right" id="slide-right">
        <i class="fe fe-chevron-right"></i>
    </div>
</nav>
<!-- End::nav -->