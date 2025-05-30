<?php
// session_start.php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../index.php?error=unauthorized");
    exit;
}
