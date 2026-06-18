-- ================================================================
-- MIGRASI DATABASE SWIFT SC
-- Cleanup legacy tables
-- Jalankan script ini di phpMyAdmin
-- ================================================================

-- cash_flows.pool_id sudah di-rename ke cabang_id ✅
-- Langsung drop legacy tables saja:

DROP TABLE IF EXISTS `members`;
DROP TABLE IF EXISTS `performances`;
DROP TABLE IF EXISTS `pools`;
DROP TABLE IF EXISTS `presensi`;
DROP TABLE IF EXISTS `absensi_performa`;
DROP TABLE IF EXISTS `program`;

-- Verifikasi
SHOW TABLES;
