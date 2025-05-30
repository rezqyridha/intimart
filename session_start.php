<?php
// session_start.php
require 'koneksi.php';
session_start();

$id_user = $_SESSION['id'];
$query = $conn->prepare("SELECT username, role FROM user WHERE id = ?");
$query->bind_param("i", $id_user);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$username = $user['username'] ?? 'Pengguna';
$role = $user['role'] ?? 'Role Tidak Diketahui';

if (!isset($_SESSION['role'])) {
    header("Location: ../index.php?error=unauthorized");
    exit;
}
