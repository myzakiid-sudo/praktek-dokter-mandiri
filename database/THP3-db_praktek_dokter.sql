-- ==============================================================================
-- TUGAS AKHIR MATA KULIAH PEMROGRAMAN SQL - TAHAP 3
-- PROYEK APLIKASI PRAKTEK DOKTER MANDIRI
-- STORED PROGRAMS (PROCEDURES & FUNCTIONS) - VERSI FINAL
-- ==============================================================================

USE db_praktek_dokter;

-- ==============================================================================
-- A. STORED FUNCTIONS (MINIMAL 5 FUNCTIONS)
-- ==============================================================================

-- 1. Function untuk menghitung usia pasien secara otomatis berdasarkan tgl_lahir 
DROP FUNCTION IF EXISTS sf_hitung_usia;
DELIMITER $$
CREATE FUNCTION sf_hitung_usia(p_patient_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_usia INT;
    SELECT TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) INTO v_usia
    FROM Pasien
    WHERE patient_id = p_patient_id;
    RETURN v_usia;
END$$
DELIMITER ;

-- 2. Function untuk mengecek ketersediaan stok obat sebelum diresepkan 
DROP FUNCTION IF EXISTS sf_cek_stok_obat;
DELIMITER $$
CREATE FUNCTION sf_cek_stok_obat(p_obat_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_stok INT;
    SELECT stok INTO v_stok
    FROM Obat
    WHERE obat_id = p_obat_id;
    RETURN v_stok;
END$$
DELIMITER ;

-- 3. Function untuk menghitung subtotal detail tagihan (Harga * Jumlah)
DROP FUNCTION IF EXISTS sf_hitung_subtotal;
DELIMITER $$
CREATE FUNCTION sf_hitung_subtotal(p_harga DECIMAL(10,2), p_jumlah INT)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    RETURN p_harga * p_jumlah;
END$$
DELIMITER ;

-- 4. Function untuk menghitung total tagihan berdasarkan detail tagihan 
DROP FUNCTION IF EXISTS sf_hitung_total_tagihan;
DELIMITER $$
CREATE FUNCTION sf_hitung_total_tagihan(p_tagihan_id INT)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE v_total DECIMAL(10,2);
    
    -- Menggunakan COALESCE agar jika kosong nilainya 0, bukan NULL
    SELECT COALESCE(SUM(subtotal), 0) INTO v_total
    FROM Detail_Tagihan
    WHERE tagihan_id = p_tagihan_id;
    
    RETURN v_total;
END$$
DELIMITER ;

-- 5. Function untuk validasi data bisnis (Cek Jam Operasional Klinik) 
DROP FUNCTION IF EXISTS sf_validasi_jam_operasional;
DELIMITER $$
CREATE FUNCTION sf_validasi_jam_operasional(p_waktu DATETIME)
RETURNS VARCHAR(50)
DETERMINISTIC
BEGIN
    DECLARE v_jam INT;
    DECLARE v_status VARCHAR(50);
    
    SET v_jam = HOUR(p_waktu);
    
    -- Klinik buka dari jam 08:00 sampai 20:00
    IF v_jam >= 8 AND v_jam < 20 THEN
        SET v_status = 'Buka';
    ELSE
        SET v_status = 'Tutup';
    END IF;
    
    RETURN v_status;
END$$
DELIMITER ;


-- ==============================================================================
-- B. STORED PROCEDURES (MINIMAL 8 PROCEDURES)
-- ==============================================================================

-- 1. Procedure Pendaftaran Kunjungan Pasien (Memakai IN, OUT) 
DROP PROCEDURE IF EXISTS sp_daftar_kunjungan;
DELIMITER $$
CREATE PROCEDURE sp_daftar_kunjungan(
    IN p_patient_id INT,
    IN p_dokter_id INT,
    IN p_jenis_layanan VARCHAR(50),
    OUT p_status_pesan VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status_pesan = 'Error: Gagal mendaftarkan kunjungan. Cek kembali data ID.';
    END;

    START TRANSACTION;
    
    INSERT INTO Kunjungan (patient_id, dokter_id, waktu_datang, status, jenis_layanan)
    VALUES (p_patient_id, p_dokter_id, NOW(), 'Menunggu', p_jenis_layanan);
    
    COMMIT;
    SET p_status_pesan = 'Sukses: Pasien berhasil didaftarkan ke antrian.';
END$$
DELIMITER ;

-- 2. Procedure Pembuatan Resep Awal (Header) 
DROP PROCEDURE IF EXISTS sp_buat_resep;
DELIMITER $$
CREATE PROCEDURE sp_buat_resep(
    IN p_record_id INT,
    IN p_dokter_id INT,
    IN p_catatan TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    INSERT INTO Resep (record_id, dokter_id, tanggal_resep, catatan_dokter, status_resep)
    VALUES (p_record_id, p_dokter_id, NOW(), p_catatan, 'Dibuat');
    COMMIT;
END$$
DELIMITER ;

-- 3. Procedure Manajemen Stok Obat Terpusat (Masuk/Keluar) 
DROP PROCEDURE IF EXISTS sp_update_stok_obat;
DELIMITER $$
CREATE PROCEDURE sp_update_stok_obat(
    IN p_obat_id INT,
    IN p_lokasi_id INT,
    IN p_jumlah INT,
    IN p_jenis ENUM('Masuk', 'Keluar', 'Penyesuaian'),
    IN p_keterangan TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    
    INSERT INTO Transaksi_Stok (obat_id, lokasi_id, tanggal, jenis_transaksi, jumlah, keterangan)
    VALUES (p_obat_id, p_lokasi_id, NOW(), p_jenis, p_jumlah, p_keterangan);
    
    IF p_jenis = 'Masuk' THEN
        UPDATE Obat SET stok = stok + p_jumlah WHERE obat_id = p_obat_id;
    ELSEIF p_jenis = 'Keluar' THEN
        UPDATE Obat SET stok = stok - p_jumlah WHERE obat_id = p_obat_id;
    END IF;
    
    COMMIT;
END$$
DELIMITER ;

-- 4. Procedure untuk generate Laporan Rekam Medis Pasien Spesifik 
DROP PROCEDURE IF EXISTS sp_generate_laporan_pasien;
DELIMITER $$
CREATE PROCEDURE sp_generate_laporan_pasien(
    IN p_patient_id INT
)
BEGIN
    SELECT k.waktu_datang, d.nama AS dokter_pemeriksa, 
           rm.anamnesa, rm.catatan_klinis
    FROM Kunjungan k
    JOIN Dokter d ON k.dokter_id = d.dokter_id
    JOIN Rekam_Medis rm ON k.kunjungan_id = rm.kunjungan_id
    WHERE k.patient_id = p_patient_id
    ORDER BY k.waktu_datang DESC;
END$$
DELIMITER ;

-- 5. Procedure untuk Menambahkan Detail Resep Obat (Dengan kolom instruksi_khusus)
DROP PROCEDURE IF EXISTS sp_tambah_detail_resep;
DELIMITER $$
CREATE PROCEDURE sp_tambah_detail_resep(
    IN p_resep_id INT,
    IN p_obat_id INT,
    IN p_jumlah INT,
    IN p_dosis VARCHAR(50),
    IN p_instruksi_khusus TEXT
)
BEGIN
    -- Menggunakan RESIGNAL agar PHP bisa menangkap error dari Trigger (contoh: stok habis)
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    INSERT INTO Detail_Resep (resep_id, obat_id, jumlah, dosis, instruksi_khusus)
    VALUES (p_resep_id, p_obat_id, p_jumlah, p_dosis, p_instruksi_khusus);
    COMMIT;
END$$
DELIMITER ;

-- 6. Procedure untuk Mencatat Tindakan Medis
DROP PROCEDURE IF EXISTS sp_catat_tindakan;
DELIMITER $$
CREATE PROCEDURE sp_catat_tindakan(
    IN p_record_id INT,
    IN p_jenis_tindakan VARCHAR(100),
    IN p_keterangan TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    INSERT INTO Tindakan (record_id, jenis_tindakan, tanggal_tindakan, keterangan)
    VALUES (p_record_id, p_jenis_tindakan, NOW(), p_keterangan);
    COMMIT;
END$$
DELIMITER ;

-- 7. Procedure Update Total Tagihan (Memanfaatkan Stored Function dan Parameter OUT) 
DROP PROCEDURE IF EXISTS sp_update_total_tagihan;
DELIMITER $$
CREATE PROCEDURE sp_update_total_tagihan(
    IN p_tagihan_id INT,
    OUT p_total_baru DECIMAL(10,2)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SET p_total_baru = 0;
    END;

    START TRANSACTION;
    SET p_total_baru = sf_hitung_total_tagihan(p_tagihan_id);
    
    UPDATE Tagihan 
    SET total_tagihan = p_total_baru 
    WHERE tagihan_id = p_tagihan_id;
    COMMIT;
END$$
DELIMITER ;

-- 8. Procedure Proses Pembayaran Tagihan (Menggunakan Parameter INOUT) 
DROP PROCEDURE IF EXISTS sp_bayar_tagihan;
DELIMITER $$
CREATE PROCEDURE sp_bayar_tagihan(
    INOUT p_tagihan_id INT,
    IN p_metode VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
    UPDATE Tagihan 
    SET status = 'Lunas', metode_pembayaran = p_metode 
    WHERE tagihan_id = p_tagihan_id;
    COMMIT;
END$$
DELIMITER ;