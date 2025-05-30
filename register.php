<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Desa Baluti - Login</title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

    <!-- Favicon -->
    <link rel="icon" href="assets/images/desa/Logo Desa.png" type="image/x-icon">

    <!-- Authentication-main Js -->
    <script src="assets/js/authentication-main.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Style Css -->
    <link href="assets/css/styles.min.css" rel="stylesheet">

    <!-- Icons Css -->
    <link href="assets/css/icons.css" rel="stylesheet">

</head>

<body class="error-1">

    <div class="page main-signin-wrapper">

        <!-- Start::row-1 -->
        <div class="row signpages text-center">
            <div class="col-md-12">
                <div class="card mb-0 col-8" style="margin: 0 auto;">
                    <div class="card-body">
                        <div class="clearfix"></div>
                        <form action="cek-masuk.php" method="post">
                            <img src="assets/images/desa/Logo Desa.png" class="ht-100 mb-2" alt="user" style="width: 90px;">
                            <h3 class="text-center mb-4">Buat Akun</h3>
                            <div class="input-group mb-4">
                                <div class="input-group-text"><i class="fa fa-user"></i></div>
                                <input class="form-control" placeholder="Masukkan Username" type="username" name="username">
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-text"><i class="fa fa-lock"></i></div>
                                <input class="form-control" placeholder="Masukkan Password" type="password" id="password" name="password">
                                <button type="button" class="btn btn-light" onclick="togglePassword()">
                                    <i id="toggleIcon" class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Daftar</button>
                            </div>
                        </form>
                        <div class="text-start mt-4 ms-0">
                            <div>Sudah punya akun? Silahkan <a href="index.php">masuk</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End::row-1 -->

    </div>

    <!-- Custom-Switcher JS -->
    <script src="assets/js/custom-switcher.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Tampil Password -->
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                password.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>

</html>