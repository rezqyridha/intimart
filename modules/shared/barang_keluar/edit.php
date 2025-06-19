<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=barang_keluar");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM barang_keluar WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=barang_keluar");
    exit;
}

// Ambil daftar barang & gudang
$barangList = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");
$gudangList = $koneksi->query("SELECT id, nama_gudang FROM gudang ORDER BY nama_gudang ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang  = (int)($_POST['id_barang'] ?? 0);
    $id_gudang  = (int)($_POST['id_gudang'] ?? 0);
    $jumlah     = (int)($_POST['jumlah'] ?? 0);
    $tanggal    = $_POST['tanggal'] ?? '';
    $jenis      = $_POST['jenis'] ?? '';
    $tujuan     = trim($_POST['tujuan'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $id_user    = $_SESSION['id_user'] ?? 0;

    if ($id_barang <= 0 || $id_gudang <= 0 || $jumlah <= 0 || empty($tanggal) || empty($jenis) || $id_user <= 0) {
        header("Location: edit.php?id=$id&msg=kosong&obj=barang_keluar");
        exit;
    }

    // Validasi barang
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE id = ?");
    $cek->bind_param("i", $id_barang);
    $cek->execute();
    $cek->bind_result($barang_ada);
    $cek->fetch();
    $cek->close();

    if (!$barang_ada) {
        header("Location: edit.php?id=$id&msg=invalid&obj=barang_keluar");
        exit;
    }

    // Validasi gudang
    $cekGudang = $koneksi->prepare("SELECT COUNT(*) FROM gudang WHERE id = ?");
    $cekGudang->bind_param("i", $id_gudang);
    $cekGudang->execute();
    $cekGudang->bind_result($gudang_ada);
    $cekGudang->fetch();
    $cekGudang->close();

    if (!$gudang_ada) {
        header("Location: edit.php?id=$id&msg=invalid&obj=barang_keluar");
        exit;
    }

    // Update
    $stmt = $koneksi->prepare("UPDATE barang_keluar SET id_barang=?, id_gudang=?, jumlah=?, tanggal=?, jenis=?, tujuan=?, keterangan=? WHERE id=?");
    $stmt->bind_param("iiissssi", $id_barang, $id_gudang, $jumlah, $tanggal, $jenis, $tujuan, $keterangan, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=barang_keluar");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=barang_keluar");
    }
    $stmt->close();
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
                <div class="card-title mb-0">Edit Data Barang Keluar</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Nama Barang</label>
                        <select name="id_barang" id="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($b = $barangList->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>" <?= $b['id'] == $data['id_barang'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_gudang" class="form-label">Gudang</label>
                        <select name="id_gudang" id="id_gudang" class="form-select" required>
                            <option value="">-- Pilih Gudang --</option>
                            <?php while ($g = $gudangList->fetch_assoc()): ?>
                                <option value="<?= $g['id'] ?>" <?= $g['id'] == $data['id_gudang'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g['nama_gudang']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required value="<?= $data['jumlah'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= $data['tanggal'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis Keluar</label>
                        <select name="jenis" id="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <?php
                            $jenisList = ['internal', 'rusak', 'hilang', 'retur_supplier'];
                            foreach ($jenisList as $jenisVal) {
                                $selected = $data['jenis'] === $jenisVal ? 'selected' : '';
                                echo "<option value=\"$jenisVal\" $selected>" . ucfirst(str_replace('_', ' ', $jenisVal)) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tujuan" class="form-label">Tujuan</label>
                        <input type="text" name="tujuan" id="tujuan" class="form-control" value="<?= htmlspecialchars($data['tujuan']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"><?= htmlspecialchars($data['keterangan']) ?></textarea>
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