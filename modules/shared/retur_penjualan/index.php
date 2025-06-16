<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? 'User';

if (!in_array($role, ['admin', 'karyawan', 'sales', 'manajer'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$query = "
    SELECT rp.*, b.nama_barang, b.satuan, p.tanggal AS tgl_penjualan, p.jumlah AS jumlah_terjual
    FROM retur_penjualan rp
    JOIN penjualan p ON rp.id_penjualan = p.id
    JOIN barang b ON p.id_barang = b.id
    ORDER BY rp.tanggal DESC
";
$result = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Manajemen Data Retur Penjualan</div>
                <?php if (in_array($role, ['admin', 'karyawan'])): ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambah" class="btn btn-sm btn-primary">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0 align-middle" id="tabel-retur">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Tgl Penjualan</th>
                                <th>Tgl Retur</th>
                                <th>Alasan</th>
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
                                    <td><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</td>
                                    <td><?= $row['jumlah'] ?> / <?= $row['jumlah_terjual'] ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tgl_penjualan'])) ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['alasan']) ?></td>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Retur -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="add.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Retur Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Transaksi Penjualan</label>
                    <select name="id_penjualan" class="form-select" required>
                        <option value="">-- Pilih Transaksi --</option>
                        <?php
                        $penjualan = $koneksi->query("
                            SELECT p.id, b.nama_barang, b.satuan, p.tanggal, p.jumlah
                            FROM penjualan p
                            JOIN barang b ON p.id_barang = b.id
                            ORDER BY p.tanggal DESC
                        ");
                        while ($row = $penjualan->fetch_assoc()):
                        ?>
                            <option value="<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>) - <?= date('d-m-Y', strtotime($row['tanggal'])) ?> - Terjual: <?= $row['jumlah'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Retur</label>
                    <input type="number" name="jumlah" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alasan Retur</label>
                    <textarea name="alasan" class="form-control" rows="2" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Retur</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary"><i class="fe fe-plus me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<script>
    document.getElementById("searchBox").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-retur tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>