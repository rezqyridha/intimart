<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=pengiriman");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $tujuan = trim($_POST['tujuan'] ?? '');
    $tanggal_kirim = $_POST['tanggal_kirim'] ?? '';
    $estimasi_tiba = $_POST['estimasi_tiba'] ?? null;
    $status_pengiriman = $_POST['status_pengiriman'] ?? 'diproses';
    $id_barang = $_POST['id_barang'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];

    // Validasi status hanya salah satu dari ENUM
    $allowed_status = ['diproses', 'dikirim', 'diterima'];
    if (!in_array($status_pengiriman, $allowed_status)) {
        header("Location: edit.php?id=$id&msg=invalid_status&obj=pengiriman");
        exit;
    }

    if ($id === '' || !is_numeric($id) || $tujuan === '' || $tanggal_kirim === '' || empty($id_barang) || count($id_barang) !== count($jumlah)) {
        header("Location: edit.php?id=$id&msg=invalid&obj=pengiriman");
        exit;
    }

    foreach ($id_barang as $i => $idb) {
        $jml = (int) $jumlah[$i];
        $sql = "
            SELECT 
                IFNULL(masuk.total_masuk, 0) - 
                (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0)) AS stok_akhir
            FROM barang b
            LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_masuk FROM barang_masuk GROUP BY id_barang) masuk ON b.id = masuk.id_barang
            LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_keluar FROM barang_keluar GROUP BY id_barang) keluar ON b.id = keluar.id_barang
            LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_terjual FROM penjualan GROUP BY id_barang) pj ON b.id = pj.id_barang
            LEFT JOIN (
                SELECT p.id_barang, SUM(r.jumlah) AS total_retur
                FROM retur r
                JOIN penjualan p ON r.id_penjualan = p.id
                GROUP BY p.id_barang
            ) retur ON b.id = retur.id_barang
            WHERE b.id = ?
        ";

        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("i", $idb);
        $stmt->execute();
        $stmt->bind_result($stok_akhir);
        $stmt->fetch();
        $stmt->close();

        if ($jml > $stok_akhir) {
            header("Location: edit.php?id=$id&msg=stok_limit&obj=pengiriman");
            exit;
        }
    }

    // ✅ Update termasuk status_pengiriman
    $stmt = $koneksi->prepare("UPDATE pengiriman SET tujuan = ?, tanggal_kirim = ?, estimasi_tiba = ?, status_pengiriman = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $tujuan, $tanggal_kirim, $estimasi_tiba, $status_pengiriman, $id);
    $stmt->execute();
    $stmt->close();

    $koneksi->query("DELETE FROM pengiriman_detail WHERE id_pengiriman = $id");
    $stmt = $koneksi->prepare("INSERT INTO pengiriman_detail (id_pengiriman, id_barang, jumlah) VALUES (?, ?, ?)");
    for ($i = 0; $i < count($id_barang); $i++) {
        $idb = (int) $id_barang[$i];
        $jml = (int) $jumlah[$i];
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

$optionList = [];
while ($b = $barang->fetch_assoc()) {
    $optionList[] = [
        'id' => $b['id'],
        'nama_barang' => $b['nama_barang'],
        'satuan' => $b['satuan']
    ];
}

require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Data Pengiriman</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
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

                    <div class="mb-3">
                        <label class="form-label">Status Pengiriman</label>
                        <select name="status_pengiriman" class="form-select" required>
                            <?php
                            $statusOptions = ['diproses', 'dikirim', 'diterima'];
                            foreach ($statusOptions as $opt):
                                $selected = $data['status_pengiriman'] === $opt ? 'selected' : '';
                            ?>
                                <option value="<?= $opt ?>" <?= $selected ?>>
                                    <?= ucfirst($opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save"></i> Simpan Perubahan
                    </button>
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
    const BARANG_OPTIONS = <?= json_encode($optionList) ?>;

    function renderBarangDropdowns() {
        const selects = document.querySelectorAll("select[name='id_barang[]']");
        const selectedIds = [...selects].map(s => s.value).filter(v => v !== '');

        selects.forEach(select => {
            const current = select.value;
            select.innerHTML = '<option value="">-- Pilih Barang --</option>';
            BARANG_OPTIONS.forEach(opt => {
                const isUsed = selectedIds.includes(String(opt.id)) && String(opt.id) !== current;
                if (!isUsed || String(opt.id) === current) {
                    const option = document.createElement("option");
                    option.value = opt.id;
                    option.textContent = `${opt.nama_barang} - ${opt.satuan}`;
                    if (String(opt.id) === current) option.selected = true;
                    select.appendChild(option);
                }
            });
        });
    }

    document.getElementById("btnTambahBaris").addEventListener("click", () => {
        const row = document.createElement("tr");
        row.innerHTML = `
        <td>
            <select name="id_barang[]" class="form-select" required onchange="renderBarangDropdowns()"></select>
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
        renderBarangDropdowns();
    });

    document.addEventListener("click", e => {
        if (e.target.closest(".btnHapusRow")) {
            e.target.closest("tr").remove();
            renderBarangDropdowns();
        }
    });

    document.addEventListener("DOMContentLoaded", renderBarangDropdowns);
</script>