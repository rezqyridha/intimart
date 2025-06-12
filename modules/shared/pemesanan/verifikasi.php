<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=pemesanan");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=pemesanan");
    exit;
}

// Ambil data pemesanan dengan status = menunggu
$stmt = $koneksi->prepare("
    SELECT p.*, b.nama_barang, u.nama_lengkap AS nama_sales
    FROM pemesanan p
    JOIN barang b ON p.id_barang = b.id
    JOIN user u ON p.id_sales = u.id
    WHERE p.id = ? AND p.status = 'menunggu'
");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    header("Location: index.php?msg=locked_or_notfound&obj=pemesanan");
    exit;
}

// Proses Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    if (!in_array($status, ['disetujui', 'ditolak'])) {
        header("Location: verifikasi.php?id=$id&msg=invalid&obj=pemesanan");
        exit;
    }

    $stmt = $koneksi->prepare("
        UPDATE pemesanan
        SET status = ?, tanggal_direspon = NOW()
        WHERE id = ? AND status = 'menunggu'
    ");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: index.php?msg=verified&obj=pemesanan");
    } else {
        header("Location: verifikasi.php?id=$id&msg=nochange&obj=pemesanan");
    }
    exit;
}
?>

<?php
require_once LAYOUTS_PATH . '/head.php';
require_once LAYOUTS_PATH . '/header.php';
require_once LAYOUTS_PATH . '/topbar.php';
require_once LAYOUTS_PATH . '/sidebar.php';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="card custom-card mt-5">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Verifikasi Pemesanan</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>

            <form method="post" action="verifikasi.php?id=<?= $id ?>">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_barang']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" class="form-control" value="<?= $data['jumlah'] ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" disabled><?= htmlspecialchars($data['catatan']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sales</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_sales']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tindakan</label>
                        <select name="status" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="disetujui">Setujui</option>
                            <option value="ditolak">Tolak</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-success"><i class="fe fe-check me-1"></i> Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>