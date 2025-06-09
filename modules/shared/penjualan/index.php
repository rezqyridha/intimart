<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'] ?? '';

// Ambil data penjualan + join nama barang
$query = "SELECT p.*, b.nama_barang, b.harga_jual 
          FROM penjualan p 
          JOIN barang b ON p.id_barang = b.id 
          ORDER BY p.tanggal DESC";
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
                <div class="card-title mb-0">Manajemen Data Penjualan</div>
                <?php if ($role === 'admin') : ?>
                    <a href="#" class="btn btn-sm  btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah" title="Tambah Penjualan">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped mb-0 align-middle" id="tabel-penjualan">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Harga Jual</th>
                                <th>Total</th>
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
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= $row['jumlah'] ?></td>
                                    <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                                    <?php if ($role === 'admin') : ?>
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

<!-- Modal Tambah -->
<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="add.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="id_barang" class="form-label">Barang</label>
                    <select name="id_barang" class="form-select" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php
                        $barang = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");
                        while ($b = $barang->fetch_assoc()):
                        ?>
                            <option value="<?= $b['id'] ?>">
                                <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Penjualan</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">
                    <i class="fe fe-plus me-1"></i> Simpan
                </button>
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
        document.querySelectorAll("#tabel-penjualan tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>