<?php
include '../session-start.php';
include '../config.php';

// Periksa apakah `id_pegawai` telah diterima untuk dihapus
if (isset($_GET['id_penjualan']) && is_numeric($_GET['id_penjualan'])) {
    $id_penjualan = intval($_GET['id_penjualan']);

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM penjualan WHERE id_penjualan = ?");
    $stmt->bind_param("i", $id_penjualan);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_barang_keluar.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_barang_keluar.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_barang_keluar.php';</script>";
}

$conn->close();
