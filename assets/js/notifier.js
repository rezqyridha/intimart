document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const msg = params.get("msg");
    const obj = params.get("obj");

    const notifications = {
        success: {
            icon: "success",
            title: "Berhasil",
            text: "Operasi berhasil dilakukan.",
        },
        added: {
            icon: "success",
            title: "Sukses",
            text: "Data berhasil ditambahkan.",
        },
        updated: {
            icon: "success",
            title: "Diperbarui",
            text: "Data berhasil diperbarui.",
        },
        deleted: {
            icon: "success",
            title: "Terhapus",
            text: "Data berhasil dihapus.",
        },
        reset: {
            icon: "info",
            title: "Password Direset",
            text: "Password berhasil direset ke default (user123).",
        },
        failed: {
            icon: "error",
            title: "Gagal",
            text: "Tidak dapat menyimpan data ke database.",
        },
        error: {
            icon: "error",
            title: "Kesalahan",
            text: "Terjadi kesalahan saat memproses.",
        },
        kosong: {
            icon: "warning",
            title: "Form Tidak Lengkap",
            get text() {
                return getKosongText(obj);
            },
        },
        duplicate: {
            icon: "warning",
            title: "Duplikat Data",
            get text() {
                return getDuplicateText(obj);
            },
        },
        duplikat: {
            icon: "warning",
            title: "Data Duplikat",
            get text() {
                return getDuplicateText(obj);
            },
        },
        invalid: {
            icon: "warning",
            title: "Tidak Valid",
            get text() {
                return getInvalidText(obj);
            },
        },
        fk_blocked: {
            icon: "error",
            title: "Tidak Bisa Dihapus",
            get text() {
                return getFKBlockedText(obj);
            },
        },
        unauthorized: {
            icon: "error",
            title: "Akses Ditolak",
            get text() {
                return getUnauthorizedText(obj);
            },
        },
        stok_limit: {
            icon: "error",
            title: "Stok Tidak Cukup",
            text: "Jumlah barang melebihi stok yang tersedia.",
        },
        melebihi: { icon: "warning", title: "Jumlah Melebihi", text: "" },
        locked: {
            icon: "warning",
            title: "Terkunci",
            get text() {
                return getLockedText(obj);
            },
        },
        nochange: {
            icon: "info",
            title: "Tidak Ada Perubahan",
            get text() {
                return getNochangeText(obj);
            },
        },
    };

    if (msg && notifications[msg]) {
        if (msg === "melebihi" && obj === "retur") {
            const maks = params.get("maks") || "?";
            notifications[
                msg
            ].text = `Jumlah retur tidak boleh melebihi total penjualan (${maks} unit).`;
        }

        Swal.fire({
            ...notifications[msg],
            timer: 2000,
            showConfirmButton: false,
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    }
});

// === Handlers ===
function getLockedText(obj) {
    const map = {
        barang: "Barang sudah dikunci dan tidak dapat diubah.",
        barang_masuk: "Data barang masuk sudah dikunci.",
        barang_keluar: "Data barang keluar sudah dikunci.",
        stok: "Data stok tidak dapat diubah.",
        stok_fisik: "Data stok fisik tidak dapat diubah.",
        penjualan: "Penjualan sudah dikunci dan tidak dapat diubah.",
        retur: "Retur sudah dikunci dan tidak dapat diubah.",
        pembayaran: "Pembayaran sudah dikunci.",
        pembayaran_penjualan: "Pembayaran penjualan sudah dikunci.",
        pembayaran_retur: "Pembayaran retur sudah dikunci.",
        rekonsiliasi: "Rekonsiliasi tidak dapat diubah.",
        pelanggan: "Data pelanggan tidak dapat diubah.",
        gudang: "Gudang sudah dikunci.",
        supplier: "Supplier tidak dapat diubah.",
        produk: "Produk tidak dapat diubah.",
        tidaklaku: "Produk tidak laku sudah dikunci.",
        kadaluarsa: "Data kadaluarsa sudah dikunci.",
        sales: "Data sales tidak dapat diubah.",
        user: "Data user sudah dikunci.",
        target: "Target sudah dikunci.",
        laporan: "Laporan sudah dikunci.",
        kas: "Transaksi kas tidak dapat diubah.",
        pengeluaran: "Data pengeluaran tidak dapat diubah.",
        pemasukan: "Data pemasukan tidak dapat diubah.",
        transaksi: "Data transaksi tidak dapat diubah.",
        notifikasi: "Notifikasi sudah terkirim.",
        pemesanan: "Pemesanan sudah dikunci.",
    };
    return map[obj] || "Data sudah dikunci dan tidak dapat diubah.";
}

