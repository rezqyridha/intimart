<?php
include '../session-start.php'; // Cek session dan pastikan pengguna login
include '../config.php';       // con$conn ke database

// Periksa apakah `id_sppt` telah diterima untuk dihapus
if (isset($_GET['id_arus_kas']) && is_numeric($_GET['id_arus_kas'])) {
    $id_arus_kas = intval($_GET['id_arus_kas']); // Memastikan ID adalah integer

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM arus_kas WHERE id_arus_kas = ?");
    $stmt->bind_param("i", $id_arus_kas);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_arus_kas.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_arus_kas.php';</script>";
    }

    $stmt->close(); // Tutup statement
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_arus_kas.php';</script>";
}

$conn->close();
