<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
$result = $koneksi->query("SELECT * FROM supplier ORDER BY nama_supplier ASC");

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Manajemen Data Supplier</div>
                <?php if ($role === 'admin') : ?>
                    <a href="#" class="btn btn-sm  btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah" title="Tambah Supplier">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped mb-0 align-middle" id="tabel-supplier">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kontak</th>
                                <th>Alamat</th>
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
                                    <td><?= htmlspecialchars($row['nama_supplier']) ?></td>
                                    <td><?= htmlspecialchars($row['kontak']) ?></td>
                                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                                    <?php if ($role === 'admin') : ?>
                                        <td class="text-center">
                                            <div class="btn-list d-flex justify-content-center">
                                                <button class="btn btn-sm btn-icon btn-warning me-1 btnEdit"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama_supplier']) ?>"
                                                    data-no="<?= htmlspecialchars($row['kontak']) ?>"
                                                    data-alamat="<?= htmlspecialchars($row['alamat']) ?>">
                                                    <i class="fe fe-edit"></i>
                                                </button>
                                                <button onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-danger btn-icon">
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
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="add.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nama" class="form-control mb-2" placeholder="Nama Supplier" required>
                <input type="text" name="kontak" class="form-control mb-2" placeholder="Kontak Supplier">
                <textarea name="alamat" class="form-control" placeholder="Alamat"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="edit.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <input type="text" name="nama" id="edit-nama" class="form-control mb-2" required>
                <input type="text" name="kontak" id="edit-kontak" class="form-control mb-2">
                <textarea name="alamat" id="edit-alamat" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Update</button>
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
        document.querySelectorAll("#tabel-supplier tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    document.querySelectorAll('.btnEdit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-nama').value = this.dataset.nama;
            document.getElementById('edit-kontak').value = this.dataset.kontak;
            document.getElementById('edit-alamat').value = this.dataset.alamat;
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        });
    });
</script>