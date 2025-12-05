-- SQL Script untuk menambah kolom field simpanan di table general
-- Tanggal: 2025-12-05

ALTER TABLE `general`
ADD COLUMN `jenis_simpanan` VARCHAR(20) NULL COMMENT 'Jenis simpanan: pokok atau wajib' AFTER `loanCode`,
ADD COLUMN `setoran_simpanan_pokok` DECIMAL(15,2) NULL DEFAULT 0.00 COMMENT 'Nilai setoran simpanan pokok' AFTER `jenis_simpanan`,
ADD COLUMN `deskripsi_simpanan` TEXT NULL COMMENT 'Deskripsi simpanan' AFTER `setoran_simpanan_pokok`;

-- Verifikasi kolom yang ditambahkan
-- SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT
-- FROM INFORMATION_SCHEMA.COLUMNS
-- WHERE TABLE_NAME = 'general'
-- AND COLUMN_NAME IN ('jenis_simpanan', 'setoran_simpanan_pokok', 'deskripsi_simpanan');
