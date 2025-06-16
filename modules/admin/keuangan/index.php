<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';

// Ambil data kas dari DB
$query = "SELECT * FROM kas ORDER BY tanggal DESC";
$result = $koneksi->query($query);

// Inisialisasi variabel
$data = [];
$saldo = 0;

// Loop data dan hitung saldo
while ($row = $result->fetch_assoc()) {
    $saldo += ($row['jenis'] === 'masuk') ? $row['jumlah'] : -$row['jumlah'];
    $data[] = $row;
}
?>

<div class="main-content app-content">
    <div class="container-fluid">

        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Manajemen Keuangan</div>
                <a href="add.php" class="btn btn-sm btn-primary">
                    <i class="fe fe-plus"></i> Tambah Transaksi
                </a>
            </div>

            <div class="card-body">
                <div class="alert alert-info">
                    💰 Saldo Kas Saat Ini:
                    <strong>Rp <?= number_format($saldo, 0, ',', '.') ?></strong>
                </div>

                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Cari...">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle mb-0" id="tabel-kas">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Keterangan</th>
                                <th>Jumlah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($data as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['jenis'] === 'masuk' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($row['jenis']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                    <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning btn-icon" title="Edit">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete('delete.php?id=<?= $row['id'] ?>')" class="btn btn-sm btn-danger btn-icon" title="Hapus">
                                            <i class="fe fe-trash-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>

<!-- Notifikasi & interaksi -->
<script src="<?= BASE_URL ?>/assets/js/notifier.js"></script>

<script>
    document.getElementById("searchBox").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#tabel-kas tbody tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>d