<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'manajer'])) {
    header("Location: index.php?msg=unauthorized&obj=restok_supplier");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=restok_supplier");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM restok_supplier WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=restok_supplier");
    exit;
}

// Cegah edit jika status final
if (in_array($data['status'], ['selesai', 'batal'])) {
    header("Location: index.php?msg=locked&obj=restok_supplier");
    exit;
}

// Ambil daftar supplier
$supplierList = $koneksi->query("SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_supplier = (int)($_POST['id_supplier'] ?? 0);
    $tgl_pesan   = trim($_POST['tgl_pesan'] ?? '');
    $status      = trim($_POST['status'] ?? '');
    $catatan     = trim($_POST['catatan'] ?? '');

    // Validasi input
    if ($id_supplier <= 0 || empty($tgl_pesan) || !in_array($status, ['diproses', 'dikirim', 'selesai', 'batal'])) {
        header("Location: edit.php?id=$id&msg=kosong&obj=restok_supplier");
        exit;
    }

    // Validasi supplier
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM supplier WHERE id = ?");
    $cek->bind_param("i", $id_supplier);
    $cek->execute();
    $cek->bind_result($supplierAda);
    $cek->fetch();
    $cek->close();
    if (!$supplierAda) {
        header("Location: edit.php?id=$id&msg=invalid&obj=restok_supplier");
        exit;
    }

    // Update data
    $stmt = $koneksi->prepare("
        UPDATE restok_supplier
        SET id_supplier=?, tgl_pesan=?, status=?, catatan=?
        WHERE id=?
    ");
    $stmt->bind_param("isssi", $id_supplier, $tgl_pesan, $status, $catatan, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=restok_supplier");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=restok_supplier");
    }

    $stmt->close();
    exit;
}
require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card shadow-sm mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Edit Permintaan Restok Supplier</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="id_supplier" class="form-label">Supplier</label>
                        <select name="id_supplier" id="id_supplier" class="form-select" required>
                            <option value="">-- Pilih Supplier --</option>
                            <?php while ($s = $supplierList->fetch_assoc()): ?>
                                <option value="<?= $s['id'] ?>" <?= $s['id'] == $data['id_supplier'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['nama_supplier']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tgl_pesan" class="form-label">Tanggal Permintaan</label>
                        <input type="date" name="tgl_pesan" id="tgl_pesan" class="form-control" required value="<?= $data['tgl_pesan'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <?php
                            $opsi = ['diproses', 'dikirim', 'selesai', 'batal'];
                            foreach ($opsi as $s):
                            ?>
                                <option value="<?= $s ?>" <?= $s === $data['status'] ? 'selected' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="form-control"><?= htmlspecialchars($data['catatan']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>