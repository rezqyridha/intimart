<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Desa Baluti</title>
    <link rel="icon" href="assets/images/desa/Logo Desa.png" type="image/x-icon">

    <!-- CSS Offline -->
    <link href="assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/icons.css" rel="stylesheet">
    <link href="assets/css/styles.min.css" rel="stylesheet">

    <style>
        body {
            background: #e2ecf4;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            display: flex;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
        }

        .login-left {
            background: #2563eb;
            color: #fff;
            padding: 2.5rem;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .login-left img {
            width: 100px;
            margin-bottom: 1.2rem;
        }

        .login-left h2 {
            font-weight: 700;
        }

        .login-right {
            width: 50%;
            padding: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
        }

        .form-control {
            border-radius: 6px;
        }

        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
            border-radius: 6px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .input-group-text {
            background-color: #e5e7eb;
            border: none;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 90%;
            }

            .login-left,
            .login-right {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <!-- Kiri: Logo dan Judul -->
        <div class="login-left text-center">
            <img src="assets/images/desa/Logo Desa.png" alt="Logo Desa">
            <h2>Desa Baluti</h2>
            <p>Selamat datang di sistem administrasi</p>
        </div>

        <!-- Kanan: Form Login -->
        <div class="login-right">
            <h4 class="mb-4">Masuk ke Akun Anda</h4>
            <form action="cek-masuk.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                            <i id="toggleIcon" class="bi bi-eye-slash-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Masuk</button>
                </div>
                <div class="mt-3 text-center">
                    <a href="forgot-password.php">Lupa Password?</a> |
                    <a href="register.php">Daftar Akun</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Offline -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
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
    </script>

</body>

</html>