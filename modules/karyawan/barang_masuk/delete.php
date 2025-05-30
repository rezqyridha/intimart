<?php
include '../session-start.php';
include '../config.php';

// Periksa apakah `id_pegawai` telah diterima untuk dihapus
if (isset($_GET['id_masuk']) && is_numeric($_GET['id_masuk'])) {
    $id_masuk = intval($_GET['id_masuk']);

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM brg_msk WHERE id_masuk = ?");
    $stmt->bind_param("i", $id_masuk);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_barang_masuk.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_barang_masuk.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_barang_masuk.php';</script>";
}

$conn->close();
