<?php
require_once __DIR__ . '/../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
session_start();

// Redirect jika sudah login
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: " . BASE_URL . "/modules/" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

// Ambil error dari session jika ada
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

$username = $_SESSION['old_username'] ?? '';
unset($_SESSION['old_username']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $_SESSION['old_username'] = $username;

    if ($username === '' || $password === '') {
        $_SESSION['login_error'] = "Username dan Password wajib diisi.";
    } else {
        // ✅ Tambahkan kolom `foto` pada SELECT
        $stmt = $koneksi->prepare("SELECT id, username, password, nama_lengkap, role, foto FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);

        if ($stmt && $stmt->execute()) {
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    $_SESSION['login']         = true;
                    $_SESSION['id_user']       = $user['id'];
                    $_SESSION['user_id']       = $user['id']; // optional alias
                    $_SESSION['username']      = $user['username'];
                    $_SESSION['nama_lengkap']  = $user['nama_lengkap'];
                    $_SESSION['role']          = $user['role'];

                    // ✅ Pastikan foto tersimpan di session, gunakan default jika kosong
                    $_SESSION['foto'] = !empty($user['foto']) ? $user['foto'] : 'default.png';

                    header("Location: " . BASE_URL . "/modules/" . $user['role'] . "/dashboard.php");
                    exit;
                } else {
                    $_SESSION['login_error'] = "Password salah.";
                }
            } else {
                $_SESSION['login_error'] = "Username tidak ditemukan.";
            }
        } else {
            $_SESSION['login_error'] = "Terjadi kesalahan saat memproses login.";
        }
    }

    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - <?= APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= ASSETS_URL ?>/images/logo.png" type="image/x-icon">

    <link href="<?= ASSETS_URL ?>/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/icons.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/styles.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/login-custom.css" rel="stylesheet">
</head>

<body>
    <div class="login-wrapper">
        <!-- Kiri: Branding -->
        <div class="login-left text-center">
            <img src="<?= ASSETS_URL ?>/images/logo.png" alt="Logo PT Intiboga Mandiri">
            <h2 class="mt-3 fw-bold">PT Intiboga Mandiri</h2>
            <p class="mt-2 px-3">
                Selamat datang di <strong>Aplikasi Manajemen Penjualan</strong>,<br>
                <strong>Laporan Keuangan</strong>, dan <strong>Rekonsiliasi Pembayaran</strong>.<br><br>
                Sistem ini membantu Anda dalam mengelola distribusi produk secara efisien, cepat, dan akurat.
            </p>
        </div>

        <!-- Kanan: Form Login -->
        <div class="login-right">
            <h4 class="mb-4 fw-semibold">Masuk ke Akun Anda</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" name="username"
                            value="<?= htmlspecialchars($username) ?>"
                            placeholder="Masukkan username" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" name="password" id="password"
                            placeholder="Masukkan password" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                            <i id="toggleIcon" class="bi bi-eye-slash-fill"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" id="btnLogin">Masuk</button>
                </div>

                <div class="mt-3 text-center">
                    <a href="forgot-password.php">Lupa Password?</a>
                    <span class="mx-2">|</span>
                    <a href="register.php">Daftar Akun</a>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= ASSETS_URL ?>/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.classList.remove('bi-eye-slash-fill');
                toggleIcon.classList.add('bi-eye-fill');
            } else {
                password.type = 'password';
                toggleIcon.classList.remove('bi-eye-fill');
                toggleIcon.classList.add('bi-eye-slash-fill');
            }
        }

        // Efek smooth saat load
        window.addEventListener('load', () => {
            document.body.classList.add('loaded');
        });

        // Disable tombol login saat submit
        const btn = document.getElementById('btnLogin');
        if (btn) {
            btn.addEventListener('click', function() {
                btn.disabled = true;
                btn.innerText = 'Memproses...';
                btn.form.submit();
            });
        }
    </script>
</body>

</html>