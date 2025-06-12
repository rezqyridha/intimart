<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- JavaScript Files -->
<script src="<?= ASSETS_URL ?>/libs/@popperjs/core/umd/popper.min.js"></script>
<script src="<?= ASSETS_URL ?>/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= ASSETS_URL ?>/js/defaultmenu.min.js"></script>
<script src="<?= ASSETS_URL ?>/libs/node-waves/waves.min.js"></script>
<script src="<?= ASSETS_URL ?>/libs/simplebar/simplebar.min.js"></script>
<script src="<?= ASSETS_URL ?>/js/simplebar.js"></script>
<script src="<?= ASSETS_URL ?>/libs/@simonwep/pickr/pickr.es5.min.js"></script>
<script src="<?= ASSETS_URL ?>/libs/jsvectormap/js/jsvectormap.min.js"></script>
<script src="<?= ASSETS_URL ?>/libs/jsvectormap/maps/world-merc.js"></script>
<script src="<?= ASSETS_URL ?>/libs/apexcharts/apexcharts.min.js"></script>
<script src="<?= ASSETS_URL ?>/js/index.js"></script>
<script src="<?= ASSETS_URL ?>/js/custom-switcher.min.js"></script>
<script src="<?= ASSETS_URL ?>/js/custom.js"></script>


<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<!-- Custom Notifier -->
<script src="<?= ASSETS_URL ?>/js/notifier.js"></script>

<script>
    function loadNotifStok() {
        fetch("<?= BASE_URL ?>/modules/shared/notifikasi_stok/api_dropdown.php")
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('notif-stok-badge');
                const body = document.getElementById('stokNotifBody');

                if (data.success && data.items.length > 0) {
                    badge.innerText = data.total;
                    badge.style.display = 'inline-block';

                    let html = '';
                    data.items.forEach(item => {
                        html += `
                        <a href="<?= BASE_URL ?>/modules/shared/notifikasi_stok/index.php?highlight=${item.id}" class="dropdown-item d-flex justify-content-between align-items-center">
                            <span>${item.nama} (${item.satuan})</span>
                            <span class="badge bg-danger">${item.stok}</span>
                        </a>
                    `;
                    });

                    body.innerHTML = html;
                } else {
                    badge.style.display = 'none';
                    body.innerHTML = '<div class="text-muted text-center py-3">Tidak ada stok menipis</div>';
                }
            })
            .catch(() => {
                badge.style.display = 'none';
                document.getElementById('stokNotifBody').innerHTML = '<div class="text-danger text-center py-3">Gagal memuat</div>';
            });
    }

    // Panggil saat load dan setiap 1 menit
    loadNotifStok();
    setInterval(loadNotifStok, 60000);
</script>