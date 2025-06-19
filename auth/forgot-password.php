<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
session_start();

$errors = [];
$success = false;

// Default password baru (akan di-hash)
$defaultPassword = 'user123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

    if ($username === '' || $nama_lengkap === '') {
        $errors[] = 'Semua field wajib diisi.';
    } else {
        $stmt = $koneksi->prepare("SELECT id FROM user WHERE username = ? AND nama_lengkap = ?");
        $stmt->bind_param("ss", $username, $nama_lengkap);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $hashed = password_hash($defaultPassword, PASSWORD_BCRYPT);
            $update = $koneksi->prepare("UPDATE user SET password = ? WHERE id = ?");
            $update->bind_param("si", $hashed, $user['id']);
            $update->execute();
            $success = true;
        } else {
            $errors[] = 'Data tidak ditemukan. Pastikan input sesuai.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password - Intimart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.min.css" rel="stylesheet">
    <link href="../assets/css/icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login-custom.css">
    <link rel="icon" href="../assets/images/logo.png">
</head>

<body class="loaded">
    <div class="login-wrapper">
        <!-- Kiri -->
        <div class="login-left">
            <img src="../assets/images/logo.png" alt="Logo Intimart">
            <h2>Reset Password</h2>
            <p>Gunakan formulir ini jika Anda lupa password dan ingin mengatur ulang ke default.</p>
        </div>

        <!-- Kanan -->
        <div class="login-right">
            <h4 class="mb-3">Lupa Password?</h4>
            <p class="text-muted mb-4">Masukkan <strong>Username</strong> dan <strong>Nama Lengkap</strong> Anda untuk reset password menjadi <code>user123</code>.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><i class="fe fe-alert-circle"></i>
                    <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
                </div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><i class="fe fe-check-circle"></i>
                    Password berhasil di-reset. Silakan login dengan password <strong>user123</strong>.
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input name="username" id="username" type="text" class="form-control" placeholder="Masukkan username Anda" required>
                </div>

                <div class="mb-4">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input name="nama_lengkap" id="nama_lengkap" type="text" class="form-control" placeholder="Masukkan nama lengkap sesuai akun" required>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary" type="submit">
                        <i class="fe fe-refresh-cw me-1"></i> Reset Password
                    </button>
                </div>
            </form>

            <div class="mt-4">
                <p class="mb-0">Ingat password Anda? <a href="login.php">Kembali ke Login</a></p>
            </div>
        </div>
    </div>

    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>