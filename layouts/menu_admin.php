<?php
require_once CONFIG_PATH . '/constants.php';
$current_page = basename($_SERVER['PHP_SELF']);
$current_uri = $_SERVER['REQUEST_URI'];

function is_uri_match(array $patterns): bool
{
    global $current_uri;
    foreach ($patterns as $pattern) {
        if (strpos($current_uri, $pattern) !== false) return true;
    }
    return false;
}
?>

<nav class="main-menu-container nav nav-pills flex-column sub-open">
    <ul class="main-menu">

        <!-- DASHBOARD -->
        <li class="slide__category"><span class="category-name">Dashboard</span></li>
        <li class="slide <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/admin/dashboard.php" class="side-menu__item">
                <i class="ti-home side-menu__icon"></i>
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>

        <!-- USER -->
        <li class="slide__category"><span class="category-name">User & Akses</span></li>
        <li class="slide <?= str_contains($current_uri, '/user/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/admin/user/index.php" class="side-menu__item">
                <i class="ti-user side-menu__icon"></i>
                <span class="side-menu__label">Manajemen User</span>
            </a>
        </li>

        <!-- MASTER -->
        <li class="slide__category"><span class="category-name">Data Master</span></li>
        <li class="slide <?= str_contains($current_uri, '/supplier/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/supplier/index.php" class="side-menu__item">
                <i class="ti-truck side-menu__icon"></i>
                <span class="side-menu__label">Supplier</span>
            </a>
        </li>
        <li class="slide <?= str_contains($current_uri, '/pelanggan/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/pelanggan/index.php" class="side-menu__item">
                <i class="ti-id-badge side-menu__icon"></i>
                <span class="side-menu__label">Pelanggan</span>
            </a>
        </li>

        <!-- MODUL KHUSUS ADMIN -->
        <li class="slide__category"><span class="category-name">Modul Khusus Admin</span></li>
        <?php
        $admin_uri = ['/admin/keuangan/', '/admin/laba/', '/admin/piutang/', '/admin/target_sales/'];
        ?>
        <li class="slide has-sub <?= is_uri_match($admin_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-settings side-menu__icon"></i>
                <span class="side-menu__label">Modul Admin</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/admin/keuangan/index.php" class="side-menu__item <?= str_contains($current_uri, '/admin/keuangan/') ? 'active' : '' ?>">Keuangan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/laba/index.php" class="side-menu__item <?= str_contains($current_uri, '/admin/laba/') ? 'active' : '' ?>">Laba</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/piutang/index.php" class="side-menu__item <?= str_contains($current_uri, '/admin/piutang/') ? 'active' : '' ?>">Piutang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/target_sales/index.php" class="side-menu__item <?= str_contains($current_uri, '/admin/target_sales/') ? 'active' : '' ?>">Target Sales</a></li>
            </ul>
        </li>

        <!-- STOK & PRODUK -->
        <li class="slide__category"><span class="category-name">Stok & Produk</span></li>
        <?php
        $stok_uri = ['/barang/', '/barang_masuk/', '/barang_keluar/', '/stok/', '/stok_fisik/', '/produk_tidak_laku/', '/kadaluarsa/', '/gudang/'];
        ?>
        <li class="slide has-sub <?= is_uri_match($stok_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-package side-menu__icon"></i>
                <span class="side-menu__label">Manajemen Stok</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/barang/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang/') ? 'active' : '' ?>">Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_masuk/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang_masuk/') ? 'active' : '' ?>">Barang Masuk</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/barang_keluar/index.php" class="side-menu__item <?= str_contains($current_uri, '/barang_keluar/') ? 'active' : '' ?>">Barang Keluar</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok/') ? 'active' : '' ?>">Stok Sistem</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/stok_fisik/index.php" class="side-menu__item <?= str_contains($current_uri, '/stok_fisik/') ? 'active' : '' ?>">Stok Fisik</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/produk_tidak_laku/index.php" class="side-menu__item <?= str_contains($current_uri, '/produk_tidak_laku/') ? 'active' : '' ?>">Produk Tidak Laku</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/kadaluarsa/index.php" class="side-menu__item <?= str_contains($current_uri, '/kadaluarsa/') ? 'active' : '' ?>">Kadaluarsa</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/gudang/index.php" class="side-menu__item <?= str_contains($current_uri, '/gudang/') ? 'active' : '' ?>">Gudang</a></li>
            </ul>
        </li>

        <!-- TRANSAKSI -->
        <li class="slide__category"><span class="category-name">Transaksi</span></li>
        <?php
        $trx_uri = ['/penjualan/', '/retur_penjualan/', '/pembayaran/', '/rekonsiliasi_pembayaran/', '/pengiriman/', '/pemesanan/', '/restock_supplier/'];
        ?>
        <li class="slide has-sub <?= is_uri_match($trx_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-credit-card side-menu__icon"></i>
                <span class="side-menu__label">Transaksi</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/penjualan/index.php" class="side-menu__item <?= str_contains($current_uri, '/penjualan/') ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/retur_penjualan/index.php" class="side-menu__item <?= str_contains($current_uri, '/retur_penjualan/') ? 'active' : '' ?>">Retur</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pembayaran/index.php" class="side-menu__item <?= str_contains($current_uri, '/pembayaran/') ? 'active' : '' ?>">Pembayaran</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/rekonsiliasi_pembayaran/index.php" class="side-menu__item <?= str_contains($current_uri, '/rekonsiliasi_pembayaran/') ? 'active' : '' ?>">Rekonsiliasi</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pengiriman/index.php" class="side-menu__item <?= str_contains($current_uri, '/pengiriman/') ? 'active' : '' ?>">Pengiriman</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/pemesanan/index.php" class="side-menu__item <?= str_contains($current_uri, '/pemesanan/') ? 'active' : '' ?>">Pemesanan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/restock_supplier/index.php" class="side-menu__item <?= str_contains($current_uri, '/restock_supplier/') ? 'active' : '' ?>">Restok Supplier</a></li>
            </ul>
        </li>

        <!-- LAPORAN -->
        <li class="slide__category"><span class="category-name">Laporan</span></li>
        <?php
        $laporan_uri = ['/laporan/penjualan', '/laporan/kas', '/laporan/stok_barang', '/laporan/piutang', '/laporan/pemesanan', '/laporan/retur_penjualan', '/laporan/produk_tidak_laku', '/laporan/target_sales', '/laporan/pengiriman', '/laporan/rekonsiliasi_pembayaran'];
        ?>
        <li class="slide has-sub <?= is_uri_match($laporan_uri) ? 'open active' : '' ?>">
            <a href="#" class="side-menu__item">
                <i class="ti-bar-chart side-menu__icon"></i>
                <span class="side-menu__label">Laporan</span>
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child2">
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/penjualan.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/penjualan') ? 'active' : '' ?>">Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/laporan/kas.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/kas') ? 'active' : '' ?>">Keuangan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/admin/laporan/rekonsiliasi_pembayaran.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/rekonsiliasi_pembayaran') ? 'active' : '' ?>">Rekonsiliasi</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/piutang.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/piutang') ? 'active' : '' ?>">Piutang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/stok_barang.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/stok_barang') ? 'active' : '' ?>">Stok Barang</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/produk_tidak_laku.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/produk_tidak_laku') ? 'active' : '' ?>">Produk Tidak Laku</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/pemesanan.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/pemesanan') ? 'active' : '' ?>">Pemesanan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/retur_penjualan.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/retur_penjualan') ? 'active' : '' ?>">Retur Penjualan</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/target_sales.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/target_sales') ? 'active' : '' ?>">Target Sales</a></li>
                <li><a href="<?= BASE_URL ?>/modules/shared/laporan/pengiriman.php" class="side-menu__item <?= str_contains($current_uri, 'laporan/pengiriman') ? 'active' : '' ?>">Pengiriman</a></li>
            </ul>
        </li>

        <!-- NOTIFIKASI -->
        <li class="slide__category"><span class="category-name">Notifikasi</span></li>
        <li class="slide <?= str_contains($current_uri, '/notifikasi_stok/') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/modules/shared/notifikasi_stok/index.php" class="side-menu__item">
                <i class="ti-alert side-menu__icon"></i>
                <span class="side-menu__label">Notifikasi Stok</span>
                <span class="badge bg-danger rounded-pill ms-auto" id="notif-stok" style="display:none;"></span>
            </a>
        </li>

    </ul>
</nav>