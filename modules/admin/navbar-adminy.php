<!-- Start::nav -->
<nav class="main-menu-container nav nav-pills flex-column sub-open">

    <!-- Scroll Left Button -->
    <div class="slide-left" id="slide-left">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
        </svg>
    </div>

    <!-- Sidebar Menu -->
    <ul class="main-menu">

        <!-- Dashboard -->
        <li class="slide__category"><span class="category-name">DASHBOARD</span></li>
        <li class="slide">
            <a href="/intimart/modules/admin/dashboard.php" class="side-menu__item active">
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- Transaksi -->
        <li class="slide__category"><span class="category-name">TRANSAKSI</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="ti-write side-menu__icon"></i>
                <span class="side-menu__label">Transaksi</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu">
                <li><a href="pesan_barang.php" class="side-menu__item">Pemesanan Outlet</a></li>
                <li><a href="/intimart/modules/shared/barang_masuk/index.php" class="side-menu__item">Barang Masuk</a></li>
                <li><a href="retur.php" class="side-menu__item">Retur Penjualan</a></li>
                <li><a href="pengiriman.php" class="side-menu__item">Pengiriman Barang</a></li>
                <li><a href="pembayaran.php" class="side-menu__item">Pembayaran</a></li>
                <li><a href="modules/restok/tambah.php" class="side-menu__item">Restok ke Supplier</a></li>
            </ul>
        </li>

        <!-- Manajemen Data -->
        <li class="slide__category"><span class="category-name">MANAJEMEN DATA</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="ti-settings side-menu__icon"></i>
                <span class="side-menu__label">Manajemen</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu">
                <li><a href="user.php" class="side-menu__item">Data Pengguna</a></li>
                <li><a href="/intimart/modules/barang/index.php" class="side-menu__item">Data Barang</a></li>
                <li><a href="modules/supplier/index.php" class="side-menu__item">Data Supplier</a></li>
                <li><a href="modules/restok/index.php" class="side-menu__item">Data Restok</a></li>
                <li><a href="modules/kadaluarsa/index.php" class="side-menu__item">Barang Kadaluarsa</a></li>
            </ul>
        </li>

        <!-- Laporan -->
        <li class="slide__category"><span class="category-name">LAPORAN & MONITORING</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="fa fa-file side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu">
                <li><a href="laporan_penjualan.php" class="side-menu__item">Penjualan</a></li>
                <li><a href="laporan_retur.php" class="side-menu__item">Retur</a></li>
                <li><a href="laporan_keuangan.php" class="side-menu__item">Keuangan</a></li>
                <li><a href="laba_bersih.php" class="side-menu__item">Laba Rugi</a></li>
                <li><a href="arus_kas.php" class="side-menu__item">Arus Kas</a></li>
                <li><a href="laporan_terlaris.php" class="side-menu__item">Produk Terlaris</a></li>
                <li><a href="modules/produk_tidak_laku/index.php" class="side-menu__item">Produk Tidak Laku</a></li>
                <li><a href="laporan_kinerja_sales.php" class="side-menu__item">Kinerja Sales</a></li>
                <li><a href="modules/laporan_sales/index.php" class="side-menu__item">Target vs Realisasi</a></li>
            </ul>
        </li>

        <!-- Pengaturan / Utilitas -->
        <li class="slide__category"><span class="category-name">UTILITAS</span></li>
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="fe fe-settings side-menu__icon"></i>
                <span class="side-menu__label">Pengaturan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu">
                <li><a href="saldo_stok.php" class="side-menu__item">Saldo Stok</a></li>
                <li><a href="stok_fisik.php" class="side-menu__item">Stok Fisik</a></li>
                <li><a href="stok_sistem.php" class="side-menu__item">Stok Sistem</a></li>
                <li><a href="modules/target_sales/tambah.php" class="side-menu__item">Input Target Sales</a></li>
                <li><a href="modules/kadaluarsa/tambah.php" class="side-menu__item">Input Kadaluarsa</a></li>
            </ul>
        </li>

        <!-- Logout -->
        <li class="slide__category"><span class="category-name">AKUN</span></li>
        <li class="slide">
            <a href="../../logout.php" class="side-menu__item text-danger">
                <i class="ti-power-off side-menu__icon"></i>
                <span class="side-menu__label">Logout</span>
            </a>
        </li>
    </ul>

    <!-- Scroll Right Button -->
    <div class="slide-right" id="slide-right">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
            <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
        </svg>
    </div>
</nav>
<!-- End::nav -->