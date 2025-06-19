    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
    require_once AUTH_PATH . '/session.php';
    require_once CONFIG_PATH . '/koneksi.php';

    $role = $_SESSION['role'];
    $canEdit = in_array($role, ['admin',]);
    $canAdd = in_array($role, ['admin',  'karyawan']);

    // Ambil data
    $query = "
        SELECT rs.*, s.nama_supplier
        FROM restok_supplier rs
        JOIN supplier s ON rs.id_supplier = s.id
        ORDER BY rs.created_at DESC
    ";
    $result = $koneksi->query($query);

    // Ambil semua supplier
    $suppliers = $koneksi->query("SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier ASC");

    require_once LAYOUTS_PATH . '/head.php';
    require_once LAYOUTS_PATH . '/header.php';
    require_once LAYOUTS_PATH . '/topbar.php';
    require_once LAYOUTS_PATH . '/sidebar.php';
    ?>

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="card custom-card shadow-sm mt-5">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">Manajemen Restok Supplier</div>
                    <?php if ($canAdd): ?>
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
                        <table class="table table-bordered table-striped align-middle mb-0" id="tabel-restok">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Supplier</th>
                                    <th>Tanggal Pesan</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                    <th>Dibuat</th>
                                    <?php if ($canEdit): ?>
                                        <th class="text-center">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_supplier']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tgl_pesan'])) ?></td>
                                        <?php
                                        $status = strtolower($row['status']);
                                        $badgeClasses = [
                                            'diproses' => 'bg-warning',
                                            'dikirim'  => 'bg-info',
                                            'selesai'  => 'bg-success',
                                            'batal'    => 'bg-danger',
                                        ];
                                        $badgeClass = $badgeClasses[$status] ?? 'bg-secondary';
                                        ?>
                                        <td>
                                            <span class="badge <?= htmlspecialchars($badgeClass) ?>">
                                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= nl2br(htmlspecialchars($row['catatan'] ?? '-')) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                        <?php if ($canEdit): ?>
                                            <td class="text-center">
                                                <div class="btn-list d-flex justify-content-center">
                                                    <?php if ($row['status'] === 'diproses'): ?>

                                                        <!-- Tombol Dropdown Ubah Status -->
                                                        <div class="dropdown me-1">
                                                            <button class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                                                                Ubah Status
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <form action="update_status.php" method="post">
                                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                                    <?php foreach (['dikirim', 'selesai', 'batal'] as $newStatus): ?>
                                                                        <li>
                                                                            <button class="dropdown-item" type="submit" name="status" value="<?= $newStatus ?>">
                                                                                <?= ucfirst($newStatus) ?>
                                                                            </button>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                </form>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Tombol Edit dan Hapus -->
                                                    <?php if ($status === 'diproses'): ?>
                                                        <div class="btn-list d-flex justify-content-center">
                                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-icon btn-warning me-1" title="Edit">
                                                                <i class="fe fe-edit"></i>
                                                            </a>
                                                            <button onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                                                <i class="fe fe-trash-2"></i>
                                                            </button>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted"><i class="fe fe-lock me-1"></i> Final</span>
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

    <!-- Modal Tambah -->
    <?php if ($canAdd): ?>
        <div class="modal fade" id="modalTambah" tabindex="-1">
            <div class="modal-dialog modal-md">
                <form action="add.php" method="post" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Permintaan Restok</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select name="id_supplier" class="form-select" required>
                                <option value="">-- Pilih Supplier --</option>
                                <?php while ($s = $suppliers->fetch_assoc()): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_supplier']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pesan</label>
                            <input type="date" name="tgl_pesan" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary"><i class="fe fe-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php require_once LAYOUTS_PATH . '/footer.php'; ?>
    <?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

    <script>
        document.getElementById("searchBox").addEventListener("input", function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll("#tabel-restok tbody tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(val) ? "" : "none";
            });
        });
    </script>