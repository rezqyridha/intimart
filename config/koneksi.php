<?php
// =============================
// KONEKSI DATABASE INTIMART
// =============================

$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'intimart'; // pastikan sesuai dengan nama database kamu

$koneksi = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi ke database gagal: " . $koneksi->connect_error);
}

// Set charset ke UTF-8
$koneksi->set_charset("utf8");

// Gunakan koneksi di seluruh sistem via: global $koneksi;
