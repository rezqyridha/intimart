<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

// Role yang diizinkan
$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'karyawan'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Ambil data barang kadaluarsa
$query = "
    SELECT bk.*, b.nama_barang, b.satuan 
    FROM barang_kadaluarsa bk
    JOIN barang b ON bk.id_barang = b.id
    ORDER BY bk.tanggal_expired ASC
";
$result = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <div class="card custom-card mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Manajemen Data Barang Kadaluarsa</h5>
                <?php if (in_array($role, ['admin', 'karyawan'])): ?>
                    <a href="add.php" class="btn btn-sm btn-primary">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <?php if ($result->num_rows === 0): ?>
                    <div class="alert alert-success">Tidak ada barang kadaluarsa tercatat.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped align-middle mb-0">
                            <thead class="table-warning">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal Expired</th>
                                    <?php if ($role === 'admin'): ?>
                                        <th class="text-center">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                                        <td><?= $row['jumlah'] ?></td>
                                        <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= strtotime($row['tanggal_expired']) < time() ? 'danger' : 'warning' ?>">
                                                <?= date('d-m-Y', strtotime($row['tanggal_expired'])) ?>
                                            </span>
                                        </td>
                                        <?php if ($role === 'admin'): ?>
                                            <td class="text-center">
                                                <div class="btn-list d-flex justify-content-center">
                                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-icon btn-warning me-1" title="Edit">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <button onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>