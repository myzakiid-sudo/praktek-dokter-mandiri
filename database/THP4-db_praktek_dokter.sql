-- ==============================================================================
-- TUGAS AKHIR MATA KULIAH PEMROGRAMAN SQL - TAHAP 4
-- PROYEK APLIKASI PRAKTEK DOKTER MANDIRI
-- AUTOMATION WITH TRIGGERS (MINIMAL 6 TRIGGERS)
-- ==============================================================================

USE db_praktek_dokter;

-- ==========================================
-- A. PERSIAPAN (MEMBERSIHKAN TRIGGER LAMA)
-- ==========================================
DROP TRIGGER IF EXISTS trg_detail_tagihan_insert;
DROP TRIGGER IF EXISTS trg_detail_tagihan_update;
DROP TRIGGER IF EXISTS trg_detail_tagihan_delete;
DROP TRIGGER IF EXISTS trg_validasi_stok_sebelum_resep;
DROP TRIGGER IF EXISTS trg_potong_stok_setelah_resep;
DROP TRIGGER IF EXISTS trg_kunci_tagihan_lunas;

-- ==========================================
-- B. IMPLEMENTASI TRIGGERS
-- ==========================================

-- 1. Trigger Auto-Update Total Tagihan (Setelah INSERT Detail)
DELIMITER $$
CREATE TRIGGER trg_detail_tagihan_insert
AFTER INSERT ON Detail_Tagihan
FOR EACH ROW
BEGIN
    UPDATE Tagihan 
    SET total_tagihan = total_tagihan + NEW.subtotal
    WHERE tagihan_id = NEW.tagihan_id;
END$$
DELIMITER ;

-- 2. Trigger Auto-Update Total Tagihan (Setelah UPDATE Detail)
DELIMITER $$
CREATE TRIGGER trg_detail_tagihan_update
AFTER UPDATE ON Detail_Tagihan
FOR EACH ROW
BEGIN
    UPDATE Tagihan 
    SET total_tagihan = (total_tagihan - OLD.subtotal) + NEW.subtotal
    WHERE tagihan_id = NEW.tagihan_id;
END$$
DELIMITER ;

-- 3. Trigger Auto-Update Total Tagihan (Setelah DELETE Detail)
DELIMITER $$
CREATE TRIGGER trg_detail_tagihan_delete
AFTER DELETE ON Detail_Tagihan
FOR EACH ROW
BEGIN
    UPDATE Tagihan 
    SET total_tagihan = total_tagihan - OLD.subtotal
    WHERE tagihan_id = OLD.tagihan_id;
END$$
DELIMITER ;

-- 4. Trigger Validasi Data: Mencegah resep jika stok obat tidak cukup (Stok tidak boleh negatif)
DELIMITER $$
CREATE TRIGGER trg_validasi_stok_sebelum_resep
BEFORE INSERT ON Detail_Resep
FOR EACH ROW
BEGIN
    DECLARE v_stok_sekarang INT;
    
    SELECT stok INTO v_stok_sekarang FROM Obat WHERE obat_id = NEW.obat_id;
    
    IF v_stok_sekarang < NEW.jumlah THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Stok obat tidak mencukupi untuk memenuhi resep ini!';
    END IF;
END$$
DELIMITER ;

-- 5. Trigger Auto-Update: Potong stok obat secara otomatis setelah detail resep dimasukkan
DELIMITER $$
CREATE TRIGGER trg_potong_stok_setelah_resep
AFTER INSERT ON Detail_Resep
FOR EACH ROW
BEGIN
    UPDATE Obat 
    SET stok = stok - NEW.jumlah
    WHERE obat_id = NEW.obat_id;
END$$
DELIMITER ;

-- 6. Trigger Business Rule: Kunci data tagihan jika statusnya sudah 'Lunas'
DELIMITER $$
CREATE TRIGGER trg_kunci_tagihan_lunas
BEFORE UPDATE ON Tagihan
FOR EACH ROW
BEGIN
    IF OLD.status = 'Lunas' AND NEW.status = 'Lunas' AND OLD.total_tagihan <> NEW.total_tagihan THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Tagihan sudah lunas! Data Keuangan tidak boleh diubah.';
    END IF;
END$$
DELIMITER ;