<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if (!in_array($_SESSION['role'], ['admin', 'karyawan'])) {
    header("Location: index.php?msg=unauthorized&obj=stok_fisik");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=stok_fisik");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM stok_fisik WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=stok_fisik");
    exit;
}

// Tidak boleh edit jika sudah dikoreksi
if ((int)$data['koreksi'] === 1) {
    header("Location: index.php?msg=locked&obj=stok_fisik");
    exit;
}

// Ambil daftar barang dan gudang
$barangList = $koneksi->query("SELECT id, nama_barang, satuan FROM barang ORDER BY nama_barang ASC");
$gudangList = $koneksi->query("SELECT id, nama_gudang FROM gudang ORDER BY nama_gudang ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang     = (int)($_POST['id_barang'] ?? 0);
    $id_gudang     = (int)($_POST['id_gudang'] ?? 0);
    $jumlah_fisik  = (int)($_POST['jumlah_fisik'] ?? 0);
    $tanggal       = $_POST['tanggal'] ?? '';
    $keterangan    = trim($_POST['keterangan'] ?? '');
    $koreksi       = isset($_POST['koreksi']) ? 1 : 0;
    $stok_sistem   = null;

    if ($id_barang <= 0 || $id_gudang <= 0 || $jumlah_fisik <= 0 || empty($tanggal)) {
        header("Location: edit.php?id=$id&msg=kosong&obj=stok_fisik");
        exit;
    }

    // Koreksi? Hitung ulang stok sistem
    if ($koreksi === 1) {
        $q = "
            SELECT 
                IFNULL(masuk.total_masuk, 0)
              - (IFNULL(keluar.total_keluar, 0) + IFNULL(pj.total_terjual, 0) - IFNULL(retur.total_retur, 0)) AS stok_akhir
            FROM barang b
            LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_masuk FROM barang_masuk GROUP BY id_barang) masuk ON b.id = masuk.id_barang
            LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_keluar FROM barang_keluar GROUP BY id_barang) keluar ON b.id = keluar.id_barang
            LEFT JOIN (SELECT id_barang, SUM(jumlah) AS total_terjual FROM penjualan GROUP BY id_barang) pj ON b.id = pj.id_barang
            LEFT JOIN (
                SELECT p.id_barang, SUM(r.jumlah) AS total_retur
                FROM retur_penjualan r JOIN penjualan p ON r.id_penjualan = p.id
                GROUP BY p.id_barang
            ) retur ON b.id = retur.id_barang
            WHERE b.id = ?
        ";
        $stmt2 = $koneksi->prepare($q);
        $stmt2->bind_param("i", $id_barang);
        $stmt2->execute();
        $stmt2->bind_result($stok_sistem);
        $stmt2->fetch();
        $stmt2->close();
    }

    // Update
    $stmt = $koneksi->prepare("UPDATE stok_fisik SET id_barang=?, id_gudang=?, jumlah_fisik=?, stok_sistem=?, koreksi=?, tanggal=?, keterangan=? WHERE id=?");
    $stmt->bind_param("iiiisssi", $id_barang, $id_gudang, $jumlah_fisik, $stok_sistem, $koreksi, $tanggal, $keterangan, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=stok_fisik");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=stok_fisik");
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
                <div class="card-title mb-0">Edit Data Stok Fisik</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Barang</label>
                        <select class="form-select" name="id_barang" id="id_barang" required>
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
                        <label for="jumlah_fisik" class="form-label">Jumlah Fisik</label>
                        <input type="number" name="jumlah_fisik" id="jumlah_fisik" class="form-control" required value="<?= $data['jumlah_fisik'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= $data['tanggal'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"><?= htmlspecialchars($data['keterangan']) ?></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="koreksi" id="koreksi" value="1" <?= $data['koreksi'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="koreksi">Tandai sebagai koreksi stok sistem</label>
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