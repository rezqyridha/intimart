<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role     = $_SESSION['role'] ?? '';
$id_user  = $_SESSION['id_user'] ?? 0;

// Batasi akses hanya untuk sales, admin, manajer
if (!in_array($role, ['sales', 'admin', 'manajer'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Filter berdasarkan role
$where = '';
if ($role === 'sales') {
    $where = "WHERE p.id_sales = $id_user";
}

// Ambil data pemesanan + nama barang + nama sales
$sql = "
    SELECT p.*, b.nama_barang, u.nama_lengkap AS nama_sales
    FROM pemesanan p
    JOIN barang b ON p.id_barang = b.id
    JOIN user u ON p.id_sales = u.id
    $where
    ORDER BY p.tanggal_pemesanan DESC
";
$data = $koneksi->query($sql);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Pemesanan Barang</h5>
                <?php if ($role === 'sales'): ?>
                    <a href="add.php" class="btn btn-sm btn-primary"><i class="fe fe-plus"></i> Ajukan Pesanan</a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Sales</th>
                                <th>Tanggal Pesan</th>
                                <th>Tanggal Diverifikasi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $data->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= $row['jumlah'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= match ($row['status']) {
                                                                    'menunggu' => 'warning',
                                                                    'disetujui' => 'success',
                                                                    'ditolak' => 'danger',
                                                                    default => 'secondary'
                                                                } ?>"><?= ucfirst($row['status']) ?></span>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars($row['catatan'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars($row['nama_sales']) ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($row['tanggal_pemesanan'])) ?></td>
                                    <td><?= $row['tanggal_direspon'] ? date('d-m-Y H:i', strtotime($row['tanggal_direspon'])) : '-' ?></td>
                                    <td class="text-center">
                                        <?php if ($role === 'sales' && $row['status'] === 'menunggu' && $row['id_sales'] == $id_user): ?>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1"><i class="fe fe-edit"></i></a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus pemesanan ini?')" class="btn btn-sm btn-danger"><i class="fe fe-trash"></i></a>
                                        <?php elseif ($role === 'admin' && $row['status'] === 'menunggu'): ?>
                                            <a href="verifikasi.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success"><i class="fe fe-check"></i> Verifikasi</a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($data->num_rows === 0): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada data pemesanan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>