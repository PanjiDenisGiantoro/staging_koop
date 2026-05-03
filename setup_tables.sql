
CREATE TABLE IF NOT EXISTS `setting_coa` (
  `settingID`    INT(11)      NOT NULL AUTO_INCREMENT,
  `modul`        VARCHAR(50)  NOT NULL,
  `kode_setting` VARCHAR(50)  NOT NULL,
  `label`        VARCHAR(100) NOT NULL,
  `ledger_code`  VARCHAR(50)  NOT NULL DEFAULT '',
  `ledger_name`  VARCHAR(100) NOT NULL DEFAULT '',
  `updatedDate`  DATETIME     DEFAULT NULL,
  `updatedBy`    VARCHAR(50)  DEFAULT NULL,
  PRIMARY KEY (`settingID`),
  UNIQUE KEY `uq_modul_kode` (`modul`, `kode_setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `setting_coa` (`modul`, `kode_setting`, `label`) VALUES
('pendanaan', 'KAS_POOL',         'Kas Pool Dana'),
('pendanaan', 'MODAL_DANA',       'Modal / Sumber Dana'),
('pendanaan', 'PIUTANG',          'Piutang Anggota'),
('pendanaan', 'PENDAPATAN_BUNGA', 'Pendapatan Bunga'),
('pendanaan', 'PENDAPATAN_DENDA', 'Pendapatan Denda Keterlambatan'),
('pos',       'KAS_PENJUALAN',    'Kas / Bank Penjualan'),
('pos',       'PENDAPATAN_JUAL',  'Pendapatan Penjualan'),
('pos',       'HPP',              'Harga Pokok Penjualan'),
('pos',       'PERSEDIAAN',       'Persediaan Barang'),
('produk',    'PERSEDIAAN',       'Persediaan Barang'),
('produk',    'BEBAN_PEMBELIAN',  'Beban / Kas Pembelian');


CREATE TABLE IF NOT EXISTS `pendanaan_pool` (
  `poolID`       INT(11)       NOT NULL AUTO_INCREMENT,
  `saldo`        DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `total_masuk`  DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `total_keluar` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `updatedDate`  DATETIME      DEFAULT NULL,
  `updatedBy`    VARCHAR(50)   DEFAULT NULL,
  PRIMARY KEY (`poolID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Baris inisial poolID=1 (wajib ada)
INSERT IGNORE INTO `pendanaan_pool` (`poolID`, `saldo`, `total_masuk`, `total_keluar`)
VALUES (1, 0.00, 0.00, 0.00);


-- ============================================================
-- 3. pendanaan_pool_trx
-- ============================================================
CREATE TABLE IF NOT EXISTS `pendanaan_pool_trx` (
  `trxID`         INT(11)       NOT NULL AUTO_INCREMENT,
  `jenis`         VARCHAR(10)   NOT NULL COMMENT 'MASUK / KELUAR',
  `ref_tabel`     VARCHAR(50)   DEFAULT NULL,
  `ref_id`        INT(11)       NOT NULL DEFAULT '0',
  `nominal`       DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `saldo_sesudah` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `keterangan`    VARCHAR(255)  DEFAULT NULL,
  `createdDate`   DATETIME      DEFAULT NULL,
  `createdBy`     VARCHAR(50)   DEFAULT NULL,
  PRIMARY KEY (`trxID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ============================================================
-- 4. pendanaan_jurnal
-- ============================================================
CREATE TABLE IF NOT EXISTS `pendanaan_jurnal` (
  `jurnalID`     INT(11)       NOT NULL AUTO_INCREMENT,
  `no_jurnal`    VARCHAR(30)   NOT NULL,
  `jenis`        VARCHAR(30)   NOT NULL,
  `ref_tabel`    VARCHAR(50)   DEFAULT NULL,
  `ref_id`       INT(11)       NOT NULL DEFAULT '0',
  `keterangan`   VARCHAR(255)  DEFAULT NULL,
  `tarikh`       DATE          DEFAULT NULL,
  `total_debit`  DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `total_kredit` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `createdDate`  DATETIME      DEFAULT NULL,
  `createdBy`    VARCHAR(50)   DEFAULT NULL,
  PRIMARY KEY (`jurnalID`),
  UNIQUE KEY `uq_no_jurnal` (`no_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ============================================================
-- 5. pendanaan_jurnal_detail
-- ============================================================
CREATE TABLE IF NOT EXISTS `pendanaan_jurnal_detail` (
  `detailID`   INT(11)       NOT NULL AUTO_INCREMENT,
  `jurnalID`   INT(11)       NOT NULL,
  `no_akaun`   VARCHAR(50)   DEFAULT NULL,
  `nama_akaun` VARCHAR(100)  DEFAULT NULL,
  `debit`      DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `kredit`     DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` VARCHAR(255)  DEFAULT NULL,
  PRIMARY KEY (`detailID`),
  KEY `idx_jurnalID` (`jurnalID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ============================================================
-- 6. ALTER produk_usaha
--    Tambah: barcode, ledger_code, ledger_name,
--            harga_anggota, harga_non_anggota
-- ============================================================
ALTER TABLE `produk_usaha`
  ADD COLUMN `barcode`           VARCHAR(100)  DEFAULT NULL         AFTER `sku`,
  ADD COLUMN `ledger_code`       VARCHAR(50)   DEFAULT NULL         AFTER `barcode`,
  ADD COLUMN `ledger_name`       VARCHAR(100)  DEFAULT NULL         AFTER `ledger_code`,
  ADD COLUMN `harga_anggota`     DECIMAL(15,2) NOT NULL DEFAULT '0' AFTER `harga_jual`,
  ADD COLUMN `harga_non_anggota` DECIMAL(15,2) NOT NULL DEFAULT '0' AFTER `harga_anggota`;


-- ============================================================
-- 7. ALTER pos_order
--    Tambah: member_type, member_no
-- ============================================================
ALTER TABLE `pos_order`
  ADD COLUMN `member_type` VARCHAR(20) NOT NULL DEFAULT 'non_anggota' AFTER `customerPhone`,
  ADD COLUMN `member_no`   VARCHAR(50)          DEFAULT NULL          AFTER `member_type`;


-- ============================================================
-- 8. ALTER usaha
--    Tambah: jenis_pemilik, nama_pemilik, no_hp_pemilik
-- ============================================================
ALTER TABLE `usaha`
  ADD COLUMN `jenis_pemilik` VARCHAR(20)  NOT NULL DEFAULT 'anggota' AFTER `memberID`,
  ADD COLUMN `nama_pemilik`  VARCHAR(100)          DEFAULT NULL      AFTER `jenis_pemilik`,
  ADD COLUMN `no_hp_pemilik` VARCHAR(30)           DEFAULT NULL      AFTER `nama_pemilik`;
