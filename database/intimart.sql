USE intimart;

-- 7.1 Tampilkan data restok + supplier
SELECT rs.id, s.nama_supplier, rs.tgl_pesan, rs.status
FROM restok_supplier rs
JOIN supplier s ON rs.id_supplier = s.id;

-- Expected Output:
-- | id | nama_supplier     | tgl_pesan  | status   |
-- |----|-------------------|------------|----------|
-- | 1  | CV Sumber Jaya    | 2025-05-01 | diproses |
-- | 2  | PT Mega Distribusi| 2025-05-02 | dikirim  |

-- 7.2 Tampilkan detail restok barang
SELECT dr.id, b.nama_barang, dr.jumlah, rs.id AS restok_id
FROM detail_restok dr
JOIN barang b ON dr.id_barang = b.id
JOIN restok_supplier rs ON dr.id_restok = rs.id;

-- Expected Output:
-- | id | nama_barang | jumlah | restok_id |
-- |----|-------------|--------|-----------|
-- | 1  | Sabun Mandi | 100    | 1         |
-- | 2  | Susu Kotak  | 50     | 1         |
-- | 3  | Sabun Mandi | 150    | 2         |

-- 7.3 Tampilkan barang yang akan expired
SELECT bk.id, b.nama_barang, bk.tanggal_expired, bk.jumlah, bk.lokasi
FROM barang_kadaluarsa bk
JOIN barang b ON bk.id_barang = b.id;

-- Expected Output:
-- | id | nama_barang | tanggal_expired | jumlah | lokasi        |
-- |----|-------------|------------------|--------|----------------|
-- | 1  | Sabun Mandi | 2025-06-10       | 20     | Gudang Utama   |
-- | 2  | Susu Kotak  | 2025-06-15       | 15     | Gudang Cabang  |

-- 7.4 Laporan Target vs Realisasi
SELECT u.nama_lengkap, ts.bulan, ts.target, ts.realisasi
FROM target_sales ts
JOIN user u ON ts.id_sales = u.id;

-- Expected Output:
-- | nama_lengkap     | bulan     | target | realisasi |
-- |------------------|-----------|--------|-----------|
-- | Sales Lapangan   | 2025-05   | 150    | 120       |
