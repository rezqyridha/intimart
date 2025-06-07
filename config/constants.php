<?php
// =============================
// KONSTANTA UTAMA SISTEM INTIMART
// =============================

// BASE URL sistem (ubah jika folder kamu berbeda)
define('BASE_URL', '/intimart');

// URL asset (otomatis mengikuti BASE_URL)
define('ASSETS_URL', BASE_URL . '/assets');

// Path absolut sistem (untuk include file)
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
define('MODULES_PATH', ROOT_PATH . '/modules');
define('LAYOUTS_PATH', ROOT_PATH . '/layouts');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('AUTH_PATH', ROOT_PATH . '/auth');

// Nama sistem
define('APP_NAME', 'Sistem Manajemen Intimart');

// Waktu default
date_default_timezone_set('Asia/Makassar');
