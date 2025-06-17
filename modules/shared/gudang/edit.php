<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=gudang");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=gudang");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM gudang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=gudang");
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_gudang = trim($_POST['nama_gudang'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($nama_gudang === '' || $alamat === '') {
        header("Location: edit.php?id=$id&msg=kosong&obj=gudang");
        exit;
    }

    // Cek duplikat nama (kecuali data sendiri)
    $cek = $koneksi->prepare("SELECT id FROM gudang WHERE nama_gudang = ? AND id != ?");
    $cek->bind_param("si", $nama_gudang, $id);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {
        $cek->close();
        header("Location: edit.php?id=$id&msg=duplicate&obj=gudang");
        exit;
    }
    $cek->close();

    $stmt = $koneksi->prepare("UPDATE gudang SET nama_gudang = ?, alamat = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nama_gudang, $alamat, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=gudang");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=gudang");
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
                <div class="card-title mb-0">Edit Data Gudang</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="nama_gudang" class="form-label">Nama Gudang</label>
                        <input type="text" name="nama_gudang" id="nama_gudang" class="form-control" required value="<?= htmlspecialchars($data['nama_gudang']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" required><?= htmlspecialchars($data['alamat']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fe fe-save"></i> Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>