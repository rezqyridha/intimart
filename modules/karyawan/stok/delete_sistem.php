<?php
include '../session-start.php'; // Cek session dan pastikan pengguna login
include '../config.php';       // con$conn ke database

// Periksa apakah `id_sppt` telah diterima untuk dihapus
if (isset($_GET['id_stok_sistem']) && is_numeric($_GET['id_stok_sistem'])) {
    $id_stok_sistem = intval($_GET['id_stok_sistem']); // Memastikan ID adalah integer

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM stok_sistem WHERE id_stok_sistem = ?");
    $stmt->bind_param("i", $id_stok_sistem);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_stok_sistem.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_stok_sistem.php';</script>";
    }

    $stmt->close(); // Tutup statement
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_stok_sistem.php';</script>";
}

$conn->close();
