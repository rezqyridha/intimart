<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?msg=unauthorized&obj=penjualan");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php?msg=invalid&obj=penjualan");
    exit;
}

// Ambil data lama
$stmt = $koneksi->prepare("SELECT * FROM penjualan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php?msg=invalid&obj=penjualan");
    exit;
}

// Ambil daftar barang untuk dropdown + harga untuk JS
$barangList = $koneksi->query("SELECT id, nama_barang, satuan, harga_jual FROM barang ORDER BY nama_barang ASC");
$hargaArray = [];
while ($b = $barangList->fetch_assoc()) {
    $hargaArray[$b['id']] = $b['harga_jual'];
    $dropdownOptions[] = $b; // Simpan data barang untuk digunakan ulang
}

// Form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $tanggal   = $_POST['tanggal'] ?? '';

    if ($id_barang <= 0 || $jumlah <= 0 || empty($tanggal)) {
        header("Location: edit.php?id=$id&msg=kosong&obj=penjualan");
        exit;
    }

    // Ambil harga dari DB
    $stmt = $koneksi->prepare("SELECT harga_jual FROM barang WHERE id = ?");
    $stmt->bind_param("i", $id_barang);
    $stmt->execute();
    $stmt->bind_result($harga_jual);
    $stmt->fetch();
    $stmt->close();

    if (!$harga_jual) {
        header("Location: edit.php?id=$id&msg=invalidharga&obj=penjualan");
        exit;
    }

    $total = $jumlah * $harga_jual;

    $stmt = $koneksi->prepare("UPDATE penjualan SET id_barang=?, jumlah=?, harga_total=?, tanggal=? WHERE id=?");
    $stmt->bind_param("iidsi", $id_barang, $jumlah, $total, $tanggal, $id);

    if ($stmt->execute()) {
        header("Location: index.php?msg=updated&obj=penjualan");
    } else {
        header("Location: edit.php?id=$id&msg=failed&obj=penjualan");
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
                <div class="card-title mb-0">Edit Data Penjualan</div>
                <a href="index.php" class="btn btn-sm btn-dark">← Kembali</a>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Barang</label>
                        <select class="form-select" name="id_barang" id="id_barang" required onchange="hitungTotal()">
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($dropdownOptions as $item): ?>
                                <option value="<?= $item['id'] ?>" <?= ($item['id'] == $data['id_barang']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item['nama_barang']) ?> (<?= htmlspecialchars($item['satuan']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required value="<?= $data['jumlah'] ?>" oninput="hitungTotal()">
                    </div>
                    <div class="mb-3">
                        <label for="harga_total" class="form-label">Total Harga</label>
                        <input type="number" name="harga_total" id="harga_total" class="form-control" readonly value="<?= $data['harga_total'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= $data['tanggal'] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const hargaBarang = <?= json_encode($hargaArray) ?>;

    function hitungTotal() {
        const idBarang = document.getElementById('id_barang').value;
        const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
        const harga = hargaBarang[idBarang] || 0;
        document.getElementById('harga_total').value = harga * jumlah;
    }

    // Panggil saat halaman selesai dimuat agar langsung menghitung
    window.addEventListener('DOMContentLoaded', hitungTotal);
</script>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>