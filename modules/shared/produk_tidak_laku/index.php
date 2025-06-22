<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];

if (!in_array($role, ['admin', 'manajer', 'karyawan'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

$query = "
    SELECT pt.*, b.nama_barang, b.satuan
    FROM produk_tidak_laku pt
    JOIN barang b ON pt.id_barang = b.id
    ORDER BY pt.periode_akhir DESC
";
$result = $koneksi->query($query);
$barang = $koneksi->query("SELECT id, nama_barang FROM barang ORDER BY nama_barang ASC");

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Manajemen Produk Tidak Laku</div>
                <?php if (in_array($role, ['admin', 'manajer'])) : ?>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fe fe-plus"></i> Tambah
                    </button>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle" id="tabel-produk">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Periode</th>
                                <th>Jumlah Terjual</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <?php if (in_array($role, ['admin', 'manajer'])) : ?>
                                    <th class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= $row['satuan'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['periode_awal'])) ?> s.d <?= date('d/m/Y', strtotime($row['periode_akhir'])) ?></td>
                                    <td><?= $row['jumlah_terjual'] ?></td>
                                    <td>
                                        <?php
                                        $status = $row['status'];
                                        $statusText = ucfirst(str_replace('_', ' ', $status));
                                        $badgeClass = match ($status) {
                                            'diperiksa'     => 'warning',
                                            'tindaklanjut'  => 'info',
                                            'selesai'       => 'success',
                                            default         => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                    <td class="text-center">
                                        <?php if ($row['status'] === 'selesai'): ?>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fe fe-lock me-1"></i> Final
                                            </span>
                                        <?php elseif ($role === 'manajer'): ?>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                                                    Ubah Status
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <form action="update_status.php" method="POST">
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <?php foreach (['diperiksa', 'tindaklanjut', 'selesai'] as $opsi): ?>
                                                            <li>
                                                                <button class="dropdown-item" type="submit" name="status" value="<?= $opsi ?>">
                                                                    <?= ucfirst($opsi) ?>
                                                                </button>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </form>
                                                </ul>
                                            </div>
                                        <?php elseif ($role === 'admin'): ?>
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

                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Evaluasi -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form action="add.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Evaluasi Produk Tidak Laku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Barang</label>
                        <select name="id_barang" id="id_barang" class="form-select" required onchange="tampilkanSatuan()">
                            <option value="" hidden>-- Pilih Barang --</option>
                            <?php
                            $barang = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");
                            while ($b = $barang->fetch_assoc()) :
                            ?>
                                <option value="<?= $b['id'] ?>" data-satuan="<?= $b['satuan'] ?>">
                                    <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Satuan</label>
                        <input type="text" id="satuan_display" class="form-control" readonly>
                    </div>
                    <div class="mb-3 row">
                        <div class="col">
                            <label>Periode Awal</label>
                            <input type="date" name="periode_awal" class="form-control" required>
                        </div>
                        <div class="col">
                            <label>Periode Akhir</label>
                            <input type="date" name="periode_akhir" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Terjual</label>
                        <input type="number" name="jumlah_terjual" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="diperiksa">Diperiksa</option>
                            <option value="tindaklanjut">Tindak lanjut</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<script>
    function tampilkanSatuan() {
        const select = document.getElementById('id_barang');
        const satuan = select.options[select.selectedIndex].getAttribute('data-satuan');
        document.getElementById('satuan_display').value = satuan || '';
    }
    document.addEventListener("DOMContentLoaded", function() {
        tampilkanSatuan(); // Tampilkan satuan saat halaman dimuat
    });
    document.getElementById("searchBox").addEventListener("input", function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll("#tabel-produk tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(keyword) ? "" : "none";
        });
    });
</script>