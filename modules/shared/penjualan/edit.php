<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/intimart/config/constants.php';
require_once AUTH_PATH . '/session.php';
require_once CONFIG_PATH . '/koneksi.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'sales') {
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
    $dropdownOptions[] = $b;
}

// Ambil data sales jika user admin
$salesOptions = [];
if ($_SESSION['role'] === 'admin') {
    $q = $koneksi->query("SELECT id, nama_lengkap FROM user WHERE role = 'sales' ORDER BY nama_lengkap ASC");
    while ($row = $q->fetch_assoc()) {
        $salesOptions[] = $row;
    }
}

// Form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $id_sales  = (int)($_POST['id_sales'] ?? 0);
    $jumlah    = (int)($_POST['jumlah'] ?? 0);
    $tanggal   = $_POST['tanggal'] ?? '';
    $status_pelunasan = $_POST['status_pelunasan'] ?? '';

    $allowed_status = ['belum lunas', 'lunas'];
    if (!in_array($status_pelunasan, $allowed_status)) {
        header("Location: edit.php?id=$id&msg=invalidstatus&obj=penjualan");
        exit;
    }

    if ($id_barang <= 0 || $jumlah <= 0 || empty($tanggal) || $id_sales <= 0) {
        header("Location: edit.php?id=$id&msg=kosong&obj=penjualan");
        exit;
    }

    // Jika user login sebagai sales, paksa id_sales
    if ($_SESSION['role'] === 'sales') {
        $id_sales = $_SESSION['id_user'];
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

    // Update data
    $stmt = $koneksi->prepare("UPDATE penjualan SET id_barang=?, id_sales=?, jumlah=?, harga_total=?, tanggal=?, status_pelunasan=? WHERE id=?");
    $stmt->bind_param("iiidssi", $id_barang, $id_sales, $jumlah, $total, $tanggal, $status_pelunasan, $id);

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
                        <label for="id_sales" class="form-label">Sales</label>
                        <?php if ($_SESSION['role'] === 'sales'): ?>
                            <input type="hidden" name="id_sales" value="<?= $_SESSION['id_user'] ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['nama_lengkap']) ?>" readonly>
                        <?php else: ?>
                            <select name="id_sales" id="id_sales" class="form-select" required>
                                <option value="" hidden>-- Pilih Sales --</option>
                                <?php foreach ($salesOptions as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($s['id'] == $data['id_sales']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
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
                    <div class="mb-3">
                        <label for="status_pelunasan" class="form-label">Status Pelunasan</label>
                        <select name="status_pelunasan" id="status_pelunasan" class="form-select" required>
                            <option value="belum lunas" <?= ($data['status_pelunasan'] === 'belum lunas') ? 'selected' : '' ?>>Belum Lunas</option>
                            <option value="lunas" <?= ($data['status_pelunasan'] === 'lunas') ? 'selected' : '' ?>>Lunas</option>
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

<script>
    const hargaBarang = <?= json_encode($hargaArray) ?>;

    function hitungTotal() {
        const idBarang = document.getElementById('id_barang').value;
        const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
        const harga = hargaBarang[idBarang] || 0;
        document.getElementById('harga_total').value = harga * jumlah;
    }

    window.addEventListener('DOMContentLoaded', hitungTotal);
</script>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
<?php require_once LAYOUTS_PATH . '/scripts.php'; ?>