function getDuplicateText(obj) {
    const map = {
        barang: "Barang sudah terdaftar.",
        user: "Username sudah digunakan.",
        pelanggan: "Pelanggan sudah terdaftar.",
        gudang: "Gudang sudah terdaftar.",
        produk: "Produk sudah terdaftar.",
        supplier: "Supplier sudah terdaftar.",
        sales: "Sales sudah terdaftar.",
        penjualan: "Penjualan sudah ada.",
        pengiriman: "Pengiriman sudah terdaftar.",
        retur: "Retur sudah ada.",
        pembayaran: "Pembayaran sudah ada.",
        pembayaran_penjualan: "Pembayaran penjualan sudah ada.",
        pembayaran_retur: "Pembayaran retur sudah ada.",
        laporan: "Laporan sudah pernah dibuat.",
        notifikasi: "Notifikasi serupa sudah ada.",
        target: "Target sudah ditetapkan.",
        stok: "Data stok sudah ada.",
        stok_fisik: "Stok fisik sudah dicatat.",
        pemesanan: "Pemesanan sudah terdaftar.",
        kadaluarsa: "Barang kadaluarsa sudah dicatat.",
        tidaklaku: "Produk tidak laku sudah tercatat.",
    };
    return map[obj] || "Data yang Anda masukkan sudah ada.";
}

function getKosongText(obj) {
    const map = {
        barang: "Semua field barang wajib diisi.",
        user: "Username, nama, dan password wajib diisi.",
        pelanggan: "Data pelanggan tidak boleh kosong.",
        gudang: "Field gudang tidak boleh kosong.",
        produk: "Semua field produk wajib diisi.",
        sales: "Data sales tidak boleh kosong.",
        penjualan: "Field penjualan harus lengkap.",
        retur: "Lengkapi seluruh field retur.",
        pembayaran: "Lengkapi detail pembayaran.",
        target: "Semua field target wajib diisi.",
        stok: "Field stok tidak boleh kosong.",
        stok_fisik: "Data stok fisik wajib diisi.",
        pemesanan: "Data pemesanan tidak boleh kosong.",
        kadaluarsa: "Data kadaluarsa tidak boleh kosong.",
        tidaklaku: "Data produk tidak laku harus lengkap.",
        kas: "Form transaksi kas harus diisi.",
        pengeluaran: "Lengkapi field pengeluaran.",
        pemasukan: "Lengkapi field pemasukan.",
        laporan: "Field laporan wajib diisi.",
        notifikasi: "Field notifikasi tidak boleh kosong.",
    };
    return map[obj] || "Harap lengkapi semua field yang dibutuhkan.";
}

function getInvalidText(obj) {
    const map = {
        barang: "Barang tidak ditemukan.",
        user: "User tidak valid.",
        pelanggan: "Pelanggan tidak valid.",
        gudang: "Gudang tidak ditemukan.",
        produk: "Produk tidak valid.",
        sales: "Sales tidak valid.",
        penjualan: "Penjualan tidak ditemukan.",
        retur: "Retur tidak valid.",
        pembayaran: "Pembayaran tidak ditemukan.",
        laporan: "Laporan tidak ditemukan.",
        target: "Target tidak valid.",
        stok: "Data stok tidak ditemukan.",
        stok_fisik: "Data stok fisik tidak ditemukan.",
        pemesanan: "Data pemesanan tidak ditemukan.",
        kadaluarsa: "Data kadaluarsa tidak valid.",
        tidaklaku: "Data produk tidak laku tidak ditemukan.",
        pengiriman: "Pengiriman tidak valid.",
        kas: "Transaksi kas tidak ditemukan.",
        pengeluaran: "Pengeluaran tidak valid.",
        pemasukan: "Pemasukan tidak valid.",
        notifikasi: "Notifikasi tidak ditemukan.",
        transaksi: "Transaksi tidak ditemukan.",
        rekonsiliasi: "Data rekonsiliasi tidak ditemukan.",
    };
    return map[obj] || "Data tidak valid atau tidak ditemukan.";
}

