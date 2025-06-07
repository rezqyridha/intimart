<!-- Header -->
<header class="app-header">
    <div class="main-header-container container-fluid">
        <div class="header-content-left">
            <div class="header-element">
                <!--<a href="#" class="header-logo">
                    <img src="/intimart/assets/images/brand-logos/pt.jpg" class="desktop-logo" alt="logo">
                </a>-->
            </div>
            <div class="header-element">
                <a class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" href="#"><span></span></a>
            </div>
        </div>
        <div class="header-content-right">
            <div class="header-element">
                <a href="#" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown">
                    <div class="d-flex align-items-center">
                        <div class="header-link-icon">
                            <img src="/intimart/assets/images/faces/1.jpg" alt="img" width="32" height="32" class="rounded-circle">
                        </div>
                    </div>
                </a>
                <ul class="main-header-dropdown dropdown-menu pt-0 dropdown-menu-end">
                    <li>
                        <div class="header-navheading border-bottom">
                            <h6 class="main-notification-title">Jabatan : <?= htmlspecialchars($role) ?></h6>
                            <p class="main-notification-text mb-0">Username : <?= htmlspecialchars($username) ?></p>
                        </div>
                    </li>
                    <li><a class="dropdown-item d-flex" href="/intimart/logout.php"><i class="fe fe-power me-2"></i>Log Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- Sidebar -->
<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header">
        <a href="#" class="header-logo">
            <img src="/intimart/assets/images/brand-logos/pt.jpg" class="desktop-logo" alt="logo">
        </a>
    </div>
    <div class="main-sidebar" id="sidebar-scroll">
        <?php
        if (isset($navbarPath) && file_exists($navbarPath)) {
            include $navbarPath;
        } else {
            echo "<div class='p-3 text-danger'>Navbar tidak tersedia.</div>";
        }
        ?>
    </div>
</aside>