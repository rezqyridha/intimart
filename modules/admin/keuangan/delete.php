<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=kas");
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $stmt = $koneksi->prepare("DELETE FROM kas WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=deleted&obj=kas");
        exit;
    } else {
        header("Location: index.php?msg=fk_blocked&obj=kas");
        exit;
    }
} else {
    header("Location: index.php?msg=invalid&obj=kas");
    exit;
}
