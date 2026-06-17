-- ==============================================================================
-- TUGAS AKHIR MATA KULIAH PEMROGRAMAN SQL - TAHAP 2
-- PROYEK APLIKASI PRAKTEK DOKTER MANDIRI
-- MANIPULASI DATA (CRUD), VIEWS, DAN COMPLEX QUERIES
-- ==============================================================================

USE db_praktek_dokter;

-- ==============================================================================
-- A. MANIPULASI DATA / CRUD OPERATIONS (5 ENTITAS UTAMA)
-- ==============================================================================

-- 1. CRUD: PASIEN
-- Menambahkan pasien baru
INSERT INTO Pasien (nama, tgl_lahir, jenis_kelamin, alamat, no_telpon) 
VALUES ('Raditya Dika', '1984-12-28', 'L', 'Jl. Komedi No 1', '081299998888');
-- Mengubah nomor telepon pasien
UPDATE Pasien SET no_telpon = '081299997777' WHERE nama = 'Raditya Dika';
-- Menghapus data pasien
DELETE FROM Pasien WHERE nama = 'Raditya Dika';

-- 2. CRUD: OBAT
-- Menambahkan obat baru
INSERT INTO Obat (nama_obat, jenis_obat, stok, harga_satuan, tgl_kadaluarsa) 
VALUES ('Vitamin D3', 'Tablet', 100, 15000.00, '2026-12-31');
-- Menambah stok obat
UPDATE Obat SET stok = stok + 50 WHERE nama_obat = 'Vitamin D3';
-- Menghapus obat
DELETE FROM Obat WHERE nama_obat = 'Vitamin D3';

-- 3. CRUD: DOKTER
-- Menambahkan dokter baru
INSERT INTO Dokter (nama, spesialisasi, no_izin_praktek, no_telpon) 
VALUES ('Dr. Tirta', 'Umum', 'SIP-999', '081399999999');
-- Memperbarui spesialisasi dokter
UPDATE Dokter SET spesialisasi = 'Spesialis Penyakit Dalam' WHERE nama = 'Dr. Tirta';
-- Menghapus dokter berdasarkan nomor izin
DELETE FROM Dokter WHERE no_izin_praktek = 'SIP-999';

-- 4. CRUD: KUNJUNGAN
-- Mendaftarkan kunjungan pasien (Pasien ID 1 berobat ke Dokter ID 1)
INSERT INTO Kunjungan (patient_id, dokter_id, waktu_datang, status, jenis_layanan) 
VALUES (1, 1, NOW(), 'Menunggu', 'Konsultasi Umum');
-- Mengubah status kunjungan menjadi selesai
UPDATE Kunjungan SET status = 'Selesai', waktu_selesai = NOW() WHERE kunjungan_id = 1;
-- (Operasi DELETE tidak dilakukan pada Kunjungan untuk menjaga riwayat medis)

-- 5. CRUD: REKAM MEDIS
-- Menambahkan catatan rekam medis dari kunjungan di atas
INSERT INTO Rekam_Medis (kunjungan_id, tanggal_catatan, anamnesa, catatan_klinis) 
VALUES (1, NOW(), 'Pasien mengeluh demam dan pusing sejak 3 hari lalu', 'Suspect tipes ringan');
-- Memperbarui hasil pemeriksaan fisik
UPDATE Rekam_Medis SET pemeriksaan_fisik = 'Suhu tubuh 38.5 derajat celcius, tensi 110/70' WHERE kunjungan_id = 1;


-- ==============================================================================
-- B. PEMBUATAN VIEWS (5 VIEWS UNTUK REPORTING)
-- ==============================================================================

-- View 1: Laporan Stok Obat (Menampilkan obat yang stoknya di bawah 50)
CREATE VIEW v_stok_obat_kritis AS
SELECT obat_id, nama_obat, jenis_obat, stok, tgl_kadaluarsa
FROM Obat
WHERE stok < 50
ORDER BY stok ASC;

-- View 2: Laporan Rekam Medis Pasien Lengkap (Menggabungkan 4 Tabel)
CREATE VIEW v_rekam_medis_pasien AS
SELECT rm.record_id, p.nama AS nama_pasien, d.nama AS nama_dokter, 
       k.waktu_datang, rm.anamnesa, rm.catatan_klinis
FROM Rekam_Medis rm
JOIN Kunjungan k ON rm.kunjungan_id = k.kunjungan_id
JOIN Pasien p ON k.patient_id = p.patient_id
JOIN Dokter d ON k.dokter_id = d.dokter_id;

-- View 3: Laporan Tagihan Berjalan
CREATE VIEW v_laporan_tagihan AS
SELECT t.tagihan_id, p.nama AS nama_pasien, t.tanggal_tagihan, 
       t.total_tagihan, t.status, t.metode_pembayaran
