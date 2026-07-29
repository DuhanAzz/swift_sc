-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: Swift_SC
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `absensi`
--

DROP TABLE IF EXISTS `absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `absensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpa') NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL,
  `status_bayar` enum('Unpaid','Paid') DEFAULT 'Unpaid',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_daily_attendance` (`member_id`,`tanggal`),
  KEY `cabang_id` (`cabang_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `absensi_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensi`
--

LOCK TABLES `absensi` WRITE;
/*!40000 ALTER TABLE `absensi` DISABLE KEYS */;
INSERT INTO `absensi` VALUES (1,1,'2026-05-03','Hadir',4,5,'2026-06-18 05:04:49',NULL,'Paid'),(2,1,'2026-05-10','Hadir',4,5,'2026-06-18 05:04:49',NULL,'Paid'),(3,1,'2026-05-17','Hadir',4,5,'2026-06-18 05:04:49',NULL,'Paid'),(4,1,'2026-05-24','Hadir',4,5,'2026-06-18 05:04:49',NULL,'Paid'),(5,6,'2026-05-03','Hadir',4,5,'2026-06-18 05:04:49',NULL,'Paid'),(6,6,'2026-05-10','Hadir',4,5,'2026-06-18 05:04:49',NULL,'Paid'),(7,6,'2026-05-17','Hadir',4,5,'2026-06-18 05:04:49',NULL,'Paid'),(8,6,'2026-05-24','Hadir',4,5,'2026-06-18 05:04:49',NULL,'Paid'),(9,1,'2026-06-07','Hadir',4,5,'2026-06-18 05:05:02',NULL,'Paid'),(10,1,'2026-06-14','Hadir',4,5,'2026-06-18 05:05:02',NULL,'Paid'),(11,6,'2026-06-07','Hadir',4,5,'2026-06-18 05:05:02',NULL,'Paid'),(12,6,'2026-06-14','Hadir',4,5,'2026-06-18 05:05:02',NULL,'Paid'),(13,1,'2026-04-05','Hadir',4,5,'2026-06-18 05:05:18',NULL,'Paid'),(14,1,'2026-04-12','Hadir',4,5,'2026-06-18 05:05:18',NULL,'Paid'),(15,1,'2026-04-19','Hadir',4,5,'2026-06-18 05:05:18',NULL,'Paid'),(16,1,'2026-04-26','Hadir',4,5,'2026-06-18 05:05:18',NULL,'Paid'),(17,6,'2026-04-05','Hadir',4,5,'2026-06-18 05:05:18',NULL,'Paid'),(18,6,'2026-04-12','Hadir',4,5,'2026-06-18 05:05:18',NULL,'Paid'),(19,6,'2026-04-19','Hadir',4,5,'2026-06-18 05:05:18',NULL,'Paid'),(20,6,'2026-04-26','Hadir',4,5,'2026-06-18 05:05:18',NULL,'Paid'),(21,7,'2026-06-07','Hadir',4,5,'2026-06-20 04:14:02',NULL,'Paid'),(22,7,'2026-06-14','Hadir',4,5,'2026-06-20 04:14:05',NULL,'Paid'),(23,7,'2026-05-10','Hadir',4,5,'2026-06-20 04:53:58',NULL,'Paid'),(24,7,'2026-05-03','Hadir',4,5,'2026-06-20 05:32:37',NULL,'Paid'),(25,7,'2026-04-05','Hadir',4,5,'2026-06-20 05:32:44',NULL,'Paid'),(26,7,'2026-04-12','Hadir',4,5,'2026-06-20 05:32:44',NULL,'Paid'),(27,7,'2026-05-17','Hadir',4,5,'2026-06-20 05:48:54',NULL,'Paid'),(28,7,'2026-05-24','Hadir',4,5,'2026-06-20 05:48:54',NULL,'Paid'),(29,7,'2026-05-31','Hadir',4,5,'2026-06-20 05:48:54',NULL,'Paid'),(30,1,'2026-05-31','Hadir',4,5,'2026-06-20 05:48:54',NULL,'Paid'),(31,6,'2026-05-31','Hadir',4,5,'2026-06-20 05:48:54',NULL,'Paid'),(34,9,'2026-06-07','Hadir',4,5,'2026-07-02 09:43:54',NULL,'Paid'),(35,9,'2026-06-14','Hadir',4,5,'2026-07-02 09:43:54',NULL,'Paid'),(36,9,'2026-06-21','Hadir',4,5,'2026-07-02 09:43:54',NULL,'Paid'),(37,9,'2026-06-28','Hadir',4,5,'2026-07-02 09:43:54',NULL,'Paid'),(38,9,'2026-07-02','Hadir',4,5,'2026-07-02 09:48:02',NULL,'Paid'),(39,11,'2026-06-07','Hadir',4,5,'2026-07-25 13:33:58',NULL,'Paid'),(40,11,'2026-06-14','Hadir',4,5,'2026-07-25 13:33:58',NULL,'Paid'),(41,11,'2026-07-02','Hadir',4,5,'2026-07-25 13:34:29',NULL,'Paid'),(42,11,'2026-07-09','Hadir',4,5,'2026-07-25 13:34:29',NULL,'Paid'),(43,11,'2026-07-12','Hadir',4,5,'2026-07-25 13:34:29',NULL,'Paid'),(44,11,'2026-06-04','Hadir',4,5,'2026-07-25 13:34:43',NULL,'Paid'),(45,11,'2026-06-11','Hadir',4,5,'2026-07-25 13:34:43',NULL,'Paid'),(46,11,'2026-05-03','Hadir',4,5,'2026-07-25 13:34:54',NULL,'Paid'),(47,11,'2026-05-10','Hadir',4,5,'2026-07-25 13:34:54',NULL,'Paid');
/*!40000 ALTER TABLE `absensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arus_kas`
--

DROP TABLE IF EXISTS `arus_kas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arus_kas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cabang_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('Pemasukan','Pengeluaran') NOT NULL,
  `category` varchar(100) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `cash_flows_ibfk_cabang` (`cabang_id`),
  CONSTRAINT `arus_kas_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `arus_kas_ibfk_cabang` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arus_kas`
--

LOCK TABLES `arus_kas` WRITE;
/*!40000 ALTER TABLE `arus_kas` DISABLE KEYS */;
INSERT INTO `arus_kas` VALUES (1,4,5,'2026-06-20','Pemasukan','SPP',105000.00,'Pembayaran SPP a/n Brian Taufiq Akbar','2026-06-20 05:47:59'),(2,4,5,'2026-06-20','Pemasukan','SPP',35000.00,'Pembayaran SPP a/n Brian Taufiq Akbar','2026-06-20 05:50:48'),(3,4,5,'2026-06-20','Pemasukan','SPP',35000.00,'Pembayaran SPP a/n Humaimah Ghumaisha Adeeva','2026-06-20 05:50:48'),(4,4,5,'2026-06-20','Pemasukan','SPP',35000.00,'Pembayaran SPP a/n Klauna Dikala Alwathoni','2026-06-20 05:50:48'),(5,4,5,'2026-06-20','Pemasukan','SPP',70000.00,'Pembayaran SPP a/n test 1','2026-06-20 06:40:49'),(6,4,5,'2026-07-02','Pemasukan','SPP',37500.00,'Pembayaran SPP a/n test 1','2026-07-02 09:48:48'),(7,4,5,'2026-07-02','Pemasukan','SPP',150000.00,'Pembayaran SPP a/n test 1','2026-07-02 09:49:43'),(8,4,5,'2026-07-06','Pemasukan','Pendaftaran',100000.00,'Biaya Pendaftaran a/n Brian Taufiq Akbar','2026-07-06 03:14:21'),(9,4,5,'2026-07-06','Pemasukan','Pendaftaran',100000.00,'Biaya Pendaftaran a/n Humaimah Ghumaisha Adeeva','2026-07-06 05:19:50'),(10,4,5,'2026-07-06','Pemasukan','Pendaftaran',100000.00,'Biaya Pendaftaran a/n Klauna Dikala Alwathoni','2026-07-06 05:34:04'),(11,4,5,'2026-07-06','Pemasukan','Pendaftaran',100000.00,'Biaya Pendaftaran a/n test 1','2026-07-06 06:38:14'),(12,4,5,'2026-07-06','Pemasukan','Pendaftaran',100000.00,'Biaya Pendaftaran a/n test 1','2026-07-06 06:38:30'),(13,4,5,'2026-07-25','Pengeluaran','Umum',500000.00,'Meeting room','2026-07-25 14:16:52');
/*!40000 ALTER TABLE `arus_kas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berita`
--

DROP TABLE IF EXISTS `berita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `berita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kategori` enum('Prestasi','Pengumuman','Artikel') NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `tanggal` date NOT NULL,
  `cabang` varchar(100) DEFAULT 'Pusat',
  `gambar` varchar(255) DEFAULT 'default_news.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berita`
--

LOCK TABLES `berita` WRITE;
/*!40000 ALTER TABLE `berita` DISABLE KEYS */;
INSERT INTO `berita` VALUES (1,'Prestasi','Juara Umum run swimming Renang ','abcdefghijklmnopqrstuvwxyz','2026-05-31','Umum','uploads/news_6a34ebd977562.jpg','2026-06-19 07:07:30'),(3,'Pengumuman','Juara Umum run swimming Renang ','qwertyuiasdfghjklghjkkhgtyu\r\nkikjujuhyiklokuhtvrcdecv\r\n\r\nnhbgbynyn','2026-07-25','4','uploads/news_6a64ba53f324d.jpg','2026-07-25 13:29:55');
/*!40000 ALTER TABLE `berita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cabang`
--

DROP TABLE IF EXISTS `cabang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cabang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_cabang` varchar(100) NOT NULL,
  `lokasi` text NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `jam_operasional` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_cabang` (`nama_cabang`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabang`
--

LOCK TABLES `cabang` WRITE;
/*!40000 ALTER TABLE `cabang` DISABLE KEYS */;
INSERT INTO `cabang` VALUES (4,'Kolam Renang Umbang Tirta','Jl kota baru ',NULL,NULL,NULL,'2026-06-18 04:29:30');
/*!40000 ALTER TABLE `cabang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calon_member`
--

DROP TABLE IF EXISTS `calon_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calon_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `sekolah` varchar(150) DEFAULT NULL,
  `tanggal_lahir` date NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `payment_status` enum('Paid','Unpaid') DEFAULT 'Unpaid',
  `status_approval` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tingkatan_kelas` varchar(50) DEFAULT 'Pemula',
  PRIMARY KEY (`id`),
  KEY `cabang_id` (`cabang_id`),
  CONSTRAINT `calon_member_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calon_member`
--

LOCK TABLES `calon_member` WRITE;
/*!40000 ALTER TABLE `calon_member` DISABLE KEYS */;
INSERT INTO `calon_member` VALUES (4,'test 1','L','6281327181277',NULL,'2011-03-03',4,'2026-06-18','Unpaid','Approved','2026-06-18 04:51:21','Pemula'),(5,'Test Invoice WA','L','083111030560',NULL,'2015-06-18',4,'2026-06-18','Unpaid','Approved','2026-06-18 04:54:46','Pemula'),(6,'Klauna Dikala Alwathoni','P','6288983743076',NULL,'2016-06-14',4,'2026-06-18','Unpaid','Approved','2026-06-18 04:58:51','Pemula'),(7,'Brian Taufiq Akbar','L','081993189787','sma 1 kota','2016-01-19',4,'2026-06-19','Unpaid','Approved','2026-06-19 11:21:55','Pemula'),(8,'test 1','L','12345678901','sma 1 kota','2015-06-09',4,'2026-06-20','Unpaid','Approved','2026-06-20 06:38:56','Pemula'),(9,'test 1','L','081993189787','sma 1 kota','2018-06-02',4,'2026-07-02','Unpaid','Approved','2026-07-02 09:38:00','Pemula'),(10,'test 1','P','1234567890','mkcsmapsknp','2011-11-11',4,'2026-07-06','Unpaid','Approved','2026-07-06 06:30:44','Pemula'),(11,'test 12','L','081993189787','sma 1 kota','2014-06-10',4,'2026-07-25','Unpaid','Approved','2026-07-25 13:18:38','Pemula');
/*!40000 ALTER TABLE `calon_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_banners`
--

DROP TABLE IF EXISTS `cms_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gambar` varchar(255) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `subjudul` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `status` enum('Aktif','Draft') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_banners`
--

LOCK TABLES `cms_banners` WRITE;
/*!40000 ALTER TABLE `cms_banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_landing`
--

DROP TABLE IF EXISTS `cms_landing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_landing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipe` enum('Banner','Berita','Jadwal') NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori` enum('Prestasi','Pengumuman','Artikel') DEFAULT NULL,
  `tanggal_publish` date DEFAULT NULL,
  `cabang_nama` varchar(100) DEFAULT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `status` enum('Aktif','Draft') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_landing`
--

LOCK TABLES `cms_landing` WRITE;
/*!40000 ALTER TABLE `cms_landing` DISABLE KEYS */;
INSERT INTO `cms_landing` VALUES (1,'Banner','Swift Swimming Club','“SWIFT GO CHAMPION!”',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'Aktif','2026-06-21 12:22:27','2026-07-25 12:44:35');
/*!40000 ALTER TABLE `cms_landing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dokumen_periodisasi`
--

DROP TABLE IF EXISTS `dokumen_periodisasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dokumen_periodisasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `file_pdf` varchar(255) NOT NULL,
  `status` enum('Aktif','Arsip') DEFAULT 'Aktif',
  `tanggal_upload` datetime DEFAULT current_timestamp(),
  `uploader_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dokumen_periodisasi`
--

LOCK TABLES `dokumen_periodisasi` WRITE;
/*!40000 ALTER TABLE `dokumen_periodisasi` DISABLE KEYS */;
INSERT INTO `dokumen_periodisasi` VALUES (1,'Program latihan tes','periodisasi_1785298788_3537.pdf','Aktif','2026-07-29 11:19:48',4);
/*!40000 ALTER TABLE `dokumen_periodisasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluasi_kualitatif`
--

DROP TABLE IF EXISTS `evaluasi_kualitatif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evaluasi_kualitatif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `pelatih_id` int(11) NOT NULL,
  `tanggal_evaluasi` date NOT NULL,
  `postur_streamline` tinyint(4) NOT NULL DEFAULT 3,
  `teknik_turn` tinyint(4) NOT NULL DEFAULT 3,
  `teknik_start` tinyint(4) NOT NULL DEFAULT 3,
  `disiplin` tinyint(4) NOT NULL DEFAULT 3,
  `catatan_pelatih` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `pelatih_id` (`pelatih_id`),
  CONSTRAINT `evaluasi_kualitatif_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluasi_kualitatif_ibfk_2` FOREIGN KEY (`pelatih_id`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluasi_kualitatif`
--

LOCK TABLES `evaluasi_kualitatif` WRITE;
/*!40000 ALTER TABLE `evaluasi_kualitatif` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluasi_kualitatif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal`
--

DROP TABLE IF EXISTS `jadwal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `program` text DEFAULT NULL,
  `cabang_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cabang_id` (`cabang_id`),
  CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal`
--

LOCK TABLES `jadwal` WRITE;
/*!40000 ALTER TABLE `jadwal` DISABLE KEYS */;
INSERT INTO `jadwal` VALUES (1,'Kamis','15:30:00','17:00:00','Kolam Renang Umbang Tirta','',NULL,'2026-06-18 04:30:41'),(2,'Sabtu','15:03:00','17:00:00','Kolam Renang Umbang Tirta','',NULL,'2026-06-18 04:31:03');
/*!40000 ALTER TABLE `jadwal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kalender_event`
--

DROP TABLE IF EXISTS `kalender_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalender_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_event` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kalender_event`
--

LOCK TABLES `kalender_event` WRITE;
/*!40000 ALTER TABLE `kalender_event` DISABLE KEYS */;
INSERT INTO `kalender_event` VALUES (1,'Time Trial Tirta Amanda Swimming Club 2026','2026-01-11','2026-01-11','Kolam Renang Depok Sport Center (DSC)','qwerty');
/*!40000 ALTER TABLE `kalender_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calon_member_id` int(11) DEFAULT NULL,
  `nia` varchar(50) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `sekolah` varchar(150) DEFAULT NULL,
  `tanggal_lahir` date NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `tanggal_gabung` date NOT NULL,
  `payment_status` enum('Paid','Unpaid') DEFAULT 'Unpaid',
  `status_aktif` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tingkatan_kelas` varchar(50) DEFAULT 'Pemula',
  `pelatih_id` int(11) DEFAULT NULL,
  `biaya_pendaftaran` int(11) DEFAULT 100000,
  `biaya_bulanan` int(11) DEFAULT 350000,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nia` (`nia`),
  UNIQUE KEY `calon_member_id` (`calon_member_id`),
  KEY `cabang_id` (`cabang_id`),
  KEY `fk_member_pelatih` (`pelatih_id`),
  CONSTRAINT `fk_member_pelatih` FOREIGN KEY (`pelatih_id`) REFERENCES `pelatih` (`id`) ON DELETE SET NULL,
  CONSTRAINT `member_ibfk_1` FOREIGN KEY (`calon_member_id`) REFERENCES `calon_member` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `member_ibfk_2` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES (1,NULL,'SWF-2026-0001','Humaimah Ghumaisha Adeeva','P','1234567890',NULL,'2026-06-18',4,'2026-06-18','Paid','Aktif','2026-06-18 04:43:29','Pemula',4,100000,350000),(6,6,'SWF-2026-0002','Klauna Dikala Alwathoni','P','6288983743076',NULL,'2016-06-14',4,'2026-06-18','Paid','Aktif','2026-06-18 04:59:00','Pemula',4,100000,350000),(7,7,'SWF-2026-0007','Brian Taufiq Akbar','L','081993189787','','2016-01-19',4,'2026-06-19','Paid','Aktif','2026-06-19 11:22:12','Pemula',NULL,100000,350000),(9,9,'SWF-2026-0008','test 1','L','081993189787',NULL,'2018-06-02',4,'2026-07-02','Paid','Aktif','2026-07-02 09:40:34','Pemula',4,100000,350000),(10,10,'SWF-2026-0010','test 1','P','1234567890','mkcsmapsknp','2011-11-11',4,'2026-07-06','Paid','Aktif','2026-07-06 06:35:32','Pemula',4,100000,350000),(11,11,'SWF-2026-0011','test 12','L','081993189787','sma 1 kota','2014-06-10',4,'2026-07-25','Unpaid','Aktif','2026-07-25 13:22:32','Pemula',4,100000,350000);
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paket_biaya`
--

DROP TABLE IF EXISTS `paket_biaya`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paket_biaya` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_paket` varchar(100) NOT NULL,
  `harga` varchar(50) NOT NULL,
  `satuan_waktu` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `fitur_1` varchar(255) DEFAULT NULL,
  `fitur_2` varchar(255) DEFAULT NULL,
  `fitur_3` varchar(255) DEFAULT NULL,
  `is_populer` tinyint(1) DEFAULT 0,
  `urutan` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paket_biaya`
--

LOCK TABLES `paket_biaya` WRITE;
/*!40000 ALTER TABLE `paket_biaya` DISABLE KEYS */;
INSERT INTO `paket_biaya` VALUES (1,'Pemula / Reguler','Rp 225rb','/bln','Program dasar yang menyenangkan untuk anak-anak.','8x Sesi Latihan','Usia Min. 4 Tahun','Pendaftaran Rp 100rb',0,1),(2,'Kelas Prestasi','Rp 250rb','/bln','Pelatihan intensif untuk atlet persiapan mengikuti berbagai kompetisi kejuaraan.','Porsi latihan intens','Pemantauan limit waktu','Program periodisasi juara',1,2),(3,'Privat & TNI/Polri','Konsultasi','','1-on-1 coach untuk percepatan & persiapan tes fisik instansi.','Waktu fleksibel','Tanpa batasan usia','Fokus teknik mendalam',0,3);
/*!40000 ALTER TABLE `paket_biaya` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pelatih`
--

DROP TABLE IF EXISTS `pelatih`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pelatih` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `sertifikasi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default_coach.jpg',
  `id_kolam` int(11) DEFAULT NULL,
  `cabang` varchar(100) DEFAULT 'Pusat',
  `is_highlighted` tinyint(1) DEFAULT 0,
  `lisensi` varchar(100) DEFAULT NULL,
  `kelas_mengajar` varchar(50) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `foto_hover_1` varchar(255) DEFAULT NULL,
  `foto_hover_2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pelatih`
--

LOCK TABLES `pelatih` WRITE;
/*!40000 ALTER TABLE `pelatih` DISABLE KEYS */;
INSERT INTO `pelatih` VALUES (4,7,'Hafifa Salma','Asisten Coach','Lisensi C','coach_4_1782047646.PNG',4,'Kolam Renang Umbang Tirta',1,'Lisensi C','Pemula','0882008134778','pelatih_hover_1_1782047181.PNG','pelatih_hover_2_1782047181.PNG');
/*!40000 ALTER TABLE `pelatih` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `bulan` tinyint(4) NOT NULL,
  `tahun` year(4) NOT NULL,
  `status` enum('Lunas','Belum Bayar') DEFAULT 'Belum Bayar',
  `tgl_bayar` datetime DEFAULT NULL,
  `jumlah_bayar` int(11) DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `jumlah_sesi_terbayar` int(11) DEFAULT NULL,
  `cover_tgl_awal` date DEFAULT NULL,
  `cover_tgl_akhir` date DEFAULT NULL,
  `detail_tanggal` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_member_id` (`member_id`),
  CONSTRAINT `fk_pembayaran_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
INSERT INTO `pembayaran` VALUES (1,1,6,2026,'Lunas','2026-06-20 12:50:48',35000,'',1,'2026-05-31','2026-05-31','31 Mei'),(2,6,6,2026,'Lunas','2026-06-20 12:50:48',35000,'',1,'2026-05-31','2026-05-31','31 Mei'),(3,7,6,2026,'Lunas','2026-06-20 12:30:22',105000,'',1,'2026-06-14','2026-06-14','14 Jun'),(6,7,6,2026,'Lunas','2026-06-20 12:46:23',105000,'',3,'2026-04-05','2026-05-03','05 Apr, 12 Apr, 03 Mei'),(7,7,6,2026,'Lunas','2026-06-20 12:46:26',105000,'',3,'2026-04-05','2026-05-03','05 Apr, 12 Apr, 03 Mei'),(8,7,6,2026,'Lunas','2026-06-20 12:46:38',105000,'',3,'2026-04-05','2026-05-03','05 Apr, 12 Apr, 03 Mei'),(9,7,6,2026,'Lunas','2026-06-20 12:47:59',105000,'',3,'2026-04-05','2026-05-03','05 Apr, 12 Apr, 03 Mei'),(10,7,6,2026,'Lunas','2026-06-20 12:50:48',35000,'',1,'2026-05-31','2026-05-31','31 Mei'),(11,7,6,2026,'Lunas','2026-06-20 12:51:08',0,'',1,'2026-05-24','2026-05-24','24 Mei'),(12,7,6,2026,'Lunas','2026-06-20 12:51:16',0,'',1,'2026-05-17','2026-05-17','17 Mei'),(14,9,7,2026,'Lunas','2026-07-02 16:48:48',37500,'',1,'2026-06-07','2026-06-07','07 Jun'),(15,9,7,2026,'Lunas','2026-07-02 16:49:43',150000,'',4,'2026-06-14','2026-07-02','14 Jun, 21 Jun, 28 Jun, 02 Jul');
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performa`
--

DROP TABLE IF EXISTS `performa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `performa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `gaya_renang` varchar(100) NOT NULL,
  `jarak` varchar(20) NOT NULL,
  `tipe_kolam` varchar(50) DEFAULT NULL,
  `waktu_formatted` varchar(15) NOT NULL,
  `waktu_ms` int(11) NOT NULL,
  `tanggal_rekor` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_id` int(11) DEFAULT NULL,
  `lintasan` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `cabang_id` (`cabang_id`),
  KEY `recorded_by` (`recorded_by`),
  CONSTRAINT `performa_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `performa_ibfk_2` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `performa_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performa`
--

LOCK TABLES `performa` WRITE;
/*!40000 ALTER TABLE `performa` DISABLE KEYS */;
/*!40000 ALTER TABLE `performa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performa_splits`
--

DROP TABLE IF EXISTS `performa_splits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `performa_splits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performa_id` int(11) NOT NULL,
  `jarak_split` int(11) NOT NULL,
  `waktu_lap_ms` int(11) NOT NULL,
  `waktu_lap_format` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `performa_id` (`performa_id`),
  CONSTRAINT `performa_splits_ibfk_1` FOREIGN KEY (`performa_id`) REFERENCES `performa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performa_splits`
--

LOCK TABLES `performa_splits` WRITE;
/*!40000 ALTER TABLE `performa_splits` DISABLE KEYS */;
/*!40000 ALTER TABLE `performa_splits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_latihan`
--

DROP TABLE IF EXISTS `program_latihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_latihan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pelatih_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `target_grup` varchar(50) DEFAULT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `tipe_program` enum('Harian','Mingguan','Bulanan') NOT NULL,
  `deskripsi` text NOT NULL,
  `cabang_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_program_pelatih` (`pelatih_id`),
  CONSTRAINT `fk_program_pelatih` FOREIGN KEY (`pelatih_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_latihan`
--

LOCK TABLES `program_latihan` WRITE;
/*!40000 ALTER TABLE `program_latihan` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_latihan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slider`
--

DROP TABLE IF EXISTS `slider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gambar` varchar(255) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `subjudul` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `caption` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slider`
--

LOCK TABLES `slider` WRITE;
/*!40000 ALTER TABLE `slider` DISABLE KEYS */;
INSERT INTO `slider` VALUES (7,'slider_1784983737.jpg',NULL,NULL,NULL,1,'Aktif',''),(8,'slider_1784983847.jpg',NULL,NULL,NULL,2,'Aktif',''),(9,'slider_1784983927.jpg',NULL,NULL,NULL,3,'Aktif',''),(10,'slider_1784983935.jpg',NULL,NULL,NULL,4,'Aktif','');
/*!40000 ALTER TABLE `slider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tentang_club`
--

DROP TABLE IF EXISTS `tentang_club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tentang_club` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visi` text NOT NULL,
  `misi` text NOT NULL,
  `deskripsi` text NOT NULL,
  `feature_1` varchar(255) DEFAULT NULL,
  `feature_2` varchar(255) DEFAULT NULL,
  `feature_3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tentang_club`
--

LOCK TABLES `tentang_club` WRITE;
/*!40000 ALTER TABLE `tentang_club` DISABLE KEYS */;
INSERT INTO `tentang_club` VALUES (1,'Menjadi klub renang yang dipercaya masyarakat dalam membentuk perenang yang terampil, percaya diri, berkarakter, dan berprestasi melalui pembelajaran yang aman, profesional, dan menyenangkan.','1.  Menyelenggarakan pembelajaran renang yang aman dan berkualitas.\r\n2. Mengembangkan kemampuan peserta sesuai tahapan dan potensi masing-masing.\r\n3. Menumbuhkan karakter disiplin, percaya diri, dan sportivitas.\r\n4. Menciptakan lingkungan belajar yang positif, nyaman, dan kekeluargaan.\r\n5. Mendorong lahirnya atlet-atlet berprestasi tanpa melupakan pentingnya proses belajar.','Swift Swimming Club merupakan klub renang resmi yang berkomitmen menghadirkan pembelajaran renang yang aman, menyenangkan, dan berkualitas bagi semua usia.\r\n\r\nKami percaya bahwa setiap orang memiliki potensi untuk berkembang melalui proses belajar yang tepat. Oleh karena itu, Swift menyediakan program latihan yang terstruktur, mulai dari kelas pemula hingga kelas pengembangan dan pembinaan prestasi.\r\n\r\nDidukung oleh pelatih berlisensi nasional dan internasional, Swift tidak hanya berfokus pada kemampuan berenang, tetapi juga membangun kepercayaan diri, kedisiplinan, sportivitas, dan karakter positif setiap peserta didik.\r\n\r\nDi Swift, setiap kayuhan adalah langkah menuju versi terbaik dari diri sendiri.','PROFESSIONAL','SAFETY','ACHIEVEMENT');
/*!40000 ALTER TABLE `tentang_club` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('CEO','Admin','Pelatih') NOT NULL,
  `cabang_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `cabang_id` (`cabang_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,'CEO Swift SC','ceoswiftsc@swift.com','$2y$10$plZxp7T54GSQ784GsIDbteMr8t83hlRC2dU2OogxEIyz3TmmHeFW6','CEO',NULL,'2026-06-18 02:18:31'),(5,'Admin Umbang Tirta Swift SC','adminumbang@swift.com','$2y$10$lJed1PjKazdALAs0HfpTiexbtdT5x0NMNG4lecf1WqT0gQwJko6L.','Admin',4,'2026-06-18 02:18:31'),(7,'Hafifa Salma','hafifasalma.coach@swift.com','$2y$10$akv5B.T8K.T2w4DPdAh/.O6bPXoziYt5xy7Wj/3O7DESFsKREWEmW','Pelatih',4,'2026-06-19 05:03:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-29 11:33:23
