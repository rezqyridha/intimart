<?php
require_once '../../../config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if (!in_array($_SESSION['role'], ['admin'])) {
    header("Location: index.php?msg=unauthorized&obj=retur");
    exit;
}

$id = $_GET['id'] ?? '';

if ($id === '' || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=retur");
    exit;
}

// Ambil detail retur dan penjualan terkait
$query = $koneksi->prepare("SELECT r.jumlah, r.id_penjualan, p.id_barang, p.jumlah AS jumlah_terjual
                            FROM retur_penjualan r
                            JOIN penjualan p ON r.id_penjualan = p.id
                            WHERE r.id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();
$query->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=retur");
    exit;
}

$id_penjualan = $data['id_penjualan'];
$retur_saat_ini = $data['jumlah'];
$jumlah_terjual = $data['jumlah_terjual'];

// Cek retur lain (selain yang akan dihapus)
$qRetur = $koneksi->query("SELECT SUM(jumlah) AS total FROM retur_penjualan WHERE id_penjualan = $id_penjualan AND id != $id");
$total_retur_lain = (int) ($qRetur->fetch_assoc()['total'] ?? 0);

// Jika total retur akan jadi tidak logis setelah penghapusan (optional sebenarnya)
if (($total_retur_lain + $retur_saat_ini) > $jumlah_terjual) {
    header("Location: index.php?msg=invalid&obj=retur");
    exit;
}

// Hapus retur
$stmt = $koneksi->prepare("DELETE FROM retur_penjualan WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=deleted&obj=retur");
} else {
    header("Location: index.php?msg=fk_blocked&obj=retur");
}
exit;
