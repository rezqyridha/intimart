<?php
require '../../koneksi.php';

?>

<!-- Start::nav -->
<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <div class="slide-left" id="slide-left">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
        </svg>
    </div>
    <ul class="main-menu">
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide">
            <a href="dashboard.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <li class="slide__category"><span class="category-name">Manajemen</span></li>
        <li class="slide">
            <a href="barang.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-package side-menu__icon"></i>
                <span class="side-menu__label">Barang</span>
            </a>
        </li>
        <li class="slide">
            <a href="stok.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-pulse side-menu__icon"></i>
                <span class="side-menu__label">Stok</span>
            </a>
        </li>


        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="fa fa-file side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li class="slide side-menu__label1">
                    <a href="javascript:void(0)">Laporan</a>
                </li>
                <li class="slide">
                    <a href="laporan_penjualan.php" class="side-menu__item">Laporan Penjualan</a>
                </li>
                <li class="slide">
                    <a href="laporan_retur.php" class="side-menu__item">Laporan Retur</a>
                </li>
                <li class="slide">
                    <a href="laporan_kinerja.php" class="side-menu__item">Laporan Kinerja Sales</a>
                </li>
                <li class="slide">
                    <a href="laporan_terlaris.php" class="side-menu__item">Produk Terlaris</a>
                </li>
            </ul>
        </li>

        <li class="slide__category"><span class="category-name">Akun</span></li>
        <li class="slide">
            <a href="../../logout.php" class="side-menu__item text-danger">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-power-off side-menu__icon"></i>
                <span class="side-menu__label">Logout</span>
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