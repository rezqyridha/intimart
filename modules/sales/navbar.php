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
            <a href="/intimart/modules/sales/dashboard.php" class="side-menu__item">
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- Transaksi -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <li class="slide">
            <a href="/intimart/modules/sales/penjualan/input.php" class="side-menu__item">
                <i class="ti-shopping-cart side-menu__icon"></i>
                <span class="side-menu__label">Penjualan</span>
            </a>
        </li>
        <li class="slide">
            <a href="/intimart/modules/sales/retur/index.php" class="side-menu__item">
                <i class="ti-back-left side-menu__icon"></i>
                <span class="side-menu__label">Retur Penjualan</span>
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