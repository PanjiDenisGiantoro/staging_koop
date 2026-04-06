-- ============================================================
-- Modul POS - Serba Usaha - iKOOP.com.my
-- ============================================================

CREATE TABLE IF NOT EXISTS `pos_order` (
  `orderID`       INT(11)       NOT NULL AUTO_INCREMENT,
  `orderNo`       VARCHAR(20)   NOT NULL,
  `customerName`  VARCHAR(100)  NOT NULL,
  `customerPhone` VARCHAR(20)   DEFAULT '',
  `catatan`       TEXT,
  `totalAmt`      DECIMAL(15,2) DEFAULT 0.00,
  `status`        TINYINT(1)    DEFAULT 0 COMMENT '0=Pending,1=Confirmed,2=Selesai,3=Batal',
  `createdDate`   DATETIME      DEFAULT NULL,
  `createdBy`     VARCHAR(50)   DEFAULT 'guest',
  PRIMARY KEY (`orderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pos_order_detail` (
  `detailID`    INT(11)       NOT NULL AUTO_INCREMENT,
  `orderID`     INT(11)       NOT NULL,
  `produkID`    INT(11)       NOT NULL,
  `usahaID`     INT(11)       NOT NULL,
  `nama_produk` VARCHAR(100)  NOT NULL,
  `qty`         DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `harga`       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `subtotal`    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`detailID`),
  KEY `orderID` (`orderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
