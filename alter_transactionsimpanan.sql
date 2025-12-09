-- ALTER TABLE untuk menambahkan kolom-kolom penting ke transactionsimpanan

ALTER TABLE transactionsimpanan
ADD COLUMN AccountNumber VARCHAR(50) NULL COMMENT 'No. Rekening Simpanan' AFTER NamaAnggota,
ADD COLUMN Code_simpanan VARCHAR(20) NULL COMMENT 'Kode Jenis Simpanan' AFTER AccountNumber,
ADD COLUMN NamaAkun VARCHAR(100) NULL COMMENT 'Nama Akun Simpanan' AFTER Code_simpanan,
ADD COLUMN NIK VARCHAR(20) NULL COMMENT 'NIK Anggota' AFTER NamaAkun,
ADD COLUMN NamaCabang VARCHAR(100) NULL COMMENT 'Nama Cabang' AFTER NIK,
ADD COLUMN GLRAK VARCHAR(100) NULL COMMENT 'GL RAK' AFTER NamaCabang,
ADD COLUMN JenisTransaksi VARCHAR(10) NULL COMMENT 'SETOR atau TARIK' AFTER GLRAK,
ADD COLUMN Nominal DECIMAL(18,2) DEFAULT 0 COMMENT 'Nominal Transaksi' AFTER JenisTransaksi,
ADD COLUMN SaldoSebelum DECIMAL(18,2) DEFAULT 0 COMMENT 'Saldo Sebelum Transaksi' AFTER Nominal,
ADD COLUMN SaldoSesudah DECIMAL(18,2) DEFAULT 0 COMMENT 'Saldo Sesudah Transaksi' AFTER SaldoSebelum,
ADD COLUMN TanggalTransaksi DATETIME NULL COMMENT 'Tanggal Transaksi' AFTER SaldoSesudah,
ADD COLUMN NoJurnal VARCHAR(50) NULL COMMENT 'No. Jurnal' AFTER TanggalTransaksi,
ADD COLUMN Referensi VARCHAR(100) NULL COMMENT 'Referensi Transaksi' AFTER NoJurnal,
ADD COLUMN Keterangan TEXT NULL COMMENT 'Keterangan Transaksi' AFTER Referensi,
ADD COLUMN Status TINYINT(1) DEFAULT 1 COMMENT '0 = Batal, 1 = Berhasil' AFTER Keterangan,
ADD COLUMN CreatedBy VARCHAR(50) NULL AFTER Status,
ADD COLUMN CreatedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER CreatedBy,
ADD COLUMN UpdatedBy VARCHAR(50) NULL AFTER CreatedDate,
ADD COLUMN UpdatedDate TIMESTAMP NULL AFTER UpdatedBy;

-- Tambahkan index untuk performa
ALTER TABLE transactionsimpanan
ADD INDEX idx_account (AccountNumber),
ADD INDEX idx_user (UserID),
ADD INDEX idx_tanggal (TanggalTransaksi),
ADD INDEX idx_jenis (JenisTransaksi);
