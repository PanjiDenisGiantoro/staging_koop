-- ============================================================
-- Alter Modul Pendanaan - Tambah Fitur Kredit & Monitoring
-- iKOOP.com.my | 2026-04-20
-- ============================================================

-- Akun Kredit per Usaha (seperti kartu kredit)
CREATE TABLE IF NOT EXISTS `pendanaan_kredit` (
  `kreditID`        INT(11)       NOT NULL AUTO_INCREMENT,
  `no_akun`         VARCHAR(20)   NOT NULL UNIQUE COMMENT 'Format: KPD-0001-YYYY',
  `usahaID`         INT(11)       NOT NULL,
  `memberID`        VARCHAR(20)   NOT NULL,
  `limit_kredit`    DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `saldo_terpakai`  DECIMAL(18,2) NOT NULL DEFAULT 0.00
                    COMMENT 'Total outstanding aktif',
  `total_pinjaman`  DECIMAL(18,2) NOT NULL DEFAULT 0.00
                    COMMENT 'Akumulasi semua pinjaman sejak dibuka',
  `total_bayar`     DECIMAL(18,2) NOT NULL DEFAULT 0.00
                    COMMENT 'Akumulasi semua pembayaran pokok',
  `skor_kredit`     TINYINT(3)    NOT NULL DEFAULT 100
                    COMMENT 'Skor 0-100, berkurang jika telat',
  `status`          TINYINT(1)    NOT NULL DEFAULT 0
                    COMMENT '0=Pending,1=Aktif,2=Suspend,3=Nonaktif',
  `tgl_berlaku`     DATE          DEFAULT NULL,
  `tgl_kadaluarsa`  DATE          DEFAULT NULL,
  `catatan`         TEXT,
  `createdDate`     DATETIME      DEFAULT NULL,
  `createdBy`       VARCHAR(50)   DEFAULT '',
  `updatedDate`     DATETIME      DEFAULT NULL,
  `updatedBy`       VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`kreditID`),
  KEY `usahaID` (`usahaID`),
  KEY `memberID` (`memberID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Histori mutasi akun kredit
CREATE TABLE IF NOT EXISTS `pendanaan_kredit_trx` (
  `trxID`           INT(11)       NOT NULL AUTO_INCREMENT,
  `kreditID`        INT(11)       NOT NULL,
  `usahaID`         INT(11)       NOT NULL,
  `jenis`           ENUM('DEBIT','KREDIT') NOT NULL
                    COMMENT 'DEBIT=pakai limit, KREDIT=bayar/kembalikan',
  `ref_tabel`       VARCHAR(50)   DEFAULT '',
  `ref_id`          INT(11)       DEFAULT NULL,
  `nominal`         DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `saldo_terpakai`  DECIMAL(18,2) NOT NULL DEFAULT 0.00
                    COMMENT 'Saldo terpakai setelah transaksi ini',
  `keterangan`      VARCHAR(255)  DEFAULT '',
  `createdDate`     DATETIME      DEFAULT NULL,
  `createdBy`       VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`trxID`),
  KEY `kreditID` (`kreditID`),
  KEY `usahaID` (`usahaID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Notifikasi jatuh tempo cicilan
CREATE TABLE IF NOT EXISTS `pendanaan_notifikasi` (
  `notifID`         INT(11)       NOT NULL AUTO_INCREMENT,
  `usahaID`         INT(11)       NOT NULL,
  `memberID`        VARCHAR(20)   NOT NULL,
  `distribusiID`    INT(11)       DEFAULT NULL,
  `cicilanID`       INT(11)       DEFAULT NULL,
  `jenis`           ENUM('JATUH_TEMPO','TELAT','LUNAS','APPROVED','REJECTED') NOT NULL,
  `pesan`           TEXT,
  `dibaca`          TINYINT(1)    NOT NULL DEFAULT 0,
  `createdDate`     DATETIME      DEFAULT NULL,
  PRIMARY KEY (`notifID`),
  KEY `usahaID` (`usahaID`),
  KEY `memberID` (`memberID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
