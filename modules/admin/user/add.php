<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=user");
    exit;
}

$username     = trim($_POST['username'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$password     = trim($_POST['password'] ?? '');
$role         = trim($_POST['role'] ?? '');

// Validasi kosong
if ($username === '' || $nama_lengkap === '' || $password === '' || $role === '') {
    header("Location: index.php?msg=kosong&obj=user");
    exit;
}

// Validasi panjang & karakter dasar
if (strlen($username) < 3 || strlen($password) < 5) {
    header("Location: index.php?msg=invalid&obj=user");
    exit;
}

// Cek duplikat username
$cek = $koneksi->prepare("SELECT id FROM user WHERE username = ?");
$cek->bind_param("s", $username);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    $cek->close();
    header("Location: index.php?msg=duplicate&obj=user");
    exit;
}
$cek->close();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Simpan ke DB
$stmt = $koneksi->prepare("INSERT INTO user (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $hashed_password, $nama_lengkap, $role);

if ($stmt->execute()) {
    header("Location: index.php?msg=added&obj=user");
} else {
    header("Location: index.php?msg=failed&obj=user");
}
exit;
