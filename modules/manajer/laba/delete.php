<?php
include '../session-start.php'; // Cek session dan pastikan pengguna login
include '../config.php';       // con$conn ke database

// Periksa apakah `id_sppt` telah diterima untuk dihapus
if (isset($_GET['id_laba_bersih']) && is_numeric($_GET['id_laba_bersih'])) {
    $id_laba_bersih = intval($_GET['id_laba_bersih']); // Memastikan ID adalah integer

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM laba_bersih WHERE id_laba_bersih = ?");
    $stmt->bind_param("i", $id_laba_bersih);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_laba_bersih.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_laba_bersih.php';</script>";
    }

    $stmt->close(); // Tutup statement
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_laba_bersih.php';</script>";
}

$conn->close();
