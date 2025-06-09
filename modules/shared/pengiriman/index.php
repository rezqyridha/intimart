<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'];
$username = $_SESSION['username'] ?? 'User';

$query = "
    SELECT p.*, GROUP_CONCAT(CONCAT(b.nama_barang, ' (', pd.jumlah, ')') SEPARATOR '<br>') AS detail_barang
    FROM pengiriman p
    LEFT JOIN pengiriman_detail pd ON pd.id_pengiriman = p.id
    LEFT JOIN barang b ON b.id = pd.id_barang
    GROUP BY p.id
    ORDER BY p.tanggal_kirim DESC
";
$result = $koneksi->query($query);
$barang = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");


require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Manajemen Data Pengiriman</div>
                <?php if ($role === 'admin'): ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambah" class="btn btn-sm btn-primary" title="Tambah Pengiriman">
                        <i class="fe fe-plus"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered border table-hover table-striped mb-0 align-middle" id="tabel-pengiriman">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Tujuan</th>
                                <th>Tanggal Kirim</th>
                                <th>Estimasi Tiba</th>
                                <th>Status</th>
                                <th>Barang</th>
                                <?php if ($role === 'admin'): ?>
                                    <th class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['tujuan']) ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_kirim']) ?></td>
                                    <td><?= htmlspecialchars($row['estimasi_tiba']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($row['status_pengiriman'])) ?></td>
                                    <td><?= $row['detail_barang'] ?></td>
                                    <?php if ($role === 'admin'): ?>
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

<!-- Modal Tambah Pengiriman -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="add.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tujuan Pengiriman</label>
                    <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Banjarmasin, Toko A" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Kirim</label>
                        <input type="date" name="tanggal_kirim" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Estimasi Tiba</label>
                        <input type="date" name="estimasi_tiba" class="form-control">
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-semibold mb-3">Detail Barang</h6>
                <div class="table-responsive mb-2">
                    <table class="table table-bordered align-middle" id="tabel-barang">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50%">Barang</th>
                                <th style="width:20%">Jumlah</th>
                                <th style="width:30%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="id_barang[]" class="form-select" required>
                                        <option value="">-- Pilih Barang --</option>
                                        <?php $barang->data_seek(0);
                                        while ($b = $barang->fetch_assoc()): ?>
                                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang'] . ' - ' . $b['satuan']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="jumlah[]" class="form-control" min="1" value="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger btnHapusRow d-flex align-items-center gap-1">
                                        <i class="fe fe-trash-2"></i> Hapus
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2 mb-2" id="btnTambahBaris">
                    <i class="fe fe-plus me-1"></i> Tambah Barang
                </button>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
        document.querySelectorAll("#tabel-pengiriman tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    document.getElementById("btnTambahBaris").addEventListener("click", function() {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>
            <select name="id_barang[]" class="form-select" required>
            <option value="">-- Pilih Barang --</option>
            <?php $barang->data_seek(0);
            while ($b = $barang->fetch_assoc()): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang'] . ' - ' . $b['satuan']) ?></option>
            <?php endwhile; ?>
            </select>
        </td>
        <td>
            <input type="number" name="jumlah[]" class="form-control" value="1" min="1" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger btnHapusRow d-flex align-items-center gap-1">
            <i class="fe fe-trash-2"></i> Hapus
            </button>
        </td>
        `;

        document.querySelector("#tabel-barang tbody").appendChild(row);
    });


    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("btnHapusRow")) {
            e.target.closest("tr").remove();
        }
    });
</script>