function getFKBlockedText(obj) {
    const map = {
        barang: "Barang tidak dapat dihapus karena terhubung dengan penjualan atau stok.",
        user: "User tidak dapat dihapus karena sedang digunakan.",
        pelanggan: "Pelanggan memiliki transaksi aktif.",
        gudang: "Gudang digunakan oleh stok atau distribusi.",
        produk: "Produk digunakan oleh penjualan atau retur.",
        supplier: "Supplier terhubung dengan data pembelian.",
        sales: "Sales sudah memiliki data transaksi.",
        penjualan: "Penjualan sudah digunakan di laporan.",
        retur: "Retur terhubung dengan penjualan.",
        pembayaran: "Pembayaran memiliki referensi aktif.",
        laporan: "Laporan tidak dapat dihapus karena arsip.",
        notifikasi: "Notifikasi tidak dapat dihapus.",
        target: "Target digunakan dalam laporan.",
        stok: "Stok digunakan di laporan atau modul lain.",
        stok_fisik: "Data koreksi stok terhubung.",
        pemesanan: "Pemesanan sudah diproses.",
        pengiriman: "Pengiriman tidak dapat dihapus.",
        kadaluarsa: "Data kadaluarsa dikunci.",
        tidaklaku: "Produk tidak laku terikat laporan.",
        kas: "Transaksi kas sudah tercatat.",
        pengeluaran: "Pengeluaran sudah terverifikasi.",
        pemasukan: "Pemasukan sudah tercatat.",
        transaksi: "Transaksi sudah dicatat.",
        pembayaran_penjualan: "Sudah tercatat dan tidak bisa dihapus.",
        pembayaran_retur: "Pembayaran retur sudah diverifikasi.",
    };
    return (
        map[obj] || "Data tidak dapat dihapus karena digunakan di modul lain."
    );
}

function getUnauthorizedText(obj) {
    const map = {
        barang: "Anda tidak memiliki akses ke data barang.",
        user: "Anda tidak memiliki akses ke manajemen user.",
        pelanggan: "Anda tidak memiliki akses ke pelanggan.",
        gudang: "Anda tidak memiliki akses ke gudang.",
        supplier: "Anda tidak memiliki akses ke supplier.",
        produk: "Anda tidak memiliki akses ke produk.",
        sales: "Anda tidak memiliki akses ke sales.",
        penjualan: "Anda tidak memiliki akses ke penjualan.",
        retur: "Anda tidak memiliki akses ke retur.",
        pembayaran: "Anda tidak memiliki akses ke pembayaran.",
        kas: "Anda tidak memiliki akses ke kas.",
        pengeluaran: "Anda tidak memiliki akses ke pengeluaran.",
        pemasukan: "Anda tidak memiliki akses ke pemasukan.",
        laporan: "Anda tidak memiliki akses ke laporan.",
        notifikasi: "Anda tidak memiliki akses ke notifikasi.",
        target: "Anda tidak memiliki akses ke target sales.",
        stok: "Anda tidak memiliki akses ke stok.",
        stok_fisik: "Anda tidak memiliki akses ke koreksi stok.",
        pemesanan: "Anda tidak memiliki akses ke pemesanan.",
        pengiriman: "Anda tidak memiliki akses ke pengiriman.",
        transaksi: "Anda tidak memiliki akses ke transaksi.",
        rekonsiliasi: "Anda tidak memiliki akses ke rekonsiliasi.",
    };
    return map[obj] || "Anda tidak memiliki izin untuk mengakses halaman ini.";
}

function getNochangeText(obj) {
    const map = {
        barang: "Tidak ada perubahan data barang.",
        user: "Data user tidak mengalami perubahan.",
        pelanggan: "Data pelanggan tetap sama.",
        gudang: "Data gudang tidak berubah.",
        supplier: "Data supplier tidak berubah.",
        produk: "Data produk tetap sama.",
        sales: "Data sales tetap sama.",
        penjualan: "Data penjualan tidak berubah.",
        retur: "Tidak ada perubahan retur.",
        pembayaran: "Tidak ada perubahan pembayaran.",
        kas: "Tidak ada perubahan kas.",
        pengeluaran: "Data pengeluaran tidak berubah.",
        pemasukan: "Data pemasukan tidak berubah.",
        laporan: "Laporan tidak mengalami perubahan.",
        notifikasi: "Notifikasi tidak berubah.",
        target: "Target tidak berubah.",
        stok: "Data stok tidak berubah.",
        stok_fisik: "Koreksi stok tidak berubah.",
        pemesanan: "Data pemesanan tidak berubah.",
        pengiriman: "Data pengiriman tidak berubah.",
        kadaluarsa: "Data kadaluarsa tidak berubah.",
        tidaklaku: "Produk tidak laku tidak mengalami perubahan.",
        transaksi: "Data transaksi tidak berubah.",
        rekonsiliasi: "Data rekonsiliasi tidak berubah.",
    };
    return map[obj] || "Tidak ada perubahan yang dilakukan.";
}

// 🔁 Confirm Delete
function confirmDelete(url) {
    Swal.fire({
        title: "Yakin ingin menghapus?",
        text: "Data akan dihapus secara permanen.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
