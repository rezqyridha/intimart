<?php
session_start();

// Include konstanta & koneksi
require_once __DIR__ . '/../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';

// Jika belum login, redirect ke halaman login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

// Mapping role dan folder modul yang boleh diakses
$role_access_map = [
    'admin'    => ['admin', 'shared'],
    'karyawan' => ['karyawan', 'shared'],
    'sales'    => ['sales', 'shared'],
    'manajer'  => ['manajer', 'shared'],
];

// Cek apakah role saat ini cocok dengan folder modul yang diakses
$current_role = $_SESSION['role'] ?? null;
$current_path = $_SERVER['PHP_SELF'];
$allowed_paths = $role_access_map[$current_role] ?? [];

$is_allowed = false;
foreach ($allowed_paths as $allowed_folder) {
    if (strpos($current_path, "/modules/$allowed_folder/") !== false) {
        $is_allowed = true;
        break;
    }
}

if (!$is_allowed) {
    // Jika akses tidak sesuai role, redirect ke halaman notfound
    header("Location: " . BASE_URL . "/notfound.php");
    exit;
}
