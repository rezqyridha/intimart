<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

// Batasi akses hanya ke admin, manajer, dan sales
$role = $_SESSION['role'] ?? '';
$id_user = $_SESSION['id_user'] ?? 0;

if (!in_array($role, ['admin', 'manajer', 'sales'])) {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// Filter data jika role = sales (lihat hanya miliknya)
$whereClause = "";
if ($role === 'sales') {
    $whereClause = "WHERE j.id_sales = $id_user";
}

// Query untuk menampilkan data rekonsiliasi
$sqlTabel = "
    SELECT rp.*, b.nama_barang, b.satuan, p.tanggal, p.metode, p.jumlah_bayar
    FROM rekonsiliasi_pembayaran rp
    JOIN pembayaran p ON rp.id_pembayaran = p.id
    JOIN penjualan j ON p.id_penjualan = j.id
    JOIN barang b ON j.id_barang = b.id
    $whereClause
    ORDER BY rp.tanggal_rekonsiliasi DESC
";
$dataRekonsiliasi = $koneksi->query($sqlTabel);

// Query untuk dropdown hanya muncul jika bukan manajer (readonly)
$opsi_pembayaran = '';
if ($role !== 'manajer') {
    $sqlOpsi = "
        SELECT 
            p.id,
            b.nama_barang,
            b.satuan,
            p.metode,
            p.jumlah_bayar,
            p.tanggal
        FROM pembayaran p
        JOIN penjualan j ON p.id_penjualan = j.id
        JOIN barang b ON j.id_barang = b.id
        WHERE p.id NOT IN (SELECT id_pembayaran FROM rekonsiliasi_pembayaran)
        ORDER BY p.tanggal DESC
    ";
    $resultOpsi = $koneksi->query($sqlOpsi);
    while ($row = $resultOpsi->fetch_assoc()) {
        $id       = (int) $row['id'];
        $nama     = htmlspecialchars($row['nama_barang']);
        $satuan   = htmlspecialchars($row['satuan']);
        $metode   = ucfirst($row['metode']);
        $nominal  = number_format($row['jumlah_bayar'], 0, ',', '.');
        $tanggal  = date('d-m-Y', strtotime($row['tanggal']));
        $opsi_pembayaran .= "<option value=\"$id\">$nama ($satuan) - $metode - Rp$nominal [$tanggal]</option>\n";
    }
}

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <div class="card custom-card mt-5 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Rekonsiliasi Pembayaran</div>
                <?php if ($role !== 'manajer'): ?>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah" title="Tambah Rekonsiliasi Pembayaran">
                        <i class="fe fe-plus"></i> Tambah
                    </button>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped mb-0 align-middle" id="tabel-rekonsiliasi">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Metode</th>
                                <th>Nominal</th>
                                <th>Tgl Bayar</th>
                                <th>Tgl Rekonsiliasi</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $dataRekonsiliasi->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?> (<?= $row['satuan'] ?>)</td>
                                    <td><?= htmlspecialchars($row['metode']) ?></td>
                                    <td>Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal_rekonsiliasi'])) ?></td>
                                    <td>
                                        <?php
                                        $badge = match ($row['status']) {
                                            'tidak sesuai' => 'secondary',
                                            'sesuai' => 'success',
                                            default => 'dark'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?> text-capitalize"><?= str_replace('_', ' ', $row['status']) ?></span>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
                                    <td class="text-center">
                                        <?php if ($role === 'admin' && $row['is_final'] == 0): ?>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-icon btn-warning me-1" title="Edit">
                                                <i class="fe fe-edit"></i>
                                            </a>
                                            <button onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted"><i class="fe fe-lock me-1"></i> Final</span>
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

<?php if ($role !== 'manajer'): ?>
    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" action="add.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rekonsiliasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_pembayaran" class="form-label">Pembayaran</label>
                        <select name="id_pembayaran" class="form-select" required>
                            <option value="">-- Pilih Pembayaran --</option>
                            <?= $opsi_pembayaran ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Catatan atau keterangan" required></textarea>
                    </div>

                    <?php if ($role === 'admin'): ?>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="sesuai" selected>Sesuai</option>
                                <option value="tidak sesuai">Tidak Sesuai</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>


<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>

<script>
    // 🔍 Search filter
    document.getElementById("searchBox").addEventListener("input", function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll("#tabel-rekonsiliasi tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(keyword) ? "" : "none";
        });
    });
</script>