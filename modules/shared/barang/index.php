<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/session_start.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/koneksi.php';

// Validasi role (khusus admin)
if ($_SESSION['role'] !== 'admin') {
    header("Location: /intimart/index.php?error=unauthorized");
    exit;
}

// Data user
$role = $_SESSION['role'];
$username = $_SESSION['username'] ?? 'User';
$navbarPath = $_SERVER['DOCUMENT_ROOT'] . "/intimart/modules/$role/navbar.php";

// Ambil data barang
$query = "SELECT * FROM barang ORDER BY nama_barang ASC";
$result = $conn->query($query);

require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/views/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/views/layout/header.php';
?>

<!-- Main Content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Manajemen Data Barang</h4>
            <a href="/intimart/modules/shared/barang/add.php" class="btn btn-primary">+ Tambah Barang</a>
        </div>

        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-nowrap">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok Minimum</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $no = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                                        <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                                        <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                        <td><?= (int)$row['stok_minimum'] ?></td>
                                        <td>
                                            <a href="/intimart/modules/shared/barang/edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="#" onclick="confirmDelete('/intimart/modules/shared/barang/delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-danger">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada data barang.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/views/layout/footer.php'; ?>