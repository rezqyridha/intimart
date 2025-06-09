// notifier.js
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
        error: {
            icon: "error",
            title: "Kesalahan",
            text: "Terjadi kesalahan saat memproses.",
        },
        failed: {
            icon: "error",
            title: "Gagal",
            text: "Tidak dapat menyimpan data ke database.",
        },
        kosong: {
            icon: "warning",
            title: "Form Tidak Lengkap",
            text: getKosongText(obj),
        },
        duplicate: {
            icon: "warning",
            title: "Duplikat Data",
            text: getDuplicateText(obj),
        },
        duplikat: {
            icon: "warning",
            title: "Data Duplikat",
            text: getDuplicateText(obj),
        },
        invalid: {
            icon: "warning",
            title: "Tidak Valid",
            text: getInvalidText(obj),
        },
        fk_blocked: {
            icon: "error",
            title: "Tidak Bisa Dihapus",
            text: getFKBlockedText(obj),
        },
        unauthorized: {
            icon: "error",
            title: "Akses Ditolak",
            text: getUnauthorizedText(obj),
        },
        nochange: {
            icon: "info",
            title: "Tidak Ada Perubahan",
            text: getNochangeText(obj),
        },
    };

    if (msg && notifications[msg]) {
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

// === Global handler untuk seluruh modul Intimart ===

function getDuplicateText(obj) {
    const map = {
        barang: "Barang sudah terdaftar.",
        user: "Username sudah digunakan.",
        pelanggan: "Pelanggan sudah terdaftar.",
        gudang: "Gudang sudah ada.",
        produk: "Produk sudah ada.",
        supplier: "Supplier sudah terdaftar.",
        sales: "Sales sudah ada.",
        penjualan: "Penjualan sudah ada.",
        pengiriman: "Data pengiriman sudah ada.",
        retur: "Data retur sudah ada.",
        pembayaran: "Data pembayaran sudah ada.",
        laporan: "Laporan sudah pernah dibuat.",
        notifikasi: "Notifikasi serupa sudah dikirim.",
        target: "Target sudah ditetapkan sebelumnya.",
    };
    return map[obj] || "Data yang Anda masukkan sudah ada.";
}

function getKosongText(obj) {
    const map = {
        barang: "Semua field barang wajib diisi.",
        user: "Username dan password wajib diisi.",
        gudang: "Data gudang tidak boleh kosong.",
        penjualan: "Field penjualan harus lengkap.",
        pengiriman: "Data pengiriman belum lengkap.",
        retur: "Mohon isi seluruh field retur.",
        pembayaran: "Lengkapi detail pembayaran.",
        pelanggan: "Data pelanggan wajib diisi.",
        supplier: "Field supplier tidak boleh kosong.",
    };
    return map[obj] || "Harap lengkapi semua field yang dibutuhkan.";
}

function getInvalidText(obj) {
    const map = {
        barang: "Barang tidak ditemukan.",
        user: "User tidak ditemukan.",
        pelanggan: "Data pelanggan tidak valid.",
        gudang: "Gudang tidak valid atau tidak ditemukan.",
        penjualan: "Penjualan tidak valid.",
        pengiriman: "Pengiriman tidak ditemukan.",
        retur: "Retur tidak ditemukan.",
        pembayaran: "Pembayaran tidak ditemukan.",
    };
    return map[obj] || "Data tidak valid atau tidak ditemukan.";
}

function getFKBlockedText(obj) {
    const map = {
        barang: "Barang tidak dapat dihapus karena digunakan di modul lain seperti stok, penjualan, atau pengiriman.",
        user: "User tidak dapat dihapus karena terhubung dengan akun login aktif.",
        gudang: "Gudang tidak dapat dihapus karena digunakan di distribusi atau stok.",
        pelanggan: "Pelanggan memiliki riwayat transaksi.",
        supplier: "Supplier sudah digunakan di pembelian.",
        penjualan: "Penjualan sudah tercatat di laporan.",
        pengiriman: "Pengiriman sudah digunakan.",
    };
    return (
        map[obj] || "Data tidak dapat dihapus karena digunakan di modul lain."
    );
}

function getUnauthorizedText(obj) {
    const map = {
        barang: "Anda tidak memiliki hak akses untuk mengelola data barang.",
        pegawai: "Anda tidak diizinkan mengakses data pegawai.",
        user: "Anda tidak dapat mengakses manajemen user.",
        laporan: "Anda tidak memiliki izin melihat laporan ini.",
        spt: "Hanya role tertentu yang dapat mengelola SPT.",
        sppd: "Anda tidak dapat mengelola SPPD.",
    };
    return map[obj] || "Anda tidak memiliki izin untuk mengakses halaman ini.";
}

function getNochangeText(obj) {
    const map = {
        barang: "Tidak ada perubahan pada data barang.",
        pegawai: "Data pegawai tidak mengalami perubahan.",
        user: "Data user tidak berubah.",
        pelanggan: "Data pelanggan tetap sama.",
        sppd: "Tidak ada perubahan pada SPPD.",
        spt: "Tidak ada perubahan pada SPT.",
    };
    return map[obj] || "Tidak ada perubahan yang dilakukan.";
}

// 🔁 Konfirmasi Hapus
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
