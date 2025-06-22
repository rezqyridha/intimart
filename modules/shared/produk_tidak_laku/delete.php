<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $koneksi->query("DELETE FROM produk_tidak_laku WHERE id = $id");
    header("Location: index.php?msg=deleted&obj=tidaklaku");
    exit;
} else {
    header("Location: index.php?msg=invalid&obj=tidaklaku");
    exit;
}
