-- =====================================================================
-- DATABASE SCHEMA DDL FOR SWIFT SC SWIMMING CLUB MANAGEMENT SYSTEM
-- =====================================================================
-- Database: if0_42047561_swift_sc (or local database)
-- Target Platform: PHP Native Procedural & MySQLi (XAMPP / MySQL)
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `cms_landing`;
DROP TABLE IF EXISTS `cms_banners`;
DROP TABLE IF EXISTS `berita`;
DROP TABLE IF EXISTS `jadwal`;
DROP TABLE IF EXISTS `absensi`;
DROP TABLE IF EXISTS `performa`;
DROP TABLE IF EXISTS `absensi_performa`;
DROP TABLE IF EXISTS `member`;
DROP TABLE IF EXISTS `calon_member`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `cabang`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. TABEL CABANG / POOLS (LOKASI LATIHAN)
-- ---------------------------------------------------------------------
-- Diperlukan untuk merelasikan users, member, calon_member, dan absensi
-- ke cabang kolam renang masing-masing.
CREATE TABLE `cabang` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_cabang` VARCHAR(100) NOT NULL UNIQUE,
    `lokasi` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. TABEL USERS (AKSES CEO, ADMIN, PELATIH)
-- ---------------------------------------------------------------------
-- Menyimpan data login pengguna dengan 3 hak akses.
-- Cabang_id diset NULL untuk role CEO karena CEO memiliki akses global ke seluruh cabang.
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL, -- Disimpan menggunakan password_hash() PHP
    `role` ENUM('CEO', 'Admin', 'Pelatih') NOT NULL,
    `cabang_id` INT NULL, -- Hubungan ke cabang (NULL = Semua cabang / CEO)
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. TABEL CALON_MEMBER (PENDAFTARAN ONLINE PENDING)
-- ---------------------------------------------------------------------
-- Menampung pendaftaran dari form eksternal sebelum disetujui (approve).
CREATE TABLE `calon_member` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(150) NOT NULL,
    `jenis_kelamin` ENUM('L', 'P') NOT NULL,
    `no_hp` VARCHAR(20) NOT NULL,
    `tanggal_lahir` DATE NOT NULL,
    `cabang_id` INT NOT NULL,
    `tanggal_daftar` DATE NOT NULL,
    `payment_status` ENUM('Paid', 'Unpaid') DEFAULT 'Unpaid',
    `status_approval` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. TABEL MEMBER / ATLET (MEMBER AKTIF YANG SUDAH DI-APPROVE)
-- ---------------------------------------------------------------------
-- Menyimpan data atlet resmi klub renang Swift SC.
CREATE TABLE `member` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `calon_member_id` INT UNIQUE NULL, -- Referensi asal pendaftaran online
    `nia` VARCHAR(50) NOT NULL UNIQUE, -- Nomor Induk Atlet (misal: SWF-2026-1234)
    `nama` VARCHAR(150) NOT NULL,
    `jenis_kelamin` ENUM('L', 'P') NOT NULL,
    `no_hp` VARCHAR(20) NOT NULL,
    `tanggal_lahir` DATE NOT NULL,
    `cabang_id` INT NOT NULL,
    `tanggal_gabung` DATE NOT NULL,
    `payment_status` ENUM('Paid', 'Unpaid') DEFAULT 'Unpaid',
    `status_aktif` ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`calon_member_id`) REFERENCES `calon_member` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- OPSI A (SANGAT DIREKOMENDASIKAN): NORMALISASI TABEL ABSENSI & PERFORMA
-- =====================================================================
-- Memisahkan tabel Absensi dan Performa karena keduanya memiliki
-- struktur kolom yang berbeda secara signifikan (Satu untuk kehadiran harian,
-- satu lagi untuk pencatatan rekor waktu olahraga renang).

-- A.1. TABEL ABSENSI ATLET
CREATE TABLE `absensi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `member_id` INT NOT NULL,
    `tanggal` DATE NOT NULL,
    `status` ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') NOT NULL,
    `cabang_id` INT NOT NULL,
    `recorded_by` INT NOT NULL, -- User ID Pelatih yang menginput
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_daily_attendance` (`member_id`, `tanggal`),
    FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A.2. TABEL PERFORMA ATLET
CREATE TABLE `performa` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `member_id` INT NOT NULL,
    `cabang_id` INT NOT NULL,
    `gaya_renang` VARCHAR(100) NOT NULL, -- Contoh: Bebas (Freestyle), Dada (Breaststroke), dll.
    `jarak` VARCHAR(20) NOT NULL,        -- Contoh: 25, 50, 100, 200, 400, 800, 1500 (meter)
    `waktu_formatted` VARCHAR(15) NOT NULL, -- Format tampilan: "01:15.50" (Menit:Detik.Milidetik)
    `waktu_ms` INT NOT NULL,             -- Total milidetik (Untuk sorting/Leaderboard tercepat)
    `tanggal_rekor` DATE NOT NULL,
    `catatan` TEXT NULL,                 -- Catatan tambahan dari pelatih
    `recorded_by` INT NOT NULL,          -- User ID Pelatih yang menginput
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- OPSI B (ALTERNATIF): SINGLE-TABLE ABSENSI_PERFORMA
-- =====================================================================
-- Jika Anda benar-benar membutuhkan hanya SATU tabel tunggal bernama
-- "absensi_performa" yang menggabungkan presensi dan waktu performa.
-- Kolom-kolom opsional akan diset NULL berdasarkan tipe_input.
CREATE TABLE `absensi_performa` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tipe_input` ENUM('Absensi', 'Performa') NOT NULL,
    `member_id` INT NOT NULL,
    `cabang_id` INT NOT NULL,
    `tanggal` DATE NOT NULL,
    
    -- Kolom khusus Absensi (Hanya terisi jika tipe_input = 'Absensi')
    `status_absensi` ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') NULL,
    
    -- Kolom khusus Performa (Hanya terisi jika tipe_input = 'Performa')
    `gaya_renang` VARCHAR(100) NULL,
    `jarak` VARCHAR(20) NULL,
    `waktu_formatted` VARCHAR(15) NULL,
    `waktu_ms` INT NULL,
    `catatan` TEXT NULL,
    
    -- Relasi ke Pelatih pencatat
    `recorded_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- OPSI C (SANGAT DIREKOMENDASIKAN): NORMALISASI DEDICATED CMS TABLES
-- =====================================================================
-- Lebih terstruktur karena Banner, Berita, dan Jadwal Latihan memiliki
-- struktur data dan kebutuhan relasi yang sangat berbeda di CMS Anda.

-- C.1. TABEL BANNER CMS
CREATE TABLE `cms_banners` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gambar` VARCHAR(255) NOT NULL, -- Path file gambar banner (e.g. "uploads/hero_bg.jpg")
    `judul` VARCHAR(255) NULL,     -- Contoh: "Jump in. Let's swim!"
    `subjudul` VARCHAR(255) NULL,  -- Deskripsi teks banner
    `link` VARCHAR(255) NULL,      -- CTA Link tombol
    `urutan` INT DEFAULT 0,
    `status` ENUM('Aktif', 'Draft') DEFAULT 'Aktif',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- C.2. TABEL BERITA CMS (Menampung Berita & Pengumuman)
