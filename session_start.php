<?php
define('APP_PATH', $_SERVER['DOCUMENT_ROOT'] . '/intimart');

// Start session jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once APP_PATH . '/koneksi.php';

// Cek login
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: /intimart/index.php?error=unauthorized");
    exit;
}

// Ambil info user dari DB
$id_user = $_SESSION['id'];
$query = $conn->prepare("SELECT username, role FROM user WHERE id = ?");
$query->bind_param("i", $id_user);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Set variabel global
$username = $user['username'] ?? 'Pengguna';
$role = $user['role'] ?? 'Role Tidak Diketahui';
