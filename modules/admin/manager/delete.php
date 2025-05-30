<?php
include '../session-start.php'; // Cek session dan pastikan pengguna login
include '../config.php';       // con$conn ke database

// Periksa apakah `id_sppt` telah diterima untuk dihapus
if (isset($_GET['id_manager']) && is_numeric($_GET['id_manager'])) {
    $id_manager = intval($_GET['id_manager']); // Memastikan ID adalah integer

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM manager WHERE id_manager = ?");
    $stmt->bind_param("i", $id_manager);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_manager.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_manager.php';</script>";
    }

    $stmt->close(); // Tutup statement
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_manager.php';</script>";
}

$conn->close();
