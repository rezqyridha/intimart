<?php
include '../session-start.php';
include '../config.php';

// Periksa apakah `id_pegawai` telah diterima untuk dihapus
if (isset($_GET['id_stok_fisik']) && is_numeric($_GET['id_stok_fisik'])) {
    $id_stok_fisik = intval($_GET['id_stok_fisik']);

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM stok_fisik WHERE id_stok_fisik = ?");
    $stmt->bind_param("i", $id_stok_fisik);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_stok_fisik.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_stok_fisik.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_stok_fisik.php';</script>";
}

$conn->close();
