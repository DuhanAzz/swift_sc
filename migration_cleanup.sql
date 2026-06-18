-- ================================================================
-- MIGRASI DATABASE SWIFT SC
-- Cleanup legacy tables & fix cash_flows FK
-- Jalankan script ini di phpMyAdmin
-- ================================================================

-- 1. Rename cash_flows.pool_id → cabang_id
ALTER TABLE `cash_flows` CHANGE `pool_id` `cabang_id` int(11) NOT NULL;

-- 2. Drop old FK constraint (jika ada)
-- Cek dulu apakah constraint ini ada sebelum drop
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'Swift_SC' AND TABLE_NAME = 'cash_flows' AND CONSTRAINT_NAME = 'cash_flows_ibfk_1');
-- Jika error, jalankan manual:
-- ALTER TABLE `cash_flows` DROP FOREIGN KEY `cash_flows_ibfk_1`;

-- 3. Tambah FK baru ke cabang
-- ALTER TABLE `cash_flows` ADD CONSTRAINT `cash_flows_cabang_fk` FOREIGN KEY (`cabang_id`) REFERENCES `cabang`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 4. Drop legacy tables
DROP TABLE IF EXISTS `members`;
DROP TABLE IF EXISTS `performances`;
DROP TABLE IF EXISTS `pools`;
DROP TABLE IF EXISTS `presensi`;
DROP TABLE IF EXISTS `absensi_performa`;
DROP TABLE IF EXISTS `program`;

-- 5. Verifikasi
SHOW TABLES;
