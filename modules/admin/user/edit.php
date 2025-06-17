<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once CONFIG_PATH . '/koneksi.php';
require_once AUTH_PATH . '/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=user");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=user");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM user WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=user");
    exit;
}

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $role         = trim($_POST['role'] ?? '');

    if ($username === '' || $nama_lengkap === '' || $role === '') {
        header("Location: edit.php?id=$id&msg=kosong&obj=user");
        exit;
    }

    // Cek duplikat username kecuali diri sendiri
    $cek = $koneksi->prepare("SELECT id FROM user WHERE username = ? AND id != ?");
    $cek->bind_param("si", $username, $id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $cek->close();
        header("Location: edit.php?id=$id&msg=duplicate&obj=user");
        exit;
    }
    $cek->close();

    if ($password !== '') {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare("UPDATE user SET username=?, password=?, nama_lengkap=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $hashed, $nama_lengkap, $role, $id);
    } else {
        $stmt = $koneksi->prepare("UPDATE user SET username=?, nama_lengkap=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $nama_lengkap, $role, $id);
    }

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=user");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=user");
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
                <div class="card-title mb-0">Edit Pengguna</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required value="<?= htmlspecialchars($data['username']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required value="<?= htmlspecialchars($data['nama_lengkap']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (kosongkan jika tidak ingin diubah)</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="admin" <?= $data['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="manajer" <?= $data['role'] === 'manajer' ? 'selected' : '' ?>>Manajer</option>
                            <option value="karyawan" <?= $data['role'] === 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                            <option value="sales" <?= $data['role'] === 'sales' ? 'selected' : '' ?>>Sales</option>
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