<?php
$role = $_SESSION['role'] ?? 'guest';
$menuFile = __DIR__ . "/menu_$role.php";
echo '<!-- FOTO SESSION DEBUG: ' . ($_SESSION['foto'] ?? 'NULL') . ' -->';
// Ambil path foto dari session
$fotoProfil = $_SESSION['foto'] ?? null;
$fotoPath = ($fotoProfil && $fotoProfil !== '')
    ? BASE_URL . '/uploads/' . $fotoProfil
    : ASSETS_URL . '/images/default-avatar.png';
?>

<header class="app-header">
    <div class="main-header-container container-fluid">

        <!-- Kiri: Logo dan tombol sidebar -->
        <div class="header-content-left">
            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="<?= MODULES_PATH ?>/<?= $role ?>/dashboard.php" class="header-logo">
                        <img src="<?= ASSETS_URL ?>/images/logo.png" alt="logo topbar" style="height: 40px;">
                    </a>
                </div>
            </div>
            <div class="header-element">
                <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);">
                    <span></span>
                </a>
            </div>
        </div>

        <!-- Kanan: Notifikasi & Profil -->
        <div class="header-content-right">
            <?php if (in_array($_SESSION['role'], ['admin', 'karyawan'])): ?>
                <!-- 🔔 Notifikasi Stok Menipis -->
                <div class="header-element dropdown">
                    <a class="header-link dropdown-toggle" href="#" id="stokNotifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fe fe-bell fs-20 position-relative">
                            <span id="notif-stok-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger text-white" style="font-size: 11px; min-width: 18px; height: 18px; line-height: 18px; padding: 0;">0</span>
                        </i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg p-0 border-0" style="width: 320px;" id="stokNotifDropdownContent">
                        <div class="px-3 pt-3 pb-2 border-bottom">
                            <h6 class="fw-semibold mb-0"><i class="ti-alert text-warning me-2"></i>Stok Menipis</h6>
                        </div>
                        <div class="notif-body max-h-300px overflow-auto" id="stokNotifBody">
                            <div class="text-muted text-center py-3">Memuat...</div>
                        </div>
                        <div class="text-center border-top py-2">
                            <a href="<?= BASE_URL ?>/modules/shared/notifikasi_stok/index.php" class="text-primary fw-semibold text-decoration-none">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Profil Pengguna -->
            <div class="header-element">
                <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="header-link-icon">
                            <img src="<?= $fotoPath ?>" alt="Profil" class="rounded-circle" width="32" height="32">
                        </div>
                    </div>
                </a>
                <ul class="main-header-dropdown dropdown-menu header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                    <li>
                        <div class="header-navheading border-bottom">
                            <h6 class="main-notification-title mb-0">Nama: <?= $_SESSION['nama_lengkap'] ?? 'User'; ?></h6>
                            <p class="main-notification-text mb-0">Posisi: <?= ucfirst($_SESSION['role']) ?? ''; ?></p>
                        </div>
                    </li>
                    <li><a class="dropdown-item d-flex border-bottom" href="<?= BASE_URL ?>/modules/shared/profile.php"><i class="fe fe-user fs-16 me-2"></i>Profil Saya</a></li>
                    <li><a class="dropdown-item d-flex" href="<?= BASE_URL ?>/auth/logout.php"><i class="fe fe-power fs-16 me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>

    </div>
</header>