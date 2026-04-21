-- ============================================================
-- Alter Modul Pendanaan - Jurnal Akuntansi (Double Entry)
-- iKOOP.com.my | 2026-04-21
-- ============================================================

-- Header jurnal (satu baris per transaksi)
CREATE TABLE IF NOT EXISTS `pendanaan_jurnal` (
  `jurnalID`      INT(11)       NOT NULL AUTO_INCREMENT,
  `no_jurnal`     VARCHAR(25)   NOT NULL COMMENT 'Format: JN-PDN-YYYYMM-0001',
  `jenis`         VARCHAR(30)   NOT NULL
                  COMMENT 'TAMBAH_POOL | DISTRIBUSI | BAYAR_CICILAN | BAYAR_DENDA | ADJUSTMENT',
  `ref_tabel`     VARCHAR(50)   DEFAULT '',
  `ref_id`        INT(11)       DEFAULT 0,
  `keterangan`    VARCHAR(255)  DEFAULT '',
  `tarikh`        DATE          NOT NULL,
  `total_debit`   DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `total_kredit`  DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `createdDate`   DATETIME      DEFAULT NULL,
  `createdBy`     VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`jurnalID`),
  UNIQUE KEY `no_jurnal` (`no_jurnal`),
  KEY `tarikh` (`tarikh`),
  KEY `jenis` (`jenis`),
  KEY `ref_id` (`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Header jurnal akuntansi pendanaan';

-- Baris jurnal (debit / kredit per akaun)
CREATE TABLE IF NOT EXISTS `pendanaan_jurnal_detail` (
  `detailID`      INT(11)       NOT NULL AUTO_INCREMENT,
  `jurnalID`      INT(11)       NOT NULL,
  `no_akaun`      VARCHAR(20)   NOT NULL COMMENT 'Kode akun pendanaan',
  `nama_akaun`    VARCHAR(100)  NOT NULL,
  `debit`         DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `kredit`        DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `keterangan`    VARCHAR(255)  DEFAULT '',
  PRIMARY KEY (`detailID`),
  KEY `jurnalID` (`jurnalID`),
  KEY `no_akaun` (`no_akaun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Baris jurnal akuntansi pendanaan';

-- ============================================================
-- Referensi Akun Pendanaan (tidak perlu tabel, dokumentasi saja)
-- 1-1001  Kas Pool Pendanaan            (Aktiva Lancar)
-- 1-2001  Piutang Pendanaan Usaha       (Aktiva Lancar)
-- 3-1001  Modal / Dana Pendanaan        (Ekuitas)
-- 4-1001  Pendapatan Bunga Pendanaan    (Pendapatan)
-- 4-1002  Pendapatan Denda Pendanaan    (Pendapatan)
-- ============================================================
