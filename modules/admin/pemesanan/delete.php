<?php
include '../session-start.php';
include '../config.php';

// Periksa apakah `id_pegawai` telah diterima untuk dihapus
if (isset($_GET['id_pesan']) && is_numeric($_GET['id_pesan'])) {
    $id_pesan = intval($_GET['id_pesan']);

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM pesan_barang WHERE id_pesan = ?");
    $stmt->bind_param("i", $id_pesan);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_pemesanan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_pemesanan.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_pemesanan.php';</script>";
}

$conn->close();
