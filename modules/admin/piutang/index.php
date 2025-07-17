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
            p.*, 
            u.nama_lengkap AS nama_sales, 
            b.nama_barang
        FROM piutang p
        JOIN user u ON p.id_sales = u.id
        LEFT JOIN penjualan pj ON p.id_penjualan = pj.id
        LEFT JOIN barang b ON pj.id_barang = b.id
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
                    <div class="card-title mb-0">Manajemen Data Piutang</div>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                </div>

                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-end">
                        <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0" id="tabel-piutang">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Sales</th>
                                    <th>Barang</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_sales']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang'] ?? '-') ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                        <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $row['status'] === 'lunas' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row['status'] === 'lunas'): ?>
                                                <span class="badge bg-light text-dark border" title="Sudah Lunas" style="cursor: not-allowed;">
                                                    <i class=" fe fe-lock"></i> Final
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
                                    </tr>
                                <?php endwhile; ?>
                                <?php if ($result->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data piutang.</td>
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
                        <h5 class="modal-title">Tambah Piutang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_penjualan" class="form-label">Penjualan (Barang & Sales)</label>
                            <select name="id_penjualan" id="id_penjualan" class="form-select" required>
                                <option value="" hidden>-- Pilih Penjualan --</option>
                                <?php
                                $penjualan = $koneksi->query("
                                    SELECT pj.id, pj.tanggal, pj.jumlah, b.nama_barang, u.nama_lengkap AS nama_sales
                                    FROM penjualan pj
                                    JOIN barang b ON pj.id_barang = b.id
                                    JOIN user u ON pj.id_sales = u.id
                                    WHERE pj.status_pelunasan = 'belum lunas'
                                    ORDER BY pj.tanggal DESC
                                ");
                                while ($pj = $penjualan->fetch_assoc()) {
                                    $label = "[{$pj['tanggal']}] {$pj['nama_barang']} - {$pj['jumlah']} pcs | Sales: {$pj['nama_sales']}";
                                    echo "<option value='{$pj['id']}'>" . htmlspecialchars($label) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1000" step="500" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="belum lunas" selected>Belum Lunas</option>
                                <option value="lunas">Lunas</option>
                            </select>
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
            document.querySelectorAll("#tabel-piutang tbody tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
            });
        });
    </script>