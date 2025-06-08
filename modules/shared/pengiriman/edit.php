<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=pengiriman");
    exit;
}

// Jika POST: Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $tujuan = trim($_POST['tujuan'] ?? '');
    $tanggal_kirim = $_POST['tanggal_kirim'] ?? '';
    $estimasi_tiba = $_POST['estimasi_tiba'] ?? null;
    $id_barang = $_POST['id_barang'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];

    if ($id === '' || !is_numeric($id) || $tujuan === '' || empty($id_barang) || count($id_barang) !== count($jumlah)) {
        header("Location: edit.php?id=$id&msg=invalid&obj=pengiriman");
        exit;
    }

    $stmt = $koneksi->prepare("UPDATE pengiriman SET tujuan = ?, tanggal_kirim = ?, estimasi_tiba = ? WHERE id = ?");
    $stmt->bind_param("sssi", $tujuan, $tanggal_kirim, $estimasi_tiba, $id);
    $stmt->execute();
    $stmt->close();

    $koneksi->query("DELETE FROM pengiriman_detail WHERE id_pengiriman = $id");
    $stmt = $koneksi->prepare("INSERT INTO pengiriman_detail (id_pengiriman, id_barang, jumlah) VALUES (?, ?, ?)");
    for ($i = 0; $i < count($id_barang); $i++) {
        $idb = $id_barang[$i];
        $jml = $jumlah[$i];
        $stmt->bind_param("iii", $id, $idb, $jml);
        $stmt->execute();
    }
    $stmt->close();

    header("Location: index.php?msg=updated&obj=pengiriman");
    exit;
}

$id = $_GET['id'] ?? '';
if ($id === '' || !is_numeric($id)) {
    header("Location: index.php?msg=invalid&obj=pengiriman");
    exit;
}

$q_pengiriman = $koneksi->prepare("SELECT * FROM pengiriman WHERE id = ?");
$q_pengiriman->bind_param("i", $id);
$q_pengiriman->execute();
$result = $q_pengiriman->get_result();
if ($result->num_rows === 0) {
    header("Location: index.php?msg=notfound&obj=pengiriman");
    exit;
}
$data = $result->fetch_assoc();
$q_pengiriman->close();

$details = $koneksi->query("SELECT * FROM pengiriman_detail WHERE id_pengiriman = $id");
$barang = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");

ob_start();
$barang->data_seek(0);
while ($b = $barang->fetch_assoc()) {
    $id = $b['id'];
    $label = htmlspecialchars($b['nama_barang'] . ' - ' . $b['satuan']);
    echo "<option value=\"$id\">$label</option>";
}
$optionHtml = ob_get_clean();

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Data Pengiriman</h5>
            </div>

            <form action="edit.php" method="post">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tujuan</label>
                        <input type="text" name="tujuan" class="form-control" value="<?= htmlspecialchars($data['tujuan']) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Kirim</label>
                            <input type="date" name="tanggal_kirim" class="form-control" value="<?= $data['tanggal_kirim'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estimasi Tiba</label>
                            <input type="date" name="estimasi_tiba" class="form-control" value="<?= $data['estimasi_tiba'] ?>">
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
                                <?php while ($d = $details->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <select name="id_barang[]" class="form-select" required>
                                                <option value="">-- Pilih Barang --</option>
                                                <?php $barang->data_seek(0);
                                                while ($b = $barang->fetch_assoc()): ?>
                                                    <option value="<?= $b['id'] ?>" <?= $b['id'] == $d['id_barang'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($b['nama_barang'] . ' - ' . $b['satuan']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="jumlah[]" class="form-control" value="<?= $d['jumlah'] ?>" min="1" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger btnHapusRow d-flex align-items-center gap-1">
                                                <i class="fe fe-trash-2"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2 mb-4" id="btnTambahBaris">
                        <i class="fe fe-plus me-1"></i> Tambah Barang
                    </button>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>

<script>
    const BARANG_OPTIONS = `<?= $optionHtml ?>`;

    document.getElementById("btnTambahBaris").addEventListener("click", function() {
        const row = document.createElement("tr");
        row.innerHTML = `
        <td>
            <select name="id_barang[]" class="form-select" required>
                <option value="">-- Pilih Barang --</option>
                ${BARANG_OPTIONS}
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