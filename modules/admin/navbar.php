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
        <li class="slide">
            <a href="dashboard.php" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- Form -->
        <li class="slide__category"><span class="category-name">Form</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-write side-menu__icon"></i>
                <span class="side-menu__label">Form Transaksi</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child1">
                <li class="slide"><a href="pesan_barang.php" class="side-menu__item">Pemesanan Barang</a></li>
                <li class="slide"><a href="barang_masuk.php" class="side-menu__item">Barang Masuk</a></li>
                <li class="slide"><a href="saldo_stok.php" class="side-menu__item">Saldo Stok</a></li>
                <li class="slide"><a href="stok_fisik.php" class="side-menu__item">Stok Fisik</a></li>
                <li class="slide"><a href="stok_sistem.php" class="side-menu__item">Stok Sistem</a></li>
                <li class="slide"><a href="laba_bersih.php" class="side-menu__item">Laba Rugi</a></li>
                <li class="slide"><a href="arus_kas.php" class="side-menu__item">Arus Kas</a></li>
            </ul>
        </li>

        <!-- Manajemen -->
        <li class="slide__category"><span class="category-name">Manajemen</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="ti-settings side-menu__icon"></i>
                <span class="side-menu__label">Manajemen</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child1">
                <li class="slide"><a href="user.php" class="side-menu__item">Data User</a></li>
                <li class="slide"><a href="barang.php" class="side-menu__item">Data Barang</a></li>
                <li class="slide"><a href="pengiriman.php" class="side-menu__item">Pengiriman</a></li>
                <li class="slide"><a href="pembayaran.php" class="side-menu__item">Pembayaran</a></li>
                <li class="slide"><a href="retur.php" class="side-menu__item">Retur Penjualan</a></li>
            </ul>
        </li>

        <!-- Laporan -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <span class="shape1"></span><span class="shape2"></span>
                <i class="fa fa-file side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li class="slide"><a href="laporan_penjualan.php" class="side-menu__item">Laporan Penjualan</a></li>
                <li class="slide"><a href="laporan_retur.php" class="side-menu__item">Laporan Retur</a></li>
                <li class="slide"><a href="laporan_kinerja_sales.php" class="side-menu__item">Kinerja Sales</a></li>
                <li class="slide"><a href="laporan_terlaris.php" class="side-menu__item">Produk Terlaris</a></li>
                <li class="slide"><a href="laporan_keuangan.php" class="side-menu__item">Laporan Keuangan</a></li>
            </ul>
        </li>

        <!-- Akun -->
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