<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'manajer'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$query = "
    SELECT ptl.*, b.nama_barang, b.satuan
    FROM produk_tidak_laku ptl
    JOIN barang b ON ptl.id_barang = b.id
    ORDER BY ptl.created_at DESC
";
$result = $koneksi->query($query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Manajemen Data Produk Tidak Laku</h5>
                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fe fe-plus me-1"></i> Tambah
                </a>

            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="tabel-tidaklaku">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Periode</th>
                                <th>Jumlah Terjual</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</td>
                                    <td><?= date('d/m/Y', strtotime($row['periode_awal'])) ?> s/d <?= date('d/m/Y', strtotime($row['periode_akhir'])) ?></td>
                                    <td><?= $row['jumlah_terjual'] ?></td>
                                    <td>
                                        <?php
                                        $status = $row['status'];
                                        $badge = match ($status) {
                                            'diperiksa' => 'secondary',
                                            'tindaklanjut' => 'warning',
                                            'selesai' => 'success',
                                            default => 'dark'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?> text-capitalize"><?= $status ?></span>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars($row['keterangan'])) ?></td>
                                    <td class="text-center">
                                        <div class="btn-list d-flex justify-content-center">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-icon btn-warning me-1" title="Edit">
                                                <i class="fe fe-edit"></i>
                                            </a>
                                            <button onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-danger btn-icon" title="Hapus">
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

<!-- Modal Tambah Produk Tidak Laku -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="add.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Tidak Laku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Barang</label>
                    <select name="id_barang" class="form-select" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php
                        $barang = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");
                        while ($row = $barang->fetch_assoc()):
                        ?>
                            <option value="<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Periode Awal</label>
                        <input type="date" name="periode_awal" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Periode Akhir</label>
                        <input type="date" name="periode_akhir" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Jumlah Terjual</label>
                    <input type="number" name="jumlah_terjual" class="form-control" min="0" value="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="diperiksa" selected>Diperiksa</option>
                        <option value="tindaklanjut">Tindak Lanjut</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>


<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<script>
    document.getElementById("searchBox").addEventListener("input", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-tidaklaku tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>