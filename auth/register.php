<?php
require_once '../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
session_start();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username       = trim($_POST['username'] ?? '');
    $nama_lengkap   = trim($_POST['nama_lengkap'] ?? '');
    $password       = $_POST['password'] ?? '';
    $confirm_pass   = $_POST['confirm_password'] ?? '';
    $role           = $_POST['role'] ?? '';

    // Validasi
    if ($username === '' || $nama_lengkap === '' || $password === '' || $confirm_pass === '' || $role === '') {
        $errors[] = 'Semua field wajib diisi.';
    } elseif ($password !== $confirm_pass) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    } else {
        $cek = $koneksi->prepare("SELECT id FROM user WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $errors[] = 'Username sudah digunakan.';
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $koneksi->prepare("INSERT INTO user (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed, $nama_lengkap, $role);
        if ($stmt->execute()) {
            header("Location: login.php?msg=registered");
            exit;
        } else {
            $errors[] = 'Gagal menyimpan data.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi Intimart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.min.css" rel="stylesheet">
    <link href="../assets/css/icons.css" rel="stylesheet">
    <link href="../assets/css/register-custom.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/logo.png">
</head>

<body class="loaded">

    <div class="register-wrapper">
        <!-- Kiri: Branding -->
        <div class="register-left">
            <img src="../assets/images/logo.png" alt="Logo Intimart">
            <h4>PT Inti Boga Mandiri</h4>
            <p>Buat akun baru Anda di sistem Intimart</p>
        </div>

        <!-- Kanan: Form -->
        <div class="register-right">
            <h5 class="mb-2">Daftar Akun</h5>
            <p class="mb-4 text-muted">Silakan lengkapi form berikut.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control"
                        placeholder="Masukkan nama lengkap Anda" required
                        value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                        placeholder="Pilih username unik" required
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>

                <div class="mb-3 position-relative">
                    <label class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Buat password yang kuat" required>
                    <span class="toggle-password" onclick="togglePassword('password', this)">
                        <i class="fe fe-eye-off"></i>
                    </span>
                </div>

                <div class="mb-3 position-relative">
                    <label class="form-label">Ulangi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                        placeholder="Ketik ulang password Anda" required>
                    <span class="toggle-password" onclick="togglePassword('confirm_password', this)">
                        <i class="fe fe-eye-off"></i>
                    </span>
                </div>

                <div class="mb-4">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="manajer" <?= ($_POST['role'] ?? '') === 'manajer' ? 'selected' : '' ?>>Manajer</option>
                        <option value="karyawan" <?= ($_POST['role'] ?? '') === 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                        <option value="sales" <?= ($_POST['role'] ?? '') === 'sales' ? 'selected' : '' ?>>Sales</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-user-plus me-1"></i> Buat Akun
                    </button>
                </div>
            </form>

            <div class="text-start mt-4">
                <p class="mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id, el) {
            const input = document.getElementById(id);
            const icon = el.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fe-eye', isHidden);
            icon.classList.toggle('fe-eye-off', !isHidden);
        }
    </script>

    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>