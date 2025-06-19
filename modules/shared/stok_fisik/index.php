<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'manajer', 'karyawan'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Tambahkan di bagian query
$query = "
    SELECT sf.*, b.nama_barang, b.satuan, u.nama_lengkap AS user_input, g.nama_gudang
    FROM stok_fisik sf
    JOIN barang b ON sf.id_barang = b.id
    LEFT JOIN user u ON sf.id_user = u.id
    LEFT JOIN gudang g ON sf.id_gudang = g.id
    ORDER BY sf.tanggal DESC
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
                <div class="card-title mb-0">Manajemen Data Stok Fisik</div>
                <?php if (in_array($role, ['admin', 'karyawan'])): ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambah" class="btn btn-sm btn-primary" title="Tambah Stok Fisik">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="tabel-stokfisik">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Gudang</th>
                                <th>Jumlah Fisik</th>
                                <th>Stok Sistem</th>
                                <th>Selisih</th>
                                <th>Keterangan</th>
                                <th>Tanggal</th>
                                <th>Oleh</th>
                                <th>Koreksi?</th>
                                <?php if (in_array($role, ['admin', 'karyawan'])): ?>
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
                                    <td><?= htmlspecialchars($row['nama_gudang'] ?? '-') ?></td>
                                    <td><?= $row['jumlah_fisik'] ?></td>
                                    <td><?= is_numeric($row['stok_sistem']) ? $row['stok_sistem'] : '-' ?></td>
                                    <td>
                                        <?php
                                        if (is_numeric($row['stok_sistem'])) {
                                            echo $row['stok_sistem'] - $row['jumlah_fisik'];
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['user_input'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <?= $row['koreksi'] ? '<i class="fe fe-check text-success"></i>' : '-' ?>
                                    </td>
                                    <?php if (in_array($role, ['admin', 'karyawan'])): ?>
                                        <td class="text-center">
                                            <?php if ($row['koreksi']): ?>
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fe fe-lock me-1"></i> Final
                                                </span>
                                            <?php else: ?>
                                                <div class="btn-list d-flex justify-content-center">
                                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-icon btn-warning me-1" title="Edit">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <button onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
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

<!-- Modal Tambah Data Stok Fisik -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="add.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Data Stok Fisik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Barang</label>
                        <select name="id_barang" id="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php
                            $barangList = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");
                            while ($b = $barangList->fetch_assoc()):
                            ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_fisik" class="form-label">Jumlah Fisik</label>
                        <input type="number" name="jumlah_fisik" id="jumlah_fisik" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_gudang" class="form-label">Gudang</label>
                        <select name="id_gudang" id="id_gudang" class="form-select" required>
                            <option value="">-- Pilih Gudang --</option>
                            <?php
                            $gudangList = $koneksi->query("SELECT id, nama_gudang FROM gudang ORDER BY nama_gudang ASC");
                            while ($g = $gudangList->fetch_assoc()):
                            ?>
                                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_gudang']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="koreksi" id="koreksi">
                        <label class="form-check-label" for="koreksi">
                            Tandai sebagai koreksi stok sistem
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save"></i> Simpan
                    </button>
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
        const rows = document.querySelectorAll("#tabel-stokfisik tbody tr");
        rows.forEach(row => {
            const match = [...row.cells].some(td => td.textContent.toLowerCase().includes(filter));
            row.style.display = match ? "" : "none";
        });
    });
</script>