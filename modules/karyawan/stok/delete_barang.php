<?php
include '../session-start.php';
include '../config.php';

// Periksa apakah `id_pegawai` telah diterima untuk dihapus
if (isset($_GET['id_stokperiode']) && is_numeric($_GET['id_stokperiode'])) {
    $id_stokperiode = intval($_GET['id_stokperiode']);

    // Siapkan query untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM stokbrg_periode WHERE id_stokperiode = ?");
    $stmt->bind_param("i", $id_stokperiode);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil dihapus.'); window.location.href='data_saldo_stok.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data: " . $stmt->error . "'); window.location.href='data_saldo_stok.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID tidak ditemukan atau tidak valid.'); window.location.href='data_saldo_stok.php';</script>";
}

$conn->close();
