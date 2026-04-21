-- ============================================================
-- Modul Pendanaan Usaha - iKOOP.com.my
-- Tanggal: 2026-04-20
-- Pendanaan untuk Serba Usaha anggota koperasi
-- ============================================================

-- 1. Pool Dana (saldo terpusat pendanaan usaha)
CREATE TABLE IF NOT EXISTS `pendanaan_pool` (
  `poolID`        INT(11)       NOT NULL AUTO_INCREMENT,
  `saldo`         DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `total_masuk`   DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `total_keluar`  DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `updatedDate`   DATETIME      DEFAULT NULL,
  `updatedBy`     VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`poolID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Seed baris tunggal pool
INSERT INTO `pendanaan_pool` (`poolID`, `saldo`, `total_masuk`, `total_keluar`)
VALUES (1, 0.00, 0.00, 0.00)
ON DUPLICATE KEY UPDATE `poolID` = `poolID`;

-- 2. Transaksi Pool Dana
CREATE TABLE IF NOT EXISTS `pendanaan_pool_trx` (
  `trxID`         INT(11)       NOT NULL AUTO_INCREMENT,
  `jenis`         ENUM('MASUK','KELUAR') NOT NULL DEFAULT 'MASUK',
  `ref_tabel`     VARCHAR(50)   DEFAULT '',
  `ref_id`        INT(11)       DEFAULT NULL,
  `nominal`       DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `saldo_sesudah` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `keterangan`    VARCHAR(255)  DEFAULT '',
  `createdDate`   DATETIME      DEFAULT NULL,
  `createdBy`     VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`trxID`),
  KEY `ref_id` (`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3. Pengajuan Pendanaan oleh Usaha
CREATE TABLE IF NOT EXISTS `pendanaan_pengajuan` (
  `pengajuanID`   INT(11)       NOT NULL AUTO_INCREMENT,
  `no_pengajuan`  VARCHAR(30)   NOT NULL UNIQUE,
  `usahaID`       INT(11)       NOT NULL,
  `memberID`      VARCHAR(20)   NOT NULL,
  `nominal`       DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `tujuan`        TEXT,
  `tenor`         INT(3)        NOT NULL DEFAULT 12 COMMENT 'dalam bulan',
  `dokumen`       VARCHAR(255)  DEFAULT '',
  `status`        TINYINT(1)    NOT NULL DEFAULT 0
                  COMMENT '0=Draft,1=Pending,2=Disetujui,3=Ditolak',
  `alasan_tolak`  TEXT,
  `tgl_pengajuan` DATE          DEFAULT NULL,
  `tgl_diproses`  DATETIME      DEFAULT NULL,
  `createdDate`   DATETIME      DEFAULT NULL,
  `createdBy`     VARCHAR(50)   DEFAULT '',
  `updatedDate`   DATETIME      DEFAULT NULL,
  `updatedBy`     VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`pengajuanID`),
  KEY `usahaID` (`usahaID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 4. Approval Pengajuan (Admin -> Manager)
CREATE TABLE IF NOT EXISTS `pendanaan_approval` (
  `approvalID`    INT(11)       NOT NULL AUTO_INCREMENT,
  `pengajuanID`   INT(11)       NOT NULL,
  `level_approval` ENUM('ADMIN','MANAGER') NOT NULL DEFAULT 'ADMIN',
  `approverID`    VARCHAR(50)   DEFAULT '',
  `status`        TINYINT(1)    NOT NULL DEFAULT 0
                  COMMENT '0=Pending,1=Disetujui,2=Ditolak',
  `catatan`       TEXT,
  `tgl_approval`  DATETIME      DEFAULT NULL,
  `createdDate`   DATETIME      DEFAULT NULL,
  PRIMARY KEY (`approvalID`),
  KEY `pengajuanID` (`pengajuanID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 5. Distribusi Dana ke Usaha (setelah disetujui)
CREATE TABLE IF NOT EXISTS `pendanaan_distribusi` (
  `distribusiID`  INT(11)       NOT NULL AUTO_INCREMENT,
  `no_distribusi` VARCHAR(30)   NOT NULL UNIQUE,
  `pengajuanID`   INT(11)       NOT NULL,
  `usahaID`       INT(11)       NOT NULL,
  `memberID`      VARCHAR(20)   NOT NULL,
  `nominal`       DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `bunga_per_thn` DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
  `tenor`         INT(3)        NOT NULL DEFAULT 12 COMMENT 'dalam bulan',
  `cicilan_per_bln` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `tgl_distribusi` DATE         DEFAULT NULL,
  `tgl_jatuh_tempo` DATE        DEFAULT NULL,
  `status`        TINYINT(1)    NOT NULL DEFAULT 1
                  COMMENT '1=Aktif,2=Lunas,3=Macet',
  `createdDate`   DATETIME      DEFAULT NULL,
  `createdBy`     VARCHAR(50)   DEFAULT '',
  `updatedDate`   DATETIME      DEFAULT NULL,
  `updatedBy`     VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`distribusiID`),
  KEY `pengajuanID` (`pengajuanID`),
  KEY `usahaID` (`usahaID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 6. Jadwal Cicilan per Distribusi
CREATE TABLE IF NOT EXISTS `pendanaan_cicilan` (
  `cicilanID`     INT(11)       NOT NULL AUTO_INCREMENT,
  `distribusiID`  INT(11)       NOT NULL,
  `angsuran_ke`   INT(3)        NOT NULL DEFAULT 1,
  `nominal_pokok` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `nominal_bunga` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `nominal_denda` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `total_tagihan` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `tgl_jatuh_tempo` DATE        DEFAULT NULL,
  `tgl_bayar`     DATE          DEFAULT NULL,
  `nominal_bayar` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `status`        TINYINT(1)    NOT NULL DEFAULT 0
                  COMMENT '0=Belum Bayar,1=Lunas,2=Telat,3=Macet',
  `createdDate`   DATETIME      DEFAULT NULL,
  `updatedDate`   DATETIME      DEFAULT NULL,
  `updatedBy`     VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`cicilanID`),
  KEY `distribusiID` (`distribusiID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 7. Pembayaran Cicilan
CREATE TABLE IF NOT EXISTS `pendanaan_bayar` (
  `bayarID`       INT(11)       NOT NULL AUTO_INCREMENT,
  `cicilanID`     INT(11)       NOT NULL,
  `distribusiID`  INT(11)       NOT NULL,
  `nominal`       DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `metode_bayar`  VARCHAR(50)   DEFAULT '',
  `no_referensi`  VARCHAR(100)  DEFAULT '',
  `bukti`         VARCHAR(255)  DEFAULT '',
  `tgl_bayar`     DATE          DEFAULT NULL,
  `keterangan`    VARCHAR(255)  DEFAULT '',
  `createdDate`   DATETIME      DEFAULT NULL,
  `createdBy`     VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`bayarID`),
  KEY `cicilanID` (`cicilanID`),
  KEY `distribusiID` (`distribusiID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
