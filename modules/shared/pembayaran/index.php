<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
if (!in_array($role, ['admin', 'manajer'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$query = "
    SELECT p.*, b.nama_barang, b.satuan, penjualan.tanggal AS tgl_jual, penjualan.harga_total, penjualan.status_pelunasan
    FROM pembayaran p
    JOIN penjualan ON p.id_penjualan = penjualan.id
    JOIN barang b ON penjualan.id_barang = b.id
    ORDER BY p.tanggal DESC
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
                <div class="card-title mb-0">Manajemen Data Pembayaran</div>
                <?php if ($role === 'admin') : ?>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah" title="Tambah Pembayaran">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0" id="tabel-pembayaran">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Jumlah Bayar</th>
                                <th>Metode</th>
                                <th>Tgl Bayar</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <?php if ($role === 'admin') : ?>
                                    <th class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</strong><br>
                                        <small class="text-muted">Penjualan: <?= date('d-m-Y', strtotime($row['tgl_jual'])) ?></small>
                                    </td>
                                    <td>
                                        Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?>
                                    </td>
                                    <td><?= ucfirst($row['metode']) ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="text-center"><?= $row['keterangan'] ? htmlspecialchars($row['keterangan']) : '-' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status_pelunasan'] === 'lunas' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($row['status_pelunasan']) ?>
                                        </span>
                                    </td>

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
                                </tr>
                            <?php endwhile; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="add.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Transaksi Penjualan</label>
                    <select name="id_penjualan" class="form-select" required>
                        <option value="">-- Pilih Transaksi --</option>
                        <?php
                        $penjualan = $koneksi->query("
              SELECT p.id, b.nama_barang, b.satuan, p.tanggal, p.harga_total
              FROM penjualan p
              JOIN barang b ON p.id_barang = b.id
              ORDER BY p.tanggal DESC
            ");
                        while ($row = $penjualan->fetch_assoc()):
                        ?>
                            <option value="<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>) - <?= date('d-m-Y', strtotime($row['tanggal'])) ?> - Rp <?= number_format($row['harga_total'], 0, ',', '.') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Bayar</label>
                    <input type="number" name="jumlah_bayar" class="form-control" min="100" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="metode" class="form-select" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan (opsional)</label>
                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Pembayaran</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>

<script>
    document.getElementById("searchBox").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-barang tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>