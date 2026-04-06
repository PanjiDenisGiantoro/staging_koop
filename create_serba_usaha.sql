-- ============================================================
-- Modul Serba Usaha - iKOOP.com.my
-- Tanggal: 2026-04-05
-- ============================================================

-- 1. Tabel Usaha (Usaha milik anggota)
CREATE TABLE IF NOT EXISTS `usaha` (
  `usahaID`     INT(11)       NOT NULL AUTO_INCREMENT,
  `memberID`    VARCHAR(20)   NOT NULL,
  `nama_usaha`  VARCHAR(100)  NOT NULL,
  `kategori`    VARCHAR(50)   DEFAULT '',
  `deskripsi`   TEXT,
  `alamat`      TEXT,
  `no_telefon`  VARCHAR(20)   DEFAULT '',
  `status`      TINYINT(1)    DEFAULT 0 COMMENT '0=Pending, 1=Aktif, 2=Tidak Aktif',
  `createdDate` DATETIME      DEFAULT NULL,
  `createdBy`   VARCHAR(50)   DEFAULT '',
  `updatedDate` DATETIME      DEFAULT NULL,
  `updatedBy`   VARCHAR(50)   DEFAULT '',
  PRIMARY KEY (`usahaID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Tabel Produk per Usaha
CREATE TABLE IF NOT EXISTS `produk_usaha` (
  `produkID`    INT(11)         NOT NULL AUTO_INCREMENT,
  `usahaID`     INT(11)         NOT NULL,
  `nama_produk` VARCHAR(100)    NOT NULL,
  `sku`         VARCHAR(50)     DEFAULT '',
  `harga_jual`  DECIMAL(15,2)   DEFAULT 0.00,
  `harga_beli`  DECIMAL(15,2)   DEFAULT 0.00,
  `kategori`    VARCHAR(50)     DEFAULT '',
  `deskripsi`   TEXT,
  `status`      TINYINT(1)      DEFAULT 1 COMMENT '1=Aktif, 0=Tidak Aktif',
  `createdDate` DATETIME        DEFAULT NULL,
  `createdBy`   VARCHAR(50)     DEFAULT '',
  `updatedDate` DATETIME        DEFAULT NULL,
  `updatedBy`   VARCHAR(50)     DEFAULT '',
  PRIMARY KEY (`produkID`),
  KEY `usahaID` (`usahaID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3. Tabel Pergerakan Stok per Produk
CREATE TABLE IF NOT EXISTS `stok_usaha` (
  `stokID`      INT(11)         NOT NULL AUTO_INCREMENT,
  `produkID`    INT(11)         NOT NULL,
  `usahaID`     INT(11)         NOT NULL,
  `jenis`       ENUM('masuk','keluar') NOT NULL DEFAULT 'masuk',
  `qty`         DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `stok_akhir`  DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `harga`       DECIMAL(15,2)   DEFAULT 0.00,
  `keterangan`  VARCHAR(200)    DEFAULT '',
  `tarikh`      DATE            DEFAULT NULL,
  `createdDate` DATETIME        DEFAULT NULL,
  `createdBy`   VARCHAR(50)     DEFAULT '',
  PRIMARY KEY (`stokID`),
  KEY `produkID` (`produkID`),
  KEY `usahaID` (`usahaID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
