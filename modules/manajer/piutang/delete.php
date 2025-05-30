<?php
include '../session-start.php'; // Cek session dan pastikan pengguna login
include '../config.php';       // con$conn ke database

// Periksa apakah `id_sppt` telah diterima untuk dihapus
if (isset($_GET['id_pelanggan']) && is_numeric($_GET['id_pelanggan'])) {
    $id_pelanggan = intval($_GET['id_pelanggan']); // Memastikan ID adalah integer

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM piutang WHERE id_pelanggan = ?");
    $stmt->bind_param("i", $id_pelanggan);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_piutang.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_piutang.php';</script>";
    }

    $stmt->close(); // Tutup statement
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_piutang.php';</script>";
}

$conn->close();