CREATE TABLE `berita` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kategori` ENUM('Prestasi', 'Pengumuman', 'Artikel') NOT NULL,
    `judul` VARCHAR(255) NOT NULL,
    `isi` TEXT NOT NULL,
    `tanggal` DATE NOT NULL,
    `cabang` VARCHAR(100) DEFAULT 'Pusat', -- Bisa diisi string nama cabang atau dihubungkan ke tabel cabang
    `gambar` VARCHAR(255) DEFAULT 'default_news.jpg',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- C.3. TABEL JADWAL LATIHAN CMS
CREATE TABLE `jadwal` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `hari` ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
    `jam_mulai` TIME NOT NULL,
    `jam_selesai` TIME NOT NULL,
    `lokasi` VARCHAR(255) NOT NULL, -- Nama kolam renang/tempat latihan
    `program` TEXT NULL,           -- Deskripsi program latihan (e.g. "Sprint Bebas 50m")
    `cabang_id` INT NULL,          -- Opsional: Relasi ke cabang Swift SC jika spesifik cabang tertentu
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- OPSI D (ALTERNATIF): SINGLE-TABLE CMS_LANDING
-- =====================================================================
-- Jika Anda ingin menggabungkan banner, berita, dan jadwal latihan
-- ke dalam SATU tabel dynamic "cms_landing".
CREATE TABLE `cms_landing` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tipe` ENUM('Banner', 'Berita', 'Jadwal') NOT NULL,
    
    -- Judul (News title, banner text, atau Hari jadwal)
    `judul` VARCHAR(255) NOT NULL,
    `konten` TEXT NULL,           -- Isi berita, deskripsi banner, atau program latihan
    `gambar` VARCHAR(255) NULL,   -- Path gambar (banner / berita)
    
    -- Kolom Spesifik Berita
    `kategori` ENUM('Prestasi', 'Pengumuman', 'Artikel') NULL,
    `tanggal_publish` DATE NULL,
    `cabang_nama` VARCHAR(100) NULL, -- Menunjuk nama cabang
    
    -- Kolom Spesifik Jadwal
    `hari` ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NULL,
    `jam_mulai` TIME NULL,
    `jam_selesai` TIME NULL,
    `lokasi` VARCHAR(255) NULL,
    
    -- Kolom Spesifik Banner
    `link` VARCHAR(255) NULL,
    `urutan` INT DEFAULT 0,
    `status` ENUM('Aktif', 'Draft') DEFAULT 'Aktif',
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- SEED DATA CONTOH (Untuk Test Input & Verifikasi Awal)
-- =====================================================================

-- Tambah Cabang Default
INSERT INTO `cabang` (`id`, `nama_cabang`, `lokasi`) VALUES
(1, 'Pusat (Tirtomoyo)', 'Jl. Tirtomoyo No. 1, Surakarta'),
(2, 'Swift SC Cabang Utara', 'Jl. Kolam Renang Utara No. 12, Solo Baru'),
(3, 'Swift SC Cabang Selatan', 'Jl. Samudra Biru No. 5, Kartasura');

-- Tambah User Default (Password hash untuk: SwiftAdmin2026! / pelatih123 / ceo123)
-- Menggunakan standard password_hash() bcrypt ($2y$)
INSERT INTO `users` (`username`, `email`, `password`, `role`, `cabang_id`) VALUES
('ceo_duhan', 'ceo@swiftsc.com', '$2y$10$w/y1cT.cR/J2X8.bNqg99edHk/eTj.C4yCbe.2J9Yh28V376FmN1G', 'CEO', NULL),
('admin_utara', 'admin.utara@swiftsc.com', '$2y$10$w/y1cT.cR/J2X8.bNqg99edHk/eTj.C4yCbe.2J9Yh28V376FmN1G', 'Admin', 2),
('coach_budi', 'budi.coach@swiftsc.com', '$2y$10$w/y1cT.cR/J2X8.bNqg99edHk/eTj.C4yCbe.2J9Yh28V376FmN1G', 'Pelatih', 2);
