-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 10, 2026 at 07:41 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Swift_SC`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpa') NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `absensi_performa`
--

CREATE TABLE `absensi_performa` (
  `id` int(11) NOT NULL,
  `tipe_input` enum('Absensi','Performa') NOT NULL,
  `member_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status_absensi` enum('Hadir','Izin','Sakit','Alpa') DEFAULT NULL,
  `gaya_renang` varchar(100) DEFAULT NULL,
  `jarak` varchar(20) DEFAULT NULL,
  `waktu_formatted` varchar(15) DEFAULT NULL,
  `waktu_ms` int(11) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `kategori` enum('Prestasi','Pengumuman','Artikel') NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `tanggal` date NOT NULL,
  `cabang` varchar(100) DEFAULT 'Pusat',
  `gambar` varchar(255) DEFAULT 'default_news.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cabang`
--

CREATE TABLE `cabang` (
  `id` int(11) NOT NULL,
  `nama_cabang` varchar(100) NOT NULL,
  `lokasi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cabang`
--

INSERT INTO `cabang` (`id`, `nama_cabang`, `lokasi`, `created_at`) VALUES
(1, 'Pusat (Tirtomoyo)', 'Jl. Tirtomoyo No. 1, Surakarta', '2026-06-10 04:32:15'),
(2, 'Swift SC Cabang Utara', 'Jl. Kolam Renang Utara No. 12, Solo Baru', '2026-06-10 04:32:15'),
(3, 'Swift SC Cabang Selatan', 'Jl. Samudra Biru No. 5, Kartasura', '2026-06-10 04:32:15');

-- --------------------------------------------------------

--
-- Table structure for table `calon_member`
--

CREATE TABLE `calon_member` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `payment_status` enum('Paid','Unpaid') DEFAULT 'Unpaid',
  `status_approval` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_flows`
--

CREATE TABLE `cash_flows` (
  `id` int(11) NOT NULL,
  `pool_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `type` enum('Pemasukan','Pengeluaran') NOT NULL,
  `category` varchar(100) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms_banners`
--

CREATE TABLE `cms_banners` (
  `id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `subjudul` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `status` enum('Aktif','Draft') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms_landing`
--

CREATE TABLE `cms_landing` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `program` text DEFAULT NULL,
  `cabang_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `calon_member_id` int(11) DEFAULT NULL,
  `nia` varchar(50) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `tanggal_gabung` date NOT NULL,
  `payment_status` enum('Paid','Unpaid') DEFAULT 'Unpaid',
  `status_aktif` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `pool_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tanggal_lahir` date NOT NULL,
  `tanggal_gabung` date NOT NULL,
  `payment_status` enum('Paid','Unpaid') DEFAULT 'Paid',
  `status_akun` enum('Pending','Aktif','Nonaktif') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `pool_id`, `nama`, `jenis_kelamin`, `no_hp`, `tanggal_lahir`, `tanggal_gabung`, `payment_status`, `status_akun`, `created_at`) VALUES
(1, 2, 'test 1', 'L', NULL, '2026-04-13', '2026-04-13', 'Paid', 'Pending', '2026-04-13 06:17:02'),
(2, 2, 'denis', 'L', NULL, '2026-04-13', '2026-04-13', 'Paid', 'Pending', '2026-04-13 06:17:02'),
(4, 2, 'adit', 'L', '998765432', '2016-06-14', '2026-04-13', 'Unpaid', 'Pending', '2026-04-13 06:35:41');

-- --------------------------------------------------------

--
-- Table structure for table `pelatih`
--

CREATE TABLE `pelatih` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `sertifikasi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default_coach.jpg',
  `cabang` varchar(100) DEFAULT 'Pusat'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelatih`
--

INSERT INTO `pelatih` (`id`, `nama`, `jabatan`, `sertifikasi`, `foto`, `cabang`) VALUES
(1, 'Budi Santoso', 'Head Coach', 'Sertifikasi FINA Level 2. Mantan atlet nasional dengan pengalaman melatih 10+ tahun.', 'default_coach.jpg', 'Pusat'),
(2, 'Anita Wijaya', 'Pelatih Fisik & Dryland', 'Spesialis strength & conditioning. Bersertifikat ISSA dan pakar gizi olahraga.', 'default_coach.jpg', 'Pusat'),
(3, 'test 1', 'asisten', 'Sertifikasi FINA Level 2. Mantan atlet nasional dengan pengalaman melatih 10+ tahun', 'uploads/coach_69d5f1cf5654d.png', 'Pusat');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `bulan` tinyint(4) NOT NULL,
  `tahun` year(4) NOT NULL,
  `status` enum('Lunas','Belum Bayar') DEFAULT 'Belum Bayar',
  `tgl_bayar` datetime DEFAULT NULL,
  `jumlah_bayar` int(11) DEFAULT 0,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performa`
--

CREATE TABLE `performa` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `gaya_renang` varchar(100) NOT NULL,
  `jarak` varchar(20) NOT NULL,
  `waktu_formatted` varchar(15) NOT NULL,
  `waktu_ms` int(11) NOT NULL,
  `tanggal_rekor` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performances`
--

CREATE TABLE `performances` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `swim_style` enum('Bebas','Dada','Punggung','Kupu-kupu','Ganti') NOT NULL,
  `distance` int(11) NOT NULL,
  `pool_length` enum('25m','50m') NOT NULL,
  `time_formatted` varchar(8) NOT NULL,
  `time_ms` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performances`
--

INSERT INTO `performances` (`id`, `member_id`, `record_date`, `swim_style`, `distance`, `pool_length`, `time_formatted`, `time_ms`, `notes`, `created_at`) VALUES
(2, 1, '2026-03-11', 'Dada', 50, '50m', '00:39.31', 39310, '', '2026-04-07 08:03:05'),
(3, 3, '2026-04-10', 'Bebas', 50, '50m', '00:33.00', 33000, 'perlu perbaikan teknik\r\n', '2026-04-10 11:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `pools`
--

CREATE TABLE `pools` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pools`
--

