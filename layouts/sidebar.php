<?php
$role = $_SESSION['role'] ?? 'guest';
$menuFile = __DIR__ . "/menu_$role.php";
?>
<!-- Sidebar -->
<aside class="app-sidebar sticky sidebar-dark" id="sidebar">
    <!-- Sidebar Header / Logo -->
    <div class="main-sidebar-header">
        <a href="<?= BASE_URL ?>/modules/<?= $role ?>/dashboard.php" class="header-logo">
            <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="logo sidebar" style="height: 40px;">
        </a>
    </div>

    <!-- Sidebar Content / Menu -->
    <div class="main-sidebar" id="sidebar-scroll">
        <?php
        if (file_exists($menuFile)) {
            require_once $menuFile;
        } else {
            echo "<div class='text-danger p-3'>Menu untuk role <strong>$role</strong> tidak ditemukan.</div>";
        }
        ?>
    </div>
</aside>