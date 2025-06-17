<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$query = "
    SELECT 
        ts.id, ts.id_sales, ts.bulan, ts.target,
        u.nama_lengkap,
        (
            SELECT IFNULL(SUM(p.harga_total), 0)
            FROM penjualan p
            WHERE p.id_sales = ts.id_sales
              AND DATE_FORMAT(p.tanggal, '%Y-%m') = ts.bulan
        ) AS realisasi
    FROM target_sales ts
    JOIN user u ON ts.id_sales = u.id
    ORDER BY ts.bulan DESC
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
                <div class="card-title mb-0">Target vs Realisasi Penjualan</div>
                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fe fe-plus"></i> Tambah
                </a>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle mb-0" id="tabel-target">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Sales</th>
                                <th>Bulan</th>
                                <th>Target</th>
                                <th>Realisasi</th>
                                <th>Selisih</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            $today = date('Y-m');
                            while ($row = $result->fetch_assoc()) :
                                $selisih = $row['realisasi'] - $row['target'];
                                $bulanLalu = $row['bulan'] < $today;
                            ?>

                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                    <td><?= date('m-Y', strtotime($row['bulan'] . '-01')) ?></td>
                                    <td>Rp <?= number_format($row['target'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($row['realisasi'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="text-<?= $selisih < 0 ? 'danger' : 'success' ?>">
                                            Rp <?= number_format($selisih, 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!$bulanLalu && $row['realisasi'] <= $row['target']): ?>
                                            <div class="btn-list d-flex justify-content-center">
                                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-icon btn-warning me-1" title="Edit">
                                                    <i class="fe fe-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                                    <i class="fe fe-trash-2"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fe fe-lock me-1"></i> Final
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($result->num_rows === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada target ditetapkan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <form action="add.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Target Sales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_sales" class="form-label">Sales</label>
                        <select name="id_sales" id="id_sales" class="form-select" required>
                            <option value="" hidden>-- Pilih Sales --</option>
                            <?php
                            $sales = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales' ORDER BY nama_lengkap ASC");
                            while ($s = $sales->fetch_assoc()) {
                                echo "<option value='{$s['id']}'>" . htmlspecialchars($s['nama_lengkap']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bulan" class="form-label">Bulan (YYYY-MM)</label>
                        <input type="month" name="bulan" id="bulan" class="form-control" required min="<?= date('Y-m') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="target" class="form-label">Target (Rp)</label>
                        <input type="number" name="target" id="target" class="form-control" required min="100000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save"></i> Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<script>
    document.getElementById("searchBox").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-target tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>