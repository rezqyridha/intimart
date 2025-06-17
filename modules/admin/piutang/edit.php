<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=piutang");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM piutang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

// Cek jika status sudah lunas, tidak boleh diedit
if ($data['status'] === 'lunas') {
    header("Location: index.php?msg=locked&obj=piutang");
    exit;
}

// Cek apakah data ditemukan
if (!$data) {
    header("Location: index.php?msg=invalid&obj=piutang");
    exit;
}

// Ambil list sales
$salesList = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales' ORDER BY nama_lengkap ASC");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sales = intval($_POST['id_sales'] ?? 0);
    $tanggal  = trim($_POST['tanggal'] ?? '');
    $jumlah   = trim($_POST['jumlah'] ?? '');
    $status   = trim($_POST['status'] ?? '');

    if ($id_sales <= 0 || empty($tanggal) || empty($jumlah) || empty($status)) {
        header("Location: edit.php?id=$id&msg=kosong&obj=piutang");
        exit;
    }

    if (!is_numeric($jumlah) || $jumlah <= 0) {
        header("Location: edit.php?id=$id&msg=invalid&obj=piutang");
        exit;
    }

    // Validasi sales
    $cek = $koneksi->prepare("SELECT id FROM user WHERE id = ? AND role = 'sales'");
    $cek->bind_param("i", $id_sales);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows === 0) {
        $cek->close();
        header("Location: edit.php?id=$id&msg=invalid&obj=piutang");
        exit;
    }
    $cek->close();

    // Update
    $stmt = $koneksi->prepare("UPDATE piutang SET id_sales = ?, tanggal = ?, jumlah = ?, status = ? WHERE id = ?");
    $stmt->bind_param("isdsi", $id_sales, $tanggal, $jumlah, $status, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=piutang");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=piutang");
    }
    exit;
}
?>

<?php require_once LAYOUTS_PATH . '/head.php'; ?>
<?php require_once LAYOUTS_PATH . '/header.php'; ?>
<?php require_once LAYOUTS_PATH . '/topbar.php'; ?>
<?php require_once LAYOUTS_PATH . '/sidebar.php'; ?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">Edit Data Piutang</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="id_sales" class="form-label">Sales</label>
                        <select name="id_sales" id="id_sales" class="form-select" required>
                            <option value="">-- Pilih Sales --</option>
                            <?php while ($s = $salesList->fetch_assoc()): ?>
                                <option value="<?= $s['id'] ?>" <?= $s['id'] == $data['id_sales'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['nama_lengkap']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= $data['tanggal'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required value="<?= $data['jumlah'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="belum lunas" <?= $data['status'] === 'belum lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                            <option value="lunas" <?= $data['status'] === 'lunas' ? 'selected' : '' ?>>Lunas</option>
                        </select>
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