INSERT INTO `pools` (`id`, `name`, `address`, `created_at`, `updated_at`) VALUES
(2, 'test 1', 'jalan in dul ua jah sia pa ta ujod ohnya\r\n', '2026-04-07 02:18:00', '2026-04-07 02:24:28');

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpa') NOT NULL DEFAULT 'Hadir',
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi`
--

INSERT INTO `presensi` (`id`, `member_id`, `tanggal`, `status`, `keterangan`) VALUES
(2, 1, '2026-04-07', 'Hadir', '');

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `id` int(11) NOT NULL,
  `nama_program` varchar(100) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_latihan`
--

CREATE TABLE `program_latihan` (
  `id` int(11) NOT NULL,
  `pelatih_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `tipe_program` enum('Harian','Mingguan','Bulanan') NOT NULL,
  `catatan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `urutan` int(11) DEFAULT 0,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tentang_club`
--

CREATE TABLE `tentang_club` (
  `id` int(11) NOT NULL,
  `visi` text NOT NULL,
  `misi` text NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('CEO','Admin','Pelatih') NOT NULL,
  `cabang_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `cabang_id`, `created_at`) VALUES
(1, 'ceo_duhan', 'ceo@swiftsc.com', '$2y$10$w/y1cT.cR/J2X8.bNqg99edHk/eTj.C4yCbe.2J9Yh28V376FmN1G', 'CEO', NULL, '2026-06-10 04:32:15'),
(2, 'admin_utara', 'admin.utara@swiftsc.com', '$2y$10$w/y1cT.cR/J2X8.bNqg99edHk/eTj.C4yCbe.2J9Yh28V376FmN1G', 'Admin', 2, '2026-06-10 04:32:15'),
(3, 'coach_budi', 'budi.coach@swiftsc.com', '$2y$10$w/y1cT.cR/J2X8.bNqg99edHk/eTj.C4yCbe.2J9Yh28V376FmN1G', 'Pelatih', 2, '2026-06-10 04:32:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_daily_attendance` (`member_id`,`tanggal`),
  ADD KEY `cabang_id` (`cabang_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `absensi_performa`
--
ALTER TABLE `absensi_performa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `cabang_id` (`cabang_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cabang`
--
ALTER TABLE `cabang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_cabang` (`nama_cabang`);

--
-- Indexes for table `calon_member`
--
ALTER TABLE `calon_member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cabang_id` (`cabang_id`);

--
-- Indexes for table `cash_flows`
--
ALTER TABLE `cash_flows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pool_id` (`pool_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cms_banners`
--
ALTER TABLE `cms_banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_landing`
--
ALTER TABLE `cms_landing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cabang_id` (`cabang_id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nia` (`nia`),
  ADD UNIQUE KEY `calon_member_id` (`calon_member_id`),
  ADD KEY `cabang_id` (`cabang_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pool_id` (`pool_id`);

--
-- Indexes for table `pelatih`
--
ALTER TABLE `pelatih`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atlet_id` (`member_id`,`bulan`,`tahun`);

--
-- Indexes for table `performa`
--
ALTER TABLE `performa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `cabang_id` (`cabang_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `performances`
--
ALTER TABLE `performances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_performa_member` (`member_id`);

--
-- Indexes for table `pools`
--
ALTER TABLE `pools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atlet_id` (`member_id`,`tanggal`);

--
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_latihan`
--
ALTER TABLE `program_latihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_program_pelatih` (`pelatih_id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tentang_club`
--
ALTER TABLE `tentang_club`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `cabang_id` (`cabang_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `absensi_performa`
--
ALTER TABLE `absensi_performa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cabang`
--
ALTER TABLE `cabang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `calon_member`
--
ALTER TABLE `calon_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_flows`
--
ALTER TABLE `cash_flows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_banners`
--
ALTER TABLE `cms_banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_landing`
--
ALTER TABLE `cms_landing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pelatih`
--
ALTER TABLE `pelatih`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performa`
--
ALTER TABLE `performa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performances`
--
ALTER TABLE `performances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pools`
--
ALTER TABLE `pools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_latihan`
--
ALTER TABLE `program_latihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tentang_club`
--
ALTER TABLE `tentang_club`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `absensi_performa`
--
ALTER TABLE `absensi_performa`
  ADD CONSTRAINT `absensi_performa_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `absensi_performa_ibfk_2` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `absensi_performa_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `calon_member`
--
ALTER TABLE `calon_member`
  ADD CONSTRAINT `calon_member_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `cash_flows`
--
ALTER TABLE `cash_flows`
  ADD CONSTRAINT `cash_flows_ibfk_1` FOREIGN KEY (`pool_id`) REFERENCES `pools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cash_flows_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION;

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`calon_member_id`) REFERENCES `calon_member` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `member_ibfk_2` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`pool_id`) REFERENCES `pools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performa`
--
ALTER TABLE `performa`
  ADD CONSTRAINT `performa_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `performa_ibfk_2` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `performa_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `performances`
--
ALTER TABLE `performances`
  ADD CONSTRAINT `fk_performa_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `fk_presensi_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `program_latihan`
--
ALTER TABLE `program_latihan`
  ADD CONSTRAINT `fk_program_pelatih` FOREIGN KEY (`pelatih_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
