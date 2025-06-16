<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
if (!in_array($role, ['admin', 'sales', 'manajer', 'karyawan'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$query = "
    SELECT p.*, b.nama_barang, b.satuan, u.nama_lengkap AS nama_sales
    FROM penjualan p
    JOIN barang b ON p.id_barang = b.id
    JOIN user u ON p.id_sales = u.id
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
                <div class="card-title mb-0">Manajemen Data Penjualan</div>
                <?php if ($role === 'admin') : ?>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0" id="tabel-penjualan">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Status</th>
                                <?php if ($role === 'admin' || $role === 'manajer') : ?>
                                    <th>Sales</th>
                                <?php endif; ?>
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
                                    <td><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= $row['jumlah'] ?></td>
                                    <td>Rp <?= number_format($row['harga_total'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status_pelunasan'] === 'lunas' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($row['status_pelunasan']) ?>
                                        </span>
                                    </td>
                                    <?php if ($role === 'admin' || $role === 'manajer') : ?>
                                        <td><?= htmlspecialchars($row['nama_sales']) ?></td>
                                    <?php endif; ?>
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

<!-- Modal Tambah Penjualan -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form action="add.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Data Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Barang</label>
                        <select name="id_barang" id="id_barang" class="form-select" required onchange="hitungTotal()">
                            <option value="" hidden>-- Pilih Barang --</option>
                            <?php
                            $barangHarga = $koneksi->query("SELECT id, nama_barang, harga_jual FROM barang ORDER BY nama_barang ASC");
                            $hargaArray = [];
                            while ($b = $barangHarga->fetch_assoc()) {
                                echo "<option value='{$b['id']}'>{$b['nama_barang']}</option>";
                                $hargaArray[$b['id']] = $b['harga_jual'];
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_sales" class="form-label">Sales</label>
                        <select name="id_sales" id="id_sales" class="form-select" required>
                            <option value="" hidden>-- Pilih Sales --</option>
                            <?php
                            $sales = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales' ORDER BY nama_lengkap ASC");
                            while ($s = $sales->fetch_assoc()) {
                                echo "<option value='{$s['id']}'>{$s['nama_lengkap']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required oninput="hitungTotal()">
                    </div>
                    <div class="mb-3">
                        <label for="harga_total" class="form-label">Total Harga (Otomatis)</label>
                        <input type="number" name="harga_total" id="harga_total" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="status_pelunasan" class="form-label">Status Pelunasan</label>
                        <select name="status_pelunasan" id="status_pelunasan" class="form-select" required>
                            <option value="belum lunas">Belum Lunas</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<!-- Script untuk pencarian data tabel -->
<script>
    document.getElementById("searchBox").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-penjualan tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>

<!-- Script untuk menghitung total -->
<script>
    const hargaBarang = <?php echo json_encode($hargaArray); ?>;

    function hitungTotal() {
        const idBarang = document.getElementById('id_barang').value;
        const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
        const harga = hargaBarang[idBarang] || 0;
        document.getElementById('harga_total').value = harga * jumlah;
    }
</script>