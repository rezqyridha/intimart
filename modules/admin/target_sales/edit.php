<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=target");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=target");
    exit;
}

// Ambil data
$stmt = $koneksi->prepare("SELECT * FROM target_sales WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Cek jika data ditemukan
if (!$data) {
    header("Location: index.php?msg=invalid&obj=target");
    exit;
}

// Hitung realisasi langsung dari penjualan
$stmtRealisasi = $koneksi->prepare("
    SELECT IFNULL(SUM(harga_total), 0) AS realisasi
    FROM penjualan
    WHERE id_sales = ? AND DATE_FORMAT(tanggal, '%Y-%m') = ?
");
$stmtRealisasi->bind_param("is", $data['id_sales'], $data['bulan']);
$stmtRealisasi->execute();
$realisasi = $stmtRealisasi->get_result()->fetch_assoc()['realisasi'];
$stmtRealisasi->close();

if ($realisasi > $data['target']) {
    header("Location: index.php?msg=locked&obj=target");
    exit;
}

// Proteksi jika bulan sudah lewat
$bulan_now = date('Y-m');
if ($data['bulan'] < $bulan_now) {
    header("Location: index.php?msg=locked&obj=target");
    exit;
}

// Ambil list sales
$salesListResult = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales' ORDER BY nama_lengkap ASC");
$salesList = [];
if ($salesListResult) {
    while ($row = $salesListResult->fetch_assoc()) {
        $salesList[] = $row;
    }
    $salesListResult->free();
} else {
    // Handle query error (optional: log error)
    header("Location: index.php?msg=error&obj=saleslist");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sales = intval($_POST['id_sales'] ?? 0);
    $bulan    = trim($_POST['bulan'] ?? '');
    $target   = trim($_POST['target'] ?? '');

    if ($id_sales <= 0 || empty($bulan) || empty($target)) {
        header("Location: edit.php?id=$id&msg=kosong&obj=target");
        exit;
    }

    if (!is_numeric($target) || $target <= 0) {
        header("Location: edit.php?id=$id&msg=invalid&obj=target");
        exit;
    }

    if ($bulan < date('Y-m')) {
        header("Location: edit.php?id=$id&msg=invalid&obj=target");
        exit;
    }

    // Validasi sales
    $cekSales = $koneksi->prepare("SELECT id FROM user WHERE id = ? AND role = 'sales'");
    $cekSales->bind_param("i", $id_sales);
    $cekSales->execute();
    $cekSales->store_result();
    if ($cekSales->num_rows === 0) {
        $cekSales->close();
        header("Location: edit.php?id=$id&msg=invalid&obj=target");
        exit;
    }
    $cekSales->close();

    // Cek duplikat (kecuali data ini sendiri)
    $cekDup = $koneksi->prepare("SELECT id FROM target_sales WHERE id_sales = ? AND bulan = ? AND id != ?");
    $cekDup->bind_param("isi", $id_sales, $bulan, $id);
    $cekDup->execute();
    $cekDup->store_result();
    if ($cekDup->num_rows > 0) {
        $cekDup->close();
        header("Location: edit.php?id=$id&msg=duplicate&obj=target");
        exit;
    }
    $cekDup->close();

    // Update
    $stmt = $koneksi->prepare("UPDATE target_sales SET id_sales=?, bulan=?, target=? WHERE id=?");
    $stmt->bind_param("isdi", $id_sales, $bulan, $target, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=target");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=target");
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
                <div class="card-title mb-0">Edit Target Sales</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="id_sales" class="form-label">Sales</label>
                        <select name="id_sales" id="id_sales" class="form-select" required>
                            <option value="">-- Pilih Sales --</option>
                            <?php foreach ($salesList as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= $s['id'] == $data['id_sales'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['nama_lengkap']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bulan" class="form-label">Bulan</label>
                        <input type="month" name="bulan" id="bulan" class="form-control" value="<?= $data['bulan'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="target" class="form-label">Target (Rp)</label>
                        <input type="number" name="target" id="target" class="form-control" value="<?= $data['target'] ?>" min="1" required>
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