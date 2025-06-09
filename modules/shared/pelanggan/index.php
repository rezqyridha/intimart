<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
$username = $_SESSION['username'] ?? 'User';

$query = "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC";
$result = mysqli_query($koneksi, $query);

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0"> Manajemen Data Pelanggan</div>
                <?php if (in_array($role, ['admin', 'sales'])): ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambah" class="btn btn-sm  btn-primary" title="Tambah Pelanggan">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered border table-hover table-striped mb-0 align-middle" id="tabel-pelanggan">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <?php if ($role === 'admin'): ?>
                                    <th class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                                    <?php if ($role === 'admin') : ?>
                                        <td class="text-center">
                                            <div class="btn-list d-flex justify-content-center">
                                                <button class="btn btn-sm btn-icon btn-warning me-1 btnEdit"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= $row['nama_pelanggan'] ?>"
                                                    data-no="<?= $row['no_hp'] ?>"
                                                    data-alamat="<?= $row['alamat'] ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalEdit">
                                                    <i class="fe fe-edit"></i>
                                                </button>
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
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="add.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nama" class="form-control mb-2" placeholder="Nama Pelanggan" required>
                <input type="text" name="no_hp" class="form-control mb-2" placeholder="No HP">
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
                <h5 class="modal-title">Edit Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <input type="text" name="nama" id="edit-nama" class="form-control mb-2" required>
                <input type="text" name="no_hp" id="edit-no" class="form-control mb-2">
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
    // Search filter
    document.getElementById("searchBox").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-pelanggan tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    // Set isi modal edit
    document.querySelectorAll('.btnEdit').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-nama').value = this.dataset.nama;
            document.getElementById('edit-no').value = this.dataset.no;
            document.getElementById('edit-alamat').value = this.dataset.alamat;
        });
    });
</script>