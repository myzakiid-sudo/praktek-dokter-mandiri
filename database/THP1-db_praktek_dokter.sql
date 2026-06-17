-- ==============================================================================
-- TUGAS AKHIR MATA KULIAH PEMROGRAMAN SQL - TAHAP 1
-- PROYEK APLIKASI PRAKTEK DOKTER MANDIRI
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- A. PERSIAPAN DATABASE
-- ------------------------------------------------------------------------------
DROP DATABASE IF EXISTS db_praktek_dokter;
CREATE DATABASE db_praktek_dokter;
USE db_praktek_dokter;

-- ------------------------------------------------------------------------------
-- B. PEMBUATAN TABEL MASTER
-- ------------------------------------------------------------------------------

-- 1. Tabel Role (Peran Pengguna)
CREATE TABLE Role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(50) NOT NULL UNIQUE,
    deskripsi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel Lokasi (Gudang/Apotek)
CREATE TABLE Lokasi (
    lokasi_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lokasi VARCHAR(100) NOT NULL,
    deskripsi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Supplier (Pemasok Obat)
CREATE TABLE Supplier (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    kontak_supplier VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Pasien (Dengan Index untuk pencarian nama)
CREATE TABLE Pasien (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    tgl_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT,
    no_telpon VARCHAR(20),
    email VARCHAR(100),
    kontak_darurat VARCHAR(100),
    asuransi_id VARCHAR(50),
    INDEX idx_pasien_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel Dokter (Dengan Index untuk pencarian nama)
CREATE TABLE Dokter (
    dokter_id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    spesialisasi VARCHAR(100) NOT NULL,
    no_izin_praktek VARCHAR(50) NOT NULL UNIQUE,
    no_telpon VARCHAR(20),
    email VARCHAR(100),
    INDEX idx_dokter_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel Obat (Dengan Check constraint stok & harga, serta Index)
CREATE TABLE Obat (
    obat_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_obat VARCHAR(100) NOT NULL,
    jenis_obat VARCHAR(50),
    stok INT NOT NULL DEFAULT 0,
    harga_satuan DECIMAL(10,2) NOT NULL,
    tgl_kadaluarsa DATE NOT NULL,
    CONSTRAINT chk_stok_positif CHECK (stok >= 0),
    CONSTRAINT chk_harga_positif CHECK (harga_satuan >= 0),
    INDEX idx_obat_nama (nama_obat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- C. PEMBUATAN TABEL TRANSAKSI (Dengan Foreign Key)
-- ------------------------------------------------------------------------------

-- 7. Tabel User (Autentikasi Aplikasi)
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES Role(role_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabel Kunjungan
CREATE TABLE Kunjungan (
    kunjungan_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    dokter_id INT NOT NULL,
    waktu_datang DATETIME NOT NULL,
    waktu_selesai DATETIME,
    status VARCHAR(50),
    jenis_layanan VARCHAR(50),
    antrian_no INT,
    FOREIGN KEY (patient_id) REFERENCES Pasien(patient_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (dokter_id) REFERENCES Dokter(dokter_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_kunjungan_waktu (waktu_datang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabel Rekam Medis
CREATE TABLE Rekam_Medis (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    kunjungan_id INT NOT NULL,
    tanggal_catatan DATETIME NOT NULL,
    anamnesa TEXT,
    pemeriksaan_fisik TEXT,
    catatan_klinis TEXT,
    riwayat_penyakit TEXT,
    alergi_obat_makanan TEXT,
    FOREIGN KEY (kunjungan_id) REFERENCES Kunjungan(kunjungan_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabel Tindakan
CREATE TABLE Tindakan (
    tindakan_id INT AUTO_INCREMENT PRIMARY KEY,
    record_id INT NOT NULL,
    jenis_tindakan VARCHAR(100) NOT NULL,
    tanggal_tindakan DATETIME NOT NULL,
    keterangan TEXT,
    FOREIGN KEY (record_id) REFERENCES Rekam_Medis(record_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Tabel Resep
CREATE TABLE Resep (
    resep_id INT AUTO_INCREMENT PRIMARY KEY,
    record_id INT NOT NULL,
    dokter_id INT NOT NULL,
    tanggal_resep DATETIME NOT NULL,
    catatan_dokter TEXT,
    status_resep VARCHAR(50),
    FOREIGN KEY (record_id) REFERENCES Rekam_Medis(record_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (dokter_id) REFERENCES Dokter(dokter_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Tabel Detail Resep (Dengan Check Constraint)
CREATE TABLE Detail_Resep (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    resep_id INT NOT NULL,
    obat_id INT NOT NULL,
    dosis VARCHAR(50),
    rute VARCHAR(50),
    frekuensi VARCHAR(50),
    durasi VARCHAR(50),
    jumlah INT NOT NULL,
    instruksi_khusus TEXT,
    CONSTRAINT chk_jumlah_resep CHECK (jumlah > 0),
    FOREIGN KEY (resep_id) REFERENCES Resep(resep_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (obat_id) REFERENCES Obat(obat_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Tabel Tagihan
CREATE TABLE Tagihan (
    tagihan_id INT AUTO_INCREMENT PRIMARY KEY,
    kunjungan_id INT NOT NULL,
    tanggal_tagihan DATETIME NOT NULL,
    total_tagihan DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    metode_pembayaran VARCHAR(50),
    asuransi_id VARCHAR(50),
    status VARCHAR(50) NOT NULL,
    FOREIGN KEY (kunjungan_id) REFERENCES Kunjungan(kunjungan_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Tabel Detail Tagihan (Dengan Check Constraint)
CREATE TABLE Detail_Tagihan (
    detail_tagihan_id INT AUTO_INCREMENT PRIMARY KEY,
    tagihan_id INT NOT NULL,
    jenis_item VARCHAR(100) NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT chk_jumlah_tagihan CHECK (jumlah > 0),
    FOREIGN KEY (tagihan_id) REFERENCES Tagihan(tagihan_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Tabel Transaksi Stok
CREATE TABLE Transaksi_Stok (
    transaksi_stok_id INT AUTO_INCREMENT PRIMARY KEY,
    obat_id INT NOT NULL,
    lokasi_id INT NOT NULL,
    tanggal DATETIME NOT NULL,
    jenis_transaksi ENUM('Masuk', 'Keluar', 'Penyesuaian') NOT NULL,
    jumlah INT NOT NULL,
    keterangan TEXT,
    FOREIGN KEY (obat_id) REFERENCES Obat(obat_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (lokasi_id) REFERENCES Lokasi(lokasi_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- D. POPULASI DATA AWAL (Minimal 10 Data per Tabel Master)
-- ------------------------------------------------------------------------------

-- Insert Data Tabel Role
INSERT INTO Role (nama_role, deskripsi) VALUES 
('Admin', 'Administrator Sistem'), ('Dokter Umum', 'Dokter Praktik Umum'),
('Dokter Spesialis', 'Dokter Spesialis'), ('Apoteker', 'Pengelola Apotek dan Resep'),
('Kasir', 'Petugas Pembayaran'), ('Resepsionis', 'Pendaftaran Pasien'),
('Perawat', 'Asisten Dokter'), ('Kepala Klinik', 'Manajemen Klinik'),
('IT Support', 'Dukungan Teknis'), ('HRD', 'Pengelola SDM');

-- Insert Data Tabel Lokasi
INSERT INTO Lokasi (nama_lokasi, deskripsi) VALUES 
('Gudang Utama', 'Penyimpanan stok awal'), ('Apotek Depan', 'Distribusi resep pasien'),
('Poli Umum 1', 'Ruang periksa umum'), ('Poli Umum 2', 'Ruang periksa umum'),
('Poli Gigi', 'Ruang periksa spesialis gigi'), ('Poli Anak', 'Ruang periksa spesialis anak'),
('Ruang Tindakan', 'Tindakan medis ringan/IGD'), ('Laboratorium', 'Pengambilan sampel darah'),
('Kasir Utama', 'Loket pembayaran'), ('Ruang Tunggu', 'Area tunggu pasien');

-- Insert Data Tabel Supplier
INSERT INTO Supplier (nama_supplier, alamat, kontak_supplier) VALUES 
('PT Kimia Farma', 'Jl. Veteran Malang', '081122334455'), ('PT Kalbe Farma', 'Jl. Sukarno Hatta', '081234567890'),
('PT Sanbe Farma', 'Kawasan Industri Singosari', '081345678901'), ('PT Dexa Medica', 'Jl. LA Sucipto', '081456789012'),
('PT Pharos', 'Jl. Raden Intan', '081567890123'), ('CV Medika Utama', 'Jl. MT Haryono', '081678901234'),
('Bina San Prima', 'Jl. Galunggung', '081789012345'), ('PT Enseval', 'Jl. Panglima Sudirman', '081890123456'),
('Mensa Binasukses', 'Jl. Letjen Sutoyo', '081901234567'), ('PT Anugerah Argon', 'Jl. Basuki Rahmat', '082012345678');

-- Insert Data Tabel Pasien
INSERT INTO Pasien (nama, tgl_lahir, jenis_kelamin, alamat, no_telpon) VALUES 
('Budi Santoso', '1990-05-15', 'L', 'Jl. Mawar No 1', '081200000001'), ('Siti Aminah', '1985-08-20', 'P', 'Jl. Melati No 2', '081200000002'),
('Andi Wijaya', '1992-12-10', 'L', 'Jl. Anggrek No 3', '081200000003'), ('Rini Astuti', '1998-03-25', 'P', 'Jl. Kamboja No 4', '081200000004'),
('Joko Susilo', '1975-11-05', 'L', 'Jl. Kenanga No 5', '081200000005'), ('Dewi Lestari', '2000-01-30', 'P', 'Jl. Flamboyan No 6', '081200000006'),
('Rahmat Hidayat', '1988-07-12', 'L', 'Jl. Cempaka No 7', '081200000007'), ('Nina Marlina', '1995-09-18', 'P', 'Jl. Dahlia No 8', '081200000008'),
('Eko Prasetyo', '1982-04-22', 'L', 'Jl. Teratai No 9', '081200000009'), ('Ayu Wandira', '1999-06-14', 'P', 'Jl. Bougenville No 10', '081200000010');

-- Insert Data Tabel Dokter
INSERT INTO Dokter (nama, spesialisasi, no_izin_praktek, no_telpon) VALUES 
('Dr. Ahmad', 'Umum', 'SIP-001', '081300000001'), ('Dr. Budi', 'Gigi', 'SIP-002', '081300000002'),
('Dr. Cici', 'Anak', 'SIP-003', '081300000003'), ('Dr. Dedi', 'Penyakit Dalam', 'SIP-004', '081300000004'),
('Dr. Eka', 'Umum', 'SIP-005', '081300000005'), ('Dr. Fani', 'Kandungan', 'SIP-006', '081300000006'),
('Dr. Gilang', 'Bedah', 'SIP-007', '081300000007'), ('Dr. Hana', 'Kulit', 'SIP-008', '081300000008'),
('Dr. Irwan', 'Saraf', 'SIP-009', '081300000009'), ('Dr. Jamil', 'THT', 'SIP-010', '081300000010');

-- Insert Data Tabel Obat
INSERT INTO Obat (nama_obat, jenis_obat, stok, harga_satuan, tgl_kadaluarsa) VALUES 
('Paracetamol 500mg', 'Tablet', 100, 5000.00, '2027-12-31'), ('Amoxicillin 500mg', 'Kapsul', 50, 8000.00, '2027-10-15'),
('Ibuprofen 400mg', 'Tablet', 75, 6000.00, '2028-01-20'), ('Cetirizine 10mg', 'Tablet', 60, 4000.00, '2027-08-05'),
('Omeprazole 20mg', 'Kapsul', 40, 10000.00, '2028-05-12'), ('Vitamin C 500mg', 'Tablet', 200, 2500.00, '2029-11-30'),
('Salep Hidrokortison', 'Salep', 30, 15000.00, '2027-04-18'), ('Sirup OBH', 'Sirup', 45, 20000.00, '2027-09-22'),
('Asam Mefenamat', 'Tablet', 80, 7000.00, '2028-02-28'), ('Loratadine 10mg', 'Tablet', 55, 5500.00, '2028-07-10');-- ==============================================================================
-- TUGAS AKHIR MATA KULIAH PEMROGRAMAN SQL - TAHAP 1
-- PROYEK APLIKASI PRAKTEK DOKTER MANDIRI
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- A. PERSIAPAN DATABASE
-- ------------------------------------------------------------------------------
DROP DATABASE IF EXISTS db_praktek_dokter;
CREATE DATABASE db_praktek_dokter;
USE db_praktek_dokter;

-- ------------------------------------------------------------------------------
-- B. PEMBUATAN TABEL MASTER
-- ------------------------------------------------------------------------------

-- 1. Tabel Role (Peran Pengguna)
CREATE TABLE Role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(50) NOT NULL UNIQUE,
    deskripsi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel Lokasi (Gudang/Apotek)
CREATE TABLE Lokasi (
    lokasi_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lokasi VARCHAR(100) NOT NULL,
    deskripsi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Supplier (Pemasok Obat)
CREATE TABLE Supplier (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    kontak_supplier VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Pasien (Dengan Index untuk pencarian nama)
CREATE TABLE Pasien (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    tgl_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT,
    no_telpon VARCHAR(20),
    email VARCHAR(100),
    kontak_darurat VARCHAR(100),
    asuransi_id VARCHAR(50),
    INDEX idx_pasien_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel Dokter (Dengan Index untuk pencarian nama)
CREATE TABLE Dokter (
    dokter_id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    spesialisasi VARCHAR(100) NOT NULL,
    no_izin_praktek VARCHAR(50) NOT NULL UNIQUE,
    no_telpon VARCHAR(20),
    email VARCHAR(100),
    INDEX idx_dokter_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel Obat (Dengan Check constraint stok & harga, serta Index)
CREATE TABLE Obat (
    obat_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_obat VARCHAR(100) NOT NULL,
    jenis_obat VARCHAR(50),
    stok INT NOT NULL DEFAULT 0,
    harga_satuan DECIMAL(10,2) NOT NULL,
    tgl_kadaluarsa DATE NOT NULL,
    CONSTRAINT chk_stok_positif CHECK (stok >= 0),
    CONSTRAINT chk_harga_positif CHECK (harga_satuan >= 0),
    INDEX idx_obat_nama (nama_obat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- C. PEMBUATAN TABEL TRANSAKSI (Dengan Foreign Key)
-- ------------------------------------------------------------------------------

-- 7. Tabel User (Autentikasi Aplikasi)
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES Role(role_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabel Kunjungan
CREATE TABLE Kunjungan (
    kunjungan_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    dokter_id INT NOT NULL,
    waktu_datang DATETIME NOT NULL,
    waktu_selesai DATETIME,
    status VARCHAR(50),
    jenis_layanan VARCHAR(50),
    antrian_no INT,
    FOREIGN KEY (patient_id) REFERENCES Pasien(patient_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (dokter_id) REFERENCES Dokter(dokter_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_kunjungan_waktu (waktu_datang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabel Rekam Medis
CREATE TABLE Rekam_Medis (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    kunjungan_id INT NOT NULL,
    tanggal_catatan DATETIME NOT NULL,
    anamnesa TEXT,
    pemeriksaan_fisik TEXT,
    catatan_klinis TEXT,
    riwayat_penyakit TEXT,
    alergi_obat_makanan TEXT,
    FOREIGN KEY (kunjungan_id) REFERENCES Kunjungan(kunjungan_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabel Tindakan
CREATE TABLE Tindakan (
    tindakan_id INT AUTO_INCREMENT PRIMARY KEY,
    record_id INT NOT NULL,
    jenis_tindakan VARCHAR(100) NOT NULL,
    tanggal_tindakan DATETIME NOT NULL,
    keterangan TEXT,
    FOREIGN KEY (record_id) REFERENCES Rekam_Medis(record_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Tabel Resep
CREATE TABLE Resep (
    resep_id INT AUTO_INCREMENT PRIMARY KEY,
    record_id INT NOT NULL,
    dokter_id INT NOT NULL,
    tanggal_resep DATETIME NOT NULL,
    catatan_dokter TEXT,
    status_resep VARCHAR(50),
    FOREIGN KEY (record_id) REFERENCES Rekam_Medis(record_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (dokter_id) REFERENCES Dokter(dokter_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Tabel Detail Resep (Dengan Check Constraint)
CREATE TABLE Detail_Resep (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    resep_id INT NOT NULL,
    obat_id INT NOT NULL,
    dosis VARCHAR(50),
    rute VARCHAR(50),
    frekuensi VARCHAR(50),
    durasi VARCHAR(50),
    jumlah INT NOT NULL,
    instruksi_khusus TEXT,
    CONSTRAINT chk_jumlah_resep CHECK (jumlah > 0),
    FOREIGN KEY (resep_id) REFERENCES Resep(resep_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (obat_id) REFERENCES Obat(obat_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Tabel Tagihan
CREATE TABLE Tagihan (
    tagihan_id INT AUTO_INCREMENT PRIMARY KEY,
    kunjungan_id INT NOT NULL,
    tanggal_tagihan DATETIME NOT NULL,
    total_tagihan DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    metode_pembayaran VARCHAR(50),
    asuransi_id VARCHAR(50),
    status VARCHAR(50) NOT NULL,
    FOREIGN KEY (kunjungan_id) REFERENCES Kunjungan(kunjungan_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Tabel Detail Tagihan (Dengan Check Constraint)
CREATE TABLE Detail_Tagihan (
    detail_tagihan_id INT AUTO_INCREMENT PRIMARY KEY,
    tagihan_id INT NOT NULL,
    jenis_item VARCHAR(100) NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT chk_jumlah_tagihan CHECK (jumlah > 0),
    FOREIGN KEY (tagihan_id) REFERENCES Tagihan(tagihan_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Tabel Transaksi Stok
CREATE TABLE Transaksi_Stok (
    transaksi_stok_id INT AUTO_INCREMENT PRIMARY KEY,
    obat_id INT NOT NULL,
    lokasi_id INT NOT NULL,
    tanggal DATETIME NOT NULL,
    jenis_transaksi ENUM('Masuk', 'Keluar', 'Penyesuaian') NOT NULL,
    jumlah INT NOT NULL,
    keterangan TEXT,
    FOREIGN KEY (obat_id) REFERENCES Obat(obat_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (lokasi_id) REFERENCES Lokasi(lokasi_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- D. POPULASI DATA AWAL (Minimal 10 Data per Tabel Master)
-- ------------------------------------------------------------------------------

-- Insert Data Tabel Role
INSERT INTO Role (nama_role, deskripsi) VALUES 
('Admin', 'Administrator Sistem'), ('Dokter Umum', 'Dokter Praktik Umum'),
('Dokter Spesialis', 'Dokter Spesialis'), ('Apoteker', 'Pengelola Apotek dan Resep'),
('Kasir', 'Petugas Pembayaran'), ('Resepsionis', 'Pendaftaran Pasien'),
('Perawat', 'Asisten Dokter'), ('Kepala Klinik', 'Manajemen Klinik'),
('IT Support', 'Dukungan Teknis'), ('HRD', 'Pengelola SDM');

-- Insert Data Tabel Lokasi
INSERT INTO Lokasi (nama_lokasi, deskripsi) VALUES 
('Gudang Utama', 'Penyimpanan stok awal'), ('Apotek Depan', 'Distribusi resep pasien'),
('Poli Umum 1', 'Ruang periksa umum'), ('Poli Umum 2', 'Ruang periksa umum'),
('Poli Gigi', 'Ruang periksa spesialis gigi'), ('Poli Anak', 'Ruang periksa spesialis anak'),
('Ruang Tindakan', 'Tindakan medis ringan/IGD'), ('Laboratorium', 'Pengambilan sampel darah'),
('Kasir Utama', 'Loket pembayaran'), ('Ruang Tunggu', 'Area tunggu pasien');

-- Insert Data Tabel Supplier
INSERT INTO Supplier (nama_supplier, alamat, kontak_supplier) VALUES 
('PT Kimia Farma', 'Jl. Veteran Malang', '081122334455'), ('PT Kalbe Farma', 'Jl. Sukarno Hatta', '081234567890'),
('PT Sanbe Farma', 'Kawasan Industri Singosari', '081345678901'), ('PT Dexa Medica', 'Jl. LA Sucipto', '081456789012'),
('PT Pharos', 'Jl. Raden Intan', '081567890123'), ('CV Medika Utama', 'Jl. MT Haryono', '081678901234'),
('Bina San Prima', 'Jl. Galunggung', '081789012345'), ('PT Enseval', 'Jl. Panglima Sudirman', '081890123456'),
('Mensa Binasukses', 'Jl. Letjen Sutoyo', '081901234567'), ('PT Anugerah Argon', 'Jl. Basuki Rahmat', '082012345678');

-- Insert Data Tabel Pasien
INSERT INTO Pasien (nama, tgl_lahir, jenis_kelamin, alamat, no_telpon) VALUES 
('Budi Santoso', '1990-05-15', 'L', 'Jl. Mawar No 1', '081200000001'), ('Siti Aminah', '1985-08-20', 'P', 'Jl. Melati No 2', '081200000002'),
('Andi Wijaya', '1992-12-10', 'L', 'Jl. Anggrek No 3', '081200000003'), ('Rini Astuti', '1998-03-25', 'P', 'Jl. Kamboja No 4', '081200000004'),
('Joko Susilo', '1975-11-05', 'L', 'Jl. Kenanga No 5', '081200000005'), ('Dewi Lestari', '2000-01-30', 'P', 'Jl. Flamboyan No 6', '081200000006'),
('Rahmat Hidayat', '1988-07-12', 'L', 'Jl. Cempaka No 7', '081200000007'), ('Nina Marlina', '1995-09-18', 'P', 'Jl. Dahlia No 8', '081200000008'),
('Eko Prasetyo', '1982-04-22', 'L', 'Jl. Teratai No 9', '081200000009'), ('Ayu Wandira', '1999-06-14', 'P', 'Jl. Bougenville No 10', '081200000010');

-- Insert Data Tabel Dokter
INSERT INTO Dokter (nama, spesialisasi, no_izin_praktek, no_telpon) VALUES 
('Dr. Ahmad', 'Umum', 'SIP-001', '081300000001'), ('Dr. Budi', 'Gigi', 'SIP-002', '081300000002'),
('Dr. Cici', 'Anak', 'SIP-003', '081300000003'), ('Dr. Dedi', 'Penyakit Dalam', 'SIP-004', '081300000004'),
('Dr. Eka', 'Umum', 'SIP-005', '081300000005'), ('Dr. Fani', 'Kandungan', 'SIP-006', '081300000006'),
('Dr. Gilang', 'Bedah', 'SIP-007', '081300000007'), ('Dr. Hana', 'Kulit', 'SIP-008', '081300000008'),
('Dr. Irwan', 'Saraf', 'SIP-009', '081300000009'), ('Dr. Jamil', 'THT', 'SIP-010', '081300000010');

-- Insert Data Tabel Obat
INSERT INTO Obat (nama_obat, jenis_obat, stok, harga_satuan, tgl_kadaluarsa) VALUES 
('Paracetamol 500mg', 'Tablet', 100, 5000.00, '2027-12-31'), ('Amoxicillin 500mg', 'Kapsul', 50, 8000.00, '2027-10-15'),
('Ibuprofen 400mg', 'Tablet', 75, 6000.00, '2028-01-20'), ('Cetirizine 10mg', 'Tablet', 60, 4000.00, '2027-08-05'),
('Omeprazole 20mg', 'Kapsul', 40, 10000.00, '2028-05-12'), ('Vitamin C 500mg', 'Tablet', 200, 2500.00, '2029-11-30'),
('Salep Hidrokortison', 'Salep', 30, 15000.00, '2027-04-18'), ('Sirup OBH', 'Sirup', 45, 20000.00, '2027-09-22'),
('Asam Mefenamat', 'Tablet', 80, 7000.00, '2028-02-28'), ('Loratadine 10mg', 'Tablet', 55, 5500.00, '2028-07-10');