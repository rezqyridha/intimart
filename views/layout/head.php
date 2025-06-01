<?php
/*if (!isset($_SESSION)) session_start();
$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['username'] ?? 'User';
$navbarPath = APP_PATH . "/modules/$role/navbar.php";
*/
?>

<?php
if (!isset($_SESSION)) session_start();
if (!defined('APP_PATH')) {
    define('APP_PATH', $_SERVER['DOCUMENT_ROOT'] . '/intimart');
}

$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['username'] ?? 'Pengguna';
$navbarPath = APP_PATH . "/modules/$role/navbar.php";
?>


<!DOCTYPE html>
<html lang="id" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PT. INTIBOGA MANDIRI</title>

    <!-- Favicon -->
    <link rel="icon" href="/intimart/assets/images/brand-logos/pt.jpg" type="image/x-icon">

    <!-- Styles -->
    <link href="/intimart/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/intimart/assets/css/styles.min.css" rel="stylesheet">
    <link href="/intimart/assets/css/icons.css" rel="stylesheet">
    <link href="/intimart/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <div id="loader">
        <img src="/intimart/assets/images/media/media-79.svg" alt="loading...">
    </div>
    <div class="page">