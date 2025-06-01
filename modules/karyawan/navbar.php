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
            <a href="/intimart/modules/karyawan/dashboard.php" class="side-menu__item">
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- Transaksi -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <li class="slide has-sub">
            <a href="#" class="side-menu__item">
                <i class="ti-write side-menu__icon"></i>
                <span class="side-menu__label">Form Transaksi</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child1">
                <li class="slide"><a href="/intimart/modules/shared/barang_masuk/index.php" class="side-menu__item">Barang Masuk</a></li>
                <li class="slide"><a href="/intimart/modules/karyawan/pengiriman/index.php" class="side-menu__item">Pengiriman</a></li>
            </ul>
        </li>

        <!-- Manajemen -->
        <li class="slide__category"><span class="category-name">Manajemen</span></li>
        <li class="slide has-sub">
            <a href="#" class="side-menu__item">
                <i class="ti-settings side-menu__icon"></i>
                <span class="side-menu__label">Stok</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child1">
                <li class="slide"><a href="/intimart/modules/karyawan/stok/saldo.php" class="side-menu__item">Saldo Stok</a></li>
                <li class="slide"><a href="/intimart/modules/karyawan/stok/fisik.php" class="side-menu__item">Stok Fisik</a></li>
                <li class="slide"><a href="/intimart/modules/karyawan/stok/sistem.php" class="side-menu__item">Stok Sistem</a></li>
            </ul>
        </li>

        <!-- Laporan -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <li class="slide">
            <a href="/intimart/modules/shared/laporan/stok.php" class="side-menu__item">
                <i class="fa fa-file side-menu__icon"></i>
                <span class="side-menu__label">Laporan Stok</span>
            </a>
        </li>

        <!-- Akun -->
        <li class="slide__category"><span class="category-name">Akun</span></li>
        <li class="slide">
            <a href="/intimart/logout.php" class="side-menu__item text-danger">
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