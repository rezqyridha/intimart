<?php
require_once CONFIG_PATH . '/constants.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">

    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= APP_NAME ?></title>
    <link rel="icon" href="<?= ASSETS_URL ?>/images/logo.png" type="image/x-icon">

    <!-- CSS -->
    <link id="style" href="<?= ASSETS_URL ?>/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/styles.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/custom.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/icons.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/libs/node-waves/waves.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/libs/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/libs/@simonwep/pickr/themes/nano.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/libs/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/libs/jsvectormap/css/jsvectormap.min.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/libs/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">


    <!-- JS awal (optional preload) -->
    <script src="<?= ASSETS_URL ?>/libs/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
</head>