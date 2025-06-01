<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';
require_once APP_PATH . '/koneksi.php';
require_once APP_PATH . '/views/layout/head.php';
require_once APP_PATH . '/views/layout/header.php';

// Cek hak akses
$role = $_SESSION['role'];
$userId = $_SESSION['id'];

// Query data barang masuk
if ($role === 'admin') {
    $query = "SELECT bm.*, b.nama_barang, b.satuan
              FROM barang_masuk bm
              JOIN barang b ON bm.id_barang = b.id
              ORDER BY bm.tanggal DESC";
    $stmt = $conn->prepare($query);
} else {
    $query = "SELECT bm.*, b.nama_barang, b.satuan
              FROM barang_masuk bm
              JOIN barang b ON bm.id_barang = b.id
              WHERE bm.id_user = ?
              ORDER BY bm.tanggal DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
}


$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Barang Masuk</h4>
            <a href="add.php" class="btn btn-primary">+ Tambah Barang Masuk</a>
        </div>

        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-nowrap">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Tanggal</th>
                                <?php if ($role === 'admin'): ?>
                                    <th width="130">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $no = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= (int)$row['jumlah'] ?></td>
                                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                                        <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['tanggal']))) ?></td>
                                        <?php if ($role === 'admin'): ?>
                                            <td>
                                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="#" onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-danger">Hapus</a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $role === 'admin' ? 6 : 5 ?>" class="text-center text-muted">Belum ada data.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- End::app-content -->

<?php require_once APP_PATH . '/views/layout/footer.php'; ?>