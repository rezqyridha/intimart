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
    <style>
        .notif-body .notif-item {
            padding: 8px 16px;
            border-bottom: 1px solid #f1f1f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .notif-body .notif-item:hover {
            background-color: #f8f9fc;
        }

        .notif-body .stok-badge {
            font-weight: bold;
            color: #dc3545;
            /* merah */
        }

        .notif-body.max-h-300px {
            max-height: 300px;
            overflow-y: auto;
        }

        /* Optional: custom scrollbar */
        .notif-body::-webkit-scrollbar {
            width: 5px;
        }

        .notif-body::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 3px;
        }

        .highlight-row {
            animation: fadeHighlight 2.5s ease-in-out;
        }

        @keyframes fadeHighlight {
            0% {
                background-color: #fff3cd;
            }

            50% {
                background-color: #ffeeba;
            }

            100% {
                background-color: transparent;
            }
        }
    </style>

</head>