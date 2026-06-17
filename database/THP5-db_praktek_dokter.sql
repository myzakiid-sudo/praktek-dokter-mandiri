/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.7.2-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_praktek_dokter
-- ------------------------------------------------------
-- Server version	12.1.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `detail_resep`
--

DROP TABLE IF EXISTS `detail_resep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_resep` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `resep_id` int(11) NOT NULL,
  `obat_id` int(11) NOT NULL,
  `dosis` varchar(50) DEFAULT NULL,
  `rute` varchar(50) DEFAULT NULL,
  `frekuensi` varchar(50) DEFAULT NULL,
  `durasi` varchar(50) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `instruksi_khusus` text DEFAULT NULL,
  PRIMARY KEY (`detail_id`),
  KEY `resep_id` (`resep_id`),
  KEY `obat_id` (`obat_id`),
  CONSTRAINT `1` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`resep_id`) ON UPDATE CASCADE,
  CONSTRAINT `2` FOREIGN KEY (`obat_id`) REFERENCES `obat` (`obat_id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_jumlah_resep` CHECK (`jumlah` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_resep`
--

LOCK TABLES `detail_resep` WRITE;
/*!40000 ALTER TABLE `detail_resep` DISABLE KEYS */;
INSERT INTO `detail_resep` VALUES
(1,1,2,'500mg',NULL,NULL,NULL,2,'3x sehari');
/*!40000 ALTER TABLE `detail_resep` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_validasi_stok_sebelum_resep
BEFORE INSERT ON Detail_Resep
FOR EACH ROW
BEGIN
    DECLARE v_stok_sekarang INT;
    
    SELECT stok INTO v_stok_sekarang FROM Obat WHERE obat_id = NEW.obat_id;
    
    IF v_stok_sekarang < NEW.jumlah THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Stok obat tidak mencukupi untuk memenuhi resep ini!';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_potong_stok_setelah_resep
AFTER INSERT ON Detail_Resep
FOR EACH ROW
BEGIN
    UPDATE Obat 
    SET stok = stok - NEW.jumlah
    WHERE obat_id = NEW.obat_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `detail_tagihan`
--

DROP TABLE IF EXISTS `detail_tagihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_tagihan` (
  `detail_tagihan_id` int(11) NOT NULL AUTO_INCREMENT,
  `tagihan_id` int(11) NOT NULL,
  `jenis_item` varchar(100) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`detail_tagihan_id`),
  KEY `tagihan_id` (`tagihan_id`),
  CONSTRAINT `1` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`tagihan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_jumlah_tagihan` CHECK (`jumlah` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_tagihan`
--

LOCK TABLES `detail_tagihan` WRITE;
/*!40000 ALTER TABLE `detail_tagihan` DISABLE KEYS */;
INSERT INTO `detail_tagihan` VALUES
(1,1,'Jasa dokter',50000.00,1,50000.00);
/*!40000 ALTER TABLE `detail_tagihan` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_detail_tagihan_insert
AFTER INSERT ON Detail_Tagihan
FOR EACH ROW
BEGIN
    UPDATE Tagihan 
    SET total_tagihan = total_tagihan + NEW.subtotal
    WHERE tagihan_id = NEW.tagihan_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_detail_tagihan_update
AFTER UPDATE ON Detail_Tagihan
FOR EACH ROW
BEGIN
    UPDATE Tagihan 
    SET total_tagihan = (total_tagihan - OLD.subtotal) + NEW.subtotal
    WHERE tagihan_id = NEW.tagihan_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_detail_tagihan_delete
AFTER DELETE ON Detail_Tagihan
FOR EACH ROW
BEGIN
    UPDATE Tagihan 
    SET total_tagihan = total_tagihan - OLD.subtotal
    WHERE tagihan_id = OLD.tagihan_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `dokter`
--

DROP TABLE IF EXISTS `dokter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dokter` (
  `dokter_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `spesialisasi` varchar(100) NOT NULL,
  `no_izin_praktek` varchar(50) NOT NULL,
  `no_telpon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`dokter_id`),
  UNIQUE KEY `no_izin_praktek` (`no_izin_praktek`),
  KEY `idx_dokter_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dokter`
--

LOCK TABLES `dokter` WRITE;
/*!40000 ALTER TABLE `dokter` DISABLE KEYS */;
INSERT INTO `dokter` VALUES
(1,'Dr. Ahmad','Umum','SIP-001','081300000001',NULL),
(2,'Dr. Budi','Gigi','SIP-002','081300000002',NULL),
(3,'Dr. Cici','Anak','SIP-003','081300000003',NULL),
(4,'Dr. Dedi','Penyakit Dalam','SIP-004','081300000004',NULL),
(5,'Dr. Eka','Umum','SIP-005','081300000005',NULL),
(6,'Dr. Fani','Kandungan','SIP-006','081300000006',NULL),
(7,'Dr. Gilang','Bedah','SIP-007','081300000007',NULL),
(8,'Dr. Hana','Kulit','SIP-008','081300000008',NULL),
(9,'Dr. Irwan','Saraf','SIP-009','081300000009',NULL),
(10,'Dr. Jamil','THT','SIP-010','081300000010',NULL);
/*!40000 ALTER TABLE `dokter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kunjungan`
--

DROP TABLE IF EXISTS `kunjungan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kunjungan` (
  `kunjungan_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `dokter_id` int(11) NOT NULL,
  `waktu_datang` datetime NOT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `jenis_layanan` varchar(50) DEFAULT NULL,
  `antrian_no` int(11) DEFAULT NULL,
  PRIMARY KEY (`kunjungan_id`),
  KEY `patient_id` (`patient_id`),
  KEY `dokter_id` (`dokter_id`),
  KEY `idx_kunjungan_waktu` (`waktu_datang`),
  CONSTRAINT `1` FOREIGN KEY (`patient_id`) REFERENCES `pasien` (`patient_id`) ON UPDATE CASCADE,
  CONSTRAINT `2` FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`dokter_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kunjungan`
--

LOCK TABLES `kunjungan` WRITE;
/*!40000 ALTER TABLE `kunjungan` DISABLE KEYS */;
INSERT INTO `kunjungan` VALUES
(1,1,1,'2026-06-17 19:30:59','2026-06-17 19:30:59','Selesai','Konsultasi Umum',NULL),
(2,13,2,'2026-06-17 23:09:45','2026-06-17 23:15:36','Selesai','Konsultasi Umum',NULL);
/*!40000 ALTER TABLE `kunjungan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lokasi`
--

DROP TABLE IF EXISTS `lokasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lokasi` (
  `lokasi_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lokasi` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`lokasi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lokasi`
--

LOCK TABLES `lokasi` WRITE;
/*!40000 ALTER TABLE `lokasi` DISABLE KEYS */;
INSERT INTO `lokasi` VALUES
(1,'Gudang Utama','Penyimpanan stok awal'),
(2,'Apotek Depan','Distribusi resep pasien'),
(3,'Poli Umum 1','Ruang periksa umum'),
(4,'Poli Umum 2','Ruang periksa umum'),
(5,'Poli Gigi','Ruang periksa spesialis gigi'),
(6,'Poli Anak','Ruang periksa spesialis anak'),
(7,'Ruang Tindakan','Tindakan medis ringan/IGD'),
(8,'Laboratorium','Pengambilan sampel darah'),
(9,'Kasir Utama','Loket pembayaran'),
(10,'Ruang Tunggu','Area tunggu pasien');
/*!40000 ALTER TABLE `lokasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obat`
--

DROP TABLE IF EXISTS `obat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `obat` (
  `obat_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_obat` varchar(100) NOT NULL,
  `jenis_obat` varchar(50) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `harga_satuan` decimal(10,2) NOT NULL,
  `tgl_kadaluarsa` date NOT NULL,
  PRIMARY KEY (`obat_id`),
  KEY `idx_obat_nama` (`nama_obat`),
  CONSTRAINT `chk_stok_positif` CHECK (`stok` >= 0),
  CONSTRAINT `chk_harga_positif` CHECK (`harga_satuan` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obat`
--

LOCK TABLES `obat` WRITE;
/*!40000 ALTER TABLE `obat` DISABLE KEYS */;
INSERT INTO `obat` VALUES
(1,'Paracetamol 500mg','Tablet',100,5000.00,'2027-12-31'),
(2,'Amoxicillin 500mg','Kapsul',48,8000.00,'2027-10-15'),
(3,'Ibuprofen 400mg','Tablet',75,6000.00,'2028-01-20'),
(4,'Cetirizine 10mg','Tablet',60,4000.00,'2027-08-05'),
(5,'Omeprazole 20mg','Kapsul',40,10000.00,'2028-05-12'),
(6,'Vitamin C 500mg','Tablet',200,2500.00,'2029-11-30'),
(7,'Salep Hidrokortison','Salep',32,15000.00,'2027-04-18'),
(8,'Sirup OBH','Sirup',45,20000.00,'2027-09-22'),
(9,'Asam Mefenamat','Tablet',80,7000.00,'2028-02-28'),
(10,'Loratadine 10mg','Tablet',55,5500.00,'2028-07-10');
/*!40000 ALTER TABLE `obat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pasien`
--

DROP TABLE IF EXISTS `pasien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pasien` (
  `patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telpon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `kontak_darurat` varchar(100) DEFAULT NULL,
  `asuransi_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`patient_id`),
  KEY `idx_pasien_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pasien`
--

LOCK TABLES `pasien` WRITE;
/*!40000 ALTER TABLE `pasien` DISABLE KEYS */;
INSERT INTO `pasien` VALUES
(1,'Budi Santoso','1990-05-15','L','Jl. Mawar No 1','081200000001',NULL,NULL,NULL),
(2,'Siti Aminah','1985-08-20','P','Jl. Melati No 2','081200000002',NULL,NULL,NULL),
(3,'Andi Wijaya','1992-12-10','L','Jl. Anggrek No 3','081200000003',NULL,NULL,NULL),
(4,'Rini Astuti','1998-03-25','P','Jl. Kamboja No 4','081200000004',NULL,NULL,NULL),
(5,'Joko Susilo','1975-11-05','L','Jl. Kenanga No 5','081200000005',NULL,NULL,NULL),
(6,'Dewi Lestari','2000-01-30','P','Jl. Flamboyan No 6','081200000006',NULL,NULL,NULL),
(7,'Rahmat Hidayat','1988-07-12','L','Jl. Cempaka No 7','081200000007',NULL,NULL,NULL),
(8,'Nina Marlina','1995-09-18','P','Jl. Dahlia No 8','081200000008',NULL,NULL,NULL),
(9,'Eko Prasetyo','1982-04-22','L','Jl. Teratai No 9','081200000009',NULL,NULL,NULL),
(10,'Ayu Wandira','1999-06-14','P','Jl. Bougenville No 10','081200000010',NULL,NULL,NULL),
(13,'Prabu','2006-05-17','L','JL. SINGOSARI','082144849322',NULL,NULL,NULL);
/*!40000 ALTER TABLE `pasien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rekam_medis`
--

DROP TABLE IF EXISTS `rekam_medis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rekam_medis` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL,
  `tanggal_catatan` datetime NOT NULL,
  `anamnesa` text DEFAULT NULL,
  `pemeriksaan_fisik` text DEFAULT NULL,
  `catatan_klinis` text DEFAULT NULL,
  `riwayat_penyakit` text DEFAULT NULL,
  `alergi_obat_makanan` text DEFAULT NULL,
  PRIMARY KEY (`record_id`),
  KEY `kunjungan_id` (`kunjungan_id`),
  CONSTRAINT `1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan` (`kunjungan_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rekam_medis`
--

LOCK TABLES `rekam_medis` WRITE;
/*!40000 ALTER TABLE `rekam_medis` DISABLE KEYS */;
INSERT INTO `rekam_medis` VALUES
(1,1,'2026-06-17 19:30:59','Pasien mengeluh demam dan pusing sejak 3 hari lalu','Suhu tubuh 38.5 derajat celcius, tensi 110/70','Suspect tipes ringan',NULL,NULL),
(2,2,'2026-06-17 23:15:36','konsultasi cabut gigi graham','harus dicabut','sakit gigi',NULL,NULL);
/*!40000 ALTER TABLE `rekam_medis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resep`
--

DROP TABLE IF EXISTS `resep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resep` (
  `resep_id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) NOT NULL,
  `dokter_id` int(11) NOT NULL,
  `tanggal_resep` datetime NOT NULL,
  `catatan_dokter` text DEFAULT NULL,
  `status_resep` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`resep_id`),
  KEY `record_id` (`record_id`),
  KEY `dokter_id` (`dokter_id`),
  CONSTRAINT `1` FOREIGN KEY (`record_id`) REFERENCES `rekam_medis` (`record_id`) ON UPDATE CASCADE,
  CONSTRAINT `2` FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`dokter_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep`
--

LOCK TABLES `resep` WRITE;
/*!40000 ALTER TABLE `resep` DISABLE KEYS */;
INSERT INTO `resep` VALUES
(1,2,2,'2026-06-17 23:16:36','','Dibuat');
/*!40000 ALTER TABLE `resep` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `nama_role` (`nama_role`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES
(1,'Admin','Administrator Sistem'),
(2,'Dokter Umum','Dokter Praktik Umum'),
(3,'Dokter Spesialis','Dokter Spesialis'),
(4,'Apoteker','Pengelola Apotek dan Resep'),
(5,'Kasir','Petugas Pembayaran'),
(6,'Resepsionis','Pendaftaran Pasien'),
(7,'Perawat','Asisten Dokter'),
(8,'Kepala Klinik','Manajemen Klinik'),
(9,'IT Support','Dukungan Teknis'),
(10,'HRD','Pengelola SDM');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kontak_supplier` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier`
--

LOCK TABLES `supplier` WRITE;
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;
INSERT INTO `supplier` VALUES
(1,'PT Kimia Farma','Jl. Veteran Malang','081122334455'),
(2,'PT Kalbe Farma','Jl. Sukarno Hatta','081234567890'),
(3,'PT Sanbe Farma','Kawasan Industri Singosari','081345678901'),
(4,'PT Dexa Medica','Jl. LA Sucipto','081456789012'),
(5,'PT Pharos','Jl. Raden Intan','081567890123'),
(6,'CV Medika Utama','Jl. MT Haryono','081678901234'),
(7,'Bina San Prima','Jl. Galunggung','081789012345'),
(8,'PT Enseval','Jl. Panglima Sudirman','081890123456'),
(9,'Mensa Binasukses','Jl. Letjen Sutoyo','081901234567'),
(10,'PT Anugerah Argon','Jl. Basuki Rahmat','082012345678');
/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tagihan`
--

DROP TABLE IF EXISTS `tagihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tagihan` (
  `tagihan_id` int(11) NOT NULL AUTO_INCREMENT,
  `kunjungan_id` int(11) NOT NULL,
  `tanggal_tagihan` datetime NOT NULL,
  `total_tagihan` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `asuransi_id` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`tagihan_id`),
  KEY `kunjungan_id` (`kunjungan_id`),
  CONSTRAINT `1` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan` (`kunjungan_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tagihan`
--

LOCK TABLES `tagihan` WRITE;
/*!40000 ALTER TABLE `tagihan` DISABLE KEYS */;
INSERT INTO `tagihan` VALUES
(1,2,'2026-06-17 23:41:37',50000.00,'Tunai',NULL,'Lunas');
/*!40000 ALTER TABLE `tagihan` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_kunci_tagihan_lunas
BEFORE UPDATE ON Tagihan
FOR EACH ROW
BEGIN
    IF OLD.status = 'Lunas' AND NEW.status = 'Lunas' AND OLD.total_tagihan <> NEW.total_tagihan THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Tagihan sudah lunas! Data Keuangan tidak boleh diubah.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tindakan`
--

DROP TABLE IF EXISTS `tindakan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tindakan` (
  `tindakan_id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) NOT NULL,
  `jenis_tindakan` varchar(100) NOT NULL,
  `tanggal_tindakan` datetime NOT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`tindakan_id`),
  KEY `record_id` (`record_id`),
  CONSTRAINT `1` FOREIGN KEY (`record_id`) REFERENCES `rekam_medis` (`record_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tindakan`
--

LOCK TABLES `tindakan` WRITE;
/*!40000 ALTER TABLE `tindakan` DISABLE KEYS */;
/*!40000 ALTER TABLE `tindakan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi_stok`
--

DROP TABLE IF EXISTS `transaksi_stok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksi_stok` (
  `transaksi_stok_id` int(11) NOT NULL AUTO_INCREMENT,
  `obat_id` int(11) NOT NULL,
  `lokasi_id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `jenis_transaksi` enum('Masuk','Keluar','Penyesuaian') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`transaksi_stok_id`),
  KEY `obat_id` (`obat_id`),
  KEY `lokasi_id` (`lokasi_id`),
  CONSTRAINT `1` FOREIGN KEY (`obat_id`) REFERENCES `obat` (`obat_id`) ON UPDATE CASCADE,
  CONSTRAINT `2` FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi` (`lokasi_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi_stok`
--

LOCK TABLES `transaksi_stok` WRITE;
/*!40000 ALTER TABLE `transaksi_stok` DISABLE KEYS */;
INSERT INTO `transaksi_stok` VALUES
(1,7,2,'2026-06-17 23:46:55','Masuk',2,'Penerimaan obat dari PT Kimia Farma dengan Nomor Faktur: KF-2026-0091');
/*!40000 ALTER TABLE `transaksi_stok` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
(1,'admin_yusuf','rahasia123',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_kunjungan_hari_ini`
--

DROP TABLE IF EXISTS `v_kunjungan_hari_ini`;
/*!50001 DROP VIEW IF EXISTS `v_kunjungan_hari_ini`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_kunjungan_hari_ini` AS SELECT
 1 AS `kunjungan_id`,
  1 AS `nama_pasien`,
  1 AS `nama_dokter`,
  1 AS `waktu_datang`,
  1 AS `status` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_laporan_tagihan`
--

DROP TABLE IF EXISTS `v_laporan_tagihan`;
/*!50001 DROP VIEW IF EXISTS `v_laporan_tagihan`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_laporan_tagihan` AS SELECT
 1 AS `tagihan_id`,
  1 AS `nama_pasien`,
  1 AS `tanggal_tagihan`,
  1 AS `total_tagihan`,
  1 AS `status`,
  1 AS `metode_pembayaran` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_obat_hampir_kadaluarsa`
--

DROP TABLE IF EXISTS `v_obat_hampir_kadaluarsa`;
/*!50001 DROP VIEW IF EXISTS `v_obat_hampir_kadaluarsa`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_obat_hampir_kadaluarsa` AS SELECT
 1 AS `nama_obat`,
  1 AS `stok`,
  1 AS `tgl_kadaluarsa` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_rekam_medis_pasien`
--

DROP TABLE IF EXISTS `v_rekam_medis_pasien`;
/*!50001 DROP VIEW IF EXISTS `v_rekam_medis_pasien`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_rekam_medis_pasien` AS SELECT
 1 AS `record_id`,
  1 AS `nama_pasien`,
  1 AS `nama_dokter`,
  1 AS `waktu_datang`,
  1 AS `anamnesa`,
  1 AS `catatan_klinis` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_stok_obat_kritis`
--

DROP TABLE IF EXISTS `v_stok_obat_kritis`;
/*!50001 DROP VIEW IF EXISTS `v_stok_obat_kritis`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_stok_obat_kritis` AS SELECT
 1 AS `obat_id`,
  1 AS `nama_obat`,
  1 AS `jenis_obat`,
  1 AS `stok`,
  1 AS `tgl_kadaluarsa` */;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'db_praktek_dokter'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `sf_cek_stok_obat` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `sf_cek_stok_obat`(p_obat_id INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE v_stok INT;
    SELECT stok INTO v_stok
    FROM Obat
    WHERE obat_id = p_obat_id;
    RETURN v_stok;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `sf_hitung_subtotal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `sf_hitung_subtotal`(p_harga DECIMAL(10,2), p_jumlah INT) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
    RETURN p_harga * p_jumlah;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `sf_hitung_total_tagihan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `sf_hitung_total_tagihan`(p_tagihan_id INT) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
    DECLARE v_total DECIMAL(10,2);
    
    -- Menggunakan COALESCE agar jika kosong nilainya 0, bukan NULL
    SELECT COALESCE(SUM(subtotal), 0) INTO v_total
    FROM Detail_Tagihan
    WHERE tagihan_id = p_tagihan_id;
    
    RETURN v_total;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `sf_hitung_usia` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `sf_hitung_usia`(p_patient_id INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE v_usia INT;
    SELECT TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) INTO v_usia
    FROM Pasien
    WHERE patient_id = p_patient_id;
    RETURN v_usia;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `sf_validasi_jam_operasional` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `sf_validasi_jam_operasional`(p_waktu DATETIME) RETURNS varchar(50) CHARSET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_bayar_tagihan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_bayar_tagihan`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_buat_resep` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buat_resep`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_catat_tindakan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_catat_tindakan`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_daftar_kunjungan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_daftar_kunjungan`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_generate_laporan_pasien` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generate_laporan_pasien`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_tambah_detail_resep` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tambah_detail_resep`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_update_stok_obat` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_stok_obat`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_update_total_tagihan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_total_tagihan`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `v_kunjungan_hari_ini`
--

/*!50001 DROP VIEW IF EXISTS `v_kunjungan_hari_ini`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_kunjungan_hari_ini` AS select `k`.`kunjungan_id` AS `kunjungan_id`,`p`.`nama` AS `nama_pasien`,`d`.`nama` AS `nama_dokter`,`k`.`waktu_datang` AS `waktu_datang`,`k`.`status` AS `status` from ((`kunjungan` `k` join `pasien` `p` on(`k`.`patient_id` = `p`.`patient_id`)) join `dokter` `d` on(`k`.`dokter_id` = `d`.`dokter_id`)) where cast(`k`.`waktu_datang` as date) = curdate() */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_laporan_tagihan`
--

/*!50001 DROP VIEW IF EXISTS `v_laporan_tagihan`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_laporan_tagihan` AS select `t`.`tagihan_id` AS `tagihan_id`,`p`.`nama` AS `nama_pasien`,`t`.`tanggal_tagihan` AS `tanggal_tagihan`,`t`.`total_tagihan` AS `total_tagihan`,`t`.`status` AS `status`,`t`.`metode_pembayaran` AS `metode_pembayaran` from ((`tagihan` `t` join `kunjungan` `k` on(`t`.`kunjungan_id` = `k`.`kunjungan_id`)) join `pasien` `p` on(`k`.`patient_id` = `p`.`patient_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_obat_hampir_kadaluarsa`
--

/*!50001 DROP VIEW IF EXISTS `v_obat_hampir_kadaluarsa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_obat_hampir_kadaluarsa` AS select `obat`.`nama_obat` AS `nama_obat`,`obat`.`stok` AS `stok`,`obat`.`tgl_kadaluarsa` AS `tgl_kadaluarsa` from `obat` where year(`obat`.`tgl_kadaluarsa`) = year(curdate()) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_rekam_medis_pasien`
--

/*!50001 DROP VIEW IF EXISTS `v_rekam_medis_pasien`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_rekam_medis_pasien` AS select `rm`.`record_id` AS `record_id`,`p`.`nama` AS `nama_pasien`,`d`.`nama` AS `nama_dokter`,`k`.`waktu_datang` AS `waktu_datang`,`rm`.`anamnesa` AS `anamnesa`,`rm`.`catatan_klinis` AS `catatan_klinis` from (((`rekam_medis` `rm` join `kunjungan` `k` on(`rm`.`kunjungan_id` = `k`.`kunjungan_id`)) join `pasien` `p` on(`k`.`patient_id` = `p`.`patient_id`)) join `dokter` `d` on(`k`.`dokter_id` = `d`.`dokter_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_stok_obat_kritis`
--

/*!50001 DROP VIEW IF EXISTS `v_stok_obat_kritis`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_stok_obat_kritis` AS select `obat`.`obat_id` AS `obat_id`,`obat`.`nama_obat` AS `nama_obat`,`obat`.`jenis_obat` AS `jenis_obat`,`obat`.`stok` AS `stok`,`obat`.`tgl_kadaluarsa` AS `tgl_kadaluarsa` from `obat` where `obat`.`stok` < 50 order by `obat`.`stok` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-06-18  0:05:44
