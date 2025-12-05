-- SQL Script untuk menambah kolom field di table depositoracc
-- Tanggal: 2025-12-05

ALTER TABLE `depositoracc`
ADD COLUMN `nominal_simpanan` DECIMAL(15,2) NULL DEFAULT 0.00 COMMENT 'Nominal simpanan awal' AFTER `Balance`,
ADD COLUMN `sumber_dana` VARCHAR(50) NULL COMMENT 'Sumber dana: Hasil Usaha, Pendapatan Bulanan, Lain-lain' AFTER `nominal_simpanan`;

-- Verifikasi kolom yang ditambahkan
-- SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT
-- FROM INFORMATION_SCHEMA.COLUMNS
-- WHERE TABLE_NAME = 'depositoracc'
-- AND COLUMN_NAME IN ('nominal_simpanan', 'sumber_dana');