FROM Tagihan t
JOIN Kunjungan k ON t.kunjungan_id = k.kunjungan_id
JOIN Pasien p ON k.patient_id = p.patient_id;

-- View 4: Jadwal Kunjungan Hari Ini
CREATE VIEW v_kunjungan_hari_ini AS
SELECT k.kunjungan_id, p.nama AS nama_pasien, d.nama AS nama_dokter, 
       k.waktu_datang, k.status
FROM Kunjungan k
JOIN Pasien p ON k.patient_id = p.patient_id
JOIN Dokter d ON k.dokter_id = d.dokter_id
WHERE DATE(k.waktu_datang) = CURDATE();

-- View 5: Daftar Obat Kadaluarsa Tahun Ini
CREATE VIEW v_obat_hampir_kadaluarsa AS
SELECT nama_obat, stok, tgl_kadaluarsa 
FROM Obat 
WHERE YEAR(tgl_kadaluarsa) = YEAR(CURDATE());


-- ==============================================================================
-- C. COMPLEX QUERIES (10 QUERY LANJUTAN)
-- ==============================================================================

-- 1. INNER JOIN & AGGREGATE: Menghitung total kunjungan per dokter
SELECT d.nama AS nama_dokter, COUNT(k.kunjungan_id) AS total_pasien_ditangani
FROM Dokter d
INNER JOIN Kunjungan k ON d.dokter_id = k.dokter_id
GROUP BY d.dokter_id;

-- 2. LEFT JOIN: Mencari pasien yang terdaftar tapi belum pernah berobat sama sekali
SELECT p.nama AS nama_pasien, p.no_telpon
FROM Pasien p
LEFT JOIN Kunjungan k ON p.patient_id = k.patient_id
WHERE k.kunjungan_id IS NULL;

-- 3. RIGHT JOIN & COALESCE: Menampilkan total obat diresepkan (termasuk yang belum pernah diresepkan)
SELECT o.nama_obat, COALESCE(SUM(dr.jumlah), 0) AS total_diresepkan
FROM Detail_Resep dr
RIGHT JOIN Obat o ON dr.obat_id = o.obat_id
GROUP BY o.obat_id, o.nama_obat;

-- 4. NON-CORRELATED SUBQUERY: Menampilkan obat yang harganya di atas rata-rata harga semua obat
SELECT nama_obat, jenis_obat, harga_satuan 
FROM Obat 
WHERE harga_satuan > (SELECT AVG(harga_satuan) FROM Obat);

-- 5. CORRELATED SUBQUERY: Mencari riwayat tanggal kunjungan terakhir untuk masing-masing pasien
SELECT p.nama AS nama_pasien, k1.waktu_datang AS kunjungan_terakhir
FROM Pasien p
JOIN Kunjungan k1 ON p.patient_id = k1.patient_id
WHERE k1.waktu_datang = (
    SELECT MAX(k2.waktu_datang) 
    FROM Kunjungan k2 
    WHERE k2.patient_id = k1.patient_id
);

-- 6. GROUP BY & HAVING: Menampilkan nama obat yang stoknya lebih dari 50
SELECT nama_obat, stok
FROM Obat
GROUP BY obat_id, nama_obat
HAVING MAX(stok) > 50;

-- 7. UNION: Menggabungkan nomor telepon Dokter dan Pasien sebagai daftar kontak darurat
SELECT nama, no_telpon, 'Dokter' AS tipe_pengguna FROM Dokter
UNION
SELECT nama, no_telpon, 'Pasien' AS tipe_pengguna FROM Pasien;

-- 8. INTERSECT: Menampilkan obat yang harganya > 5000 IRISAN DENGAN obat yang stoknya < 100
SELECT nama_obat FROM Obat WHERE harga_satuan > 5000.00
INTERSECT
SELECT nama_obat FROM Obat WHERE stok < 100;

-- 9. EXCEPT: Menampilkan ID Pasien KECUALI pasien yang sudah pernah datang berobat
SELECT patient_id FROM Pasien
EXCEPT
SELECT patient_id FROM Kunjungan;

-- 10. KOMBINASI (JOIN, AGGREGATE, SUBQUERY): Menampilkan pasien dengan total tagihan di atas rata-rata
SELECT t.tagihan_id, p.nama AS nama_pasien, t.total_tagihan
FROM Tagihan t
JOIN Kunjungan k ON t.kunjungan_id = k.kunjungan_id
JOIN Pasien p ON k.patient_id = p.patient_id
WHERE t.total_tagihan > (
    SELECT AVG(total_tagihan) FROM Tagihan
);