<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'] ?? null;

$query = "
    SELECT bk.*, b.nama_barang, b.satuan, g.nama_gudang
    FROM barang_keluar bk
    JOIN barang b ON bk.id_barang = b.id
    JOIN gudang g ON bk.id_gudang = g.id
    ORDER BY bk.tanggal DESC
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
                <div class="card-title mb-0">Manajemen Data Barang Keluar</div>
                <?php if ($role === 'admin') : ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambah" class="btn btn-sm btn-primary" title="Tambah Barang Keluar">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped mb-0 align-middle" id="tabel-barang-keluar">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Gudang</th>
                                <th>Jumlah</th>
                                <th>Jenis</th>
                                <th>Tujuan</th>
                                <th>Keterangan</th>
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
                                    <td><?= htmlspecialchars($row['satuan']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_gudang']) ?></td>
                                    <td><?= $row['jumlah'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= match ($row['jenis']) {
                                                                    'rusak' => 'danger',
                                                                    'hilang' => 'warning',
                                                                    'retur_supplier' => 'info',
                                                                    default => 'secondary'
                                                                } ?>">
                                            <?= ucwords(str_replace('_', ' ', $row['jenis'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['tujuan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
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

            <!-- Modal Tambah -->
            <div class="modal fade" id="modalTambah" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form method="post" action="add.php" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Barang Keluar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body row">

                            <div class="col-md-6 mb-3">
                                <label for="id_barang" class="form-label">Nama Barang</label>
                                <select name="id_barang" id="id_barang" class="form-select" required>
                                    <option value="">-- Pilih Barang --</option>
                                    <?php
                                    $barangList = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");
                                    while ($b = $barangList->fetch_assoc()) :
                                    ?>
                                        <option value="<?= $b['id'] ?>">
                                            <?= htmlspecialchars($b['nama_barang']) ?> (<?= htmlspecialchars($b['satuan']) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="id_gudang" class="form-label">Gudang</label>
                                <select name="id_gudang" id="id_gudang" class="form-select" required>
                                    <option value="">-- Pilih Gudang --</option>
                                    <?php
                                    $gudangList = $koneksi->query("SELECT id, nama_gudang FROM gudang ORDER BY nama_gudang ASC");
                                    while ($g = $gudangList->fetch_assoc()) :
                                    ?>
                                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_gudang']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" name="jumlah" class="form-control" min="1" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select name="jenis" class="form-select" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="internal">Internal</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="hilang">Hilang</option>
                                    <option value="retur_supplier">Retur Supplier</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tujuan" class="form-label">Tujuan</label>
                                <input type="text" name="tujuan" class="form-control" placeholder="Opsional">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary"><i class="fe fe-plus me-1"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<script>
    document.getElementById("searchBox").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-barang-keluar tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>