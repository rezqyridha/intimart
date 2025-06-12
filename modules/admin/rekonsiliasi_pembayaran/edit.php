<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/unauthorized.php");
    exit;
}

// 🔄 Submit form: Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = intval($_POST['id'] ?? 0);
    $catatan = trim($_POST['catatan'] ?? '');
    $status  = $_POST['status'] ?? '';

    if ($id <= 0 || $catatan === '' || !in_array($status, ['sesuai', 'tidak sesuai'])) {
        header("Location: edit.php?id=$id&msg=invalid&obj=rekonsiliasi");
        exit;
    }

    // 🚫 Cek jika data sudah final
    $cek = $koneksi->prepare("SELECT status FROM rekonsiliasi_pembayaran WHERE id = ?");
    $cek->bind_param("i", $id);
    $cek->execute();
    $result = $cek->get_result();
    $old = $result->fetch_assoc();

    if (!$old || $old['status'] === 'sudah_rekonsiliasi') {
        header("Location: index.php?msg=locked&obj=rekonsiliasi");
        exit;
    }

    // ✅ Update
    $stmt = $koneksi->prepare("UPDATE rekonsiliasi_pembayaran SET catatan = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $catatan, $status, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: index.php?msg=updated&obj=rekonsiliasi");
    } else {
        header("Location: edit.php?id=$id&msg=nochange&obj=rekonsiliasi");
    }
    exit;
}

// 📄 Tampilkan form edit
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=rekonsiliasi");
    exit;
}

$stmt = $koneksi->prepare("
    SELECT rp.*, b.nama_barang, b.satuan, p.metode, p.jumlah_bayar
    FROM rekonsiliasi_pembayaran rp
    JOIN pembayaran p ON rp.id_pembayaran = p.id
    JOIN penjualan j ON p.id_penjualan = j.id
    JOIN barang b ON j.id_barang = b.id
    WHERE rp.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    header("Location: index.php?msg=notfound&obj=rekonsiliasi");
    exit;
}

if ($data['status'] === 'sudah_rekonsiliasi') {
    header("Location: index.php?msg=locked&obj=rekonsiliasi");
    exit;
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
                <h5 class="card-title mb-0">Edit Rekonsiliasi Pembayaran</h5>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>

            <form action="edit.php" method="post">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Informasi Pembayaran</label>
                        <input type="text" class="form-control" disabled
                            value="<?= htmlspecialchars($data['nama_barang']) ?> (<?= $data['satuan'] ?>) - <?= $data['metode'] ?> - Rp<?= number_format($data['jumlah_bayar'], 0, ',', '.') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3" required><?= htmlspecialchars($data['catatan']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="sesuai" <?= $data['status'] === 'sesuai' ? 'selected' : '' ?>>Sesuai</option>
                            <option value="tidak sesuai" <?= $data['status'] === 'tidak sesuai' ? 'selected' : '' ?>>Tidak Sesuai</option>
                        </select>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button class="btn btn-primary"><i class="fe fe-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
require_once LAYOUTS_PATH . '/scripts.php';
?>