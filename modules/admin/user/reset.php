<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=user");
    exit;
}

$id = intval($_POST['id'] ?? 0);
$loginId = $_SESSION['user_id'] ?? 0;

if ($id <= 0 || $id === $loginId) {
    header("Location: index.php?msg=invalid&obj=user");
    exit;
}

// Cek user ada
$cek = $koneksi->prepare("SELECT id FROM user WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    $cek->close();
    header("Location: index.php?msg=invalid&obj=user");
    exit;
}
$cek->close();

// Set password default
$password_default = 'user123';
$hash = password_hash($password_default, PASSWORD_DEFAULT);

$stmt = $koneksi->prepare("UPDATE user SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hash, $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=reset&obj=user");
} else {
    header("Location: index.php?msg=failed&obj=user");
}
exit;
