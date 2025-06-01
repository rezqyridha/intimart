<?php
// cek-masuk.php
session_start();
require_once "../../koneksi.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header("Location: index.php?error=empty");
    exit;
}

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Cocokkan password tanpa hash (sementara)
    if ($password === $user['password']) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect sesuai role
        switch ($user['role']) {
            case 'admin':
                header("Location: /intimart/modules/admin/dashboard.php");
                break;
            case 'manajer':
                header("Location: /intimart/modules/manajer/dashboard.php");
                break;
            case 'karyawan':
                header("Location: /intimart/modules/karyawan/dashboard.php");
                break;
            case 'sales':
                header("Location: /intimart/modules/sales/dashboard.php");
                break;
            default:
                header("Location: index.php?error=unknownrole");
        }
    } else {
        header("Location: index.php?error=wrongpass");
    }
} else {
    header("Location: index.php?error=notfound");
}
