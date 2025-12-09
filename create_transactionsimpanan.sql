-- Tabel untuk transaksi setor dan tarik simpanan

CREATE TABLE transactionsimpanan (
    ID BIGINT AUTO_INCREMENT PRIMARY KEY,
    TellerID VARCHAR(50) NOT NULL COMMENT 'ID Kasir/Teller',
    TellerName VARCHAR(100) NOT NULL COMMENT 'Nama Kasir/Teller',
    SaldoKas DECIMAL(18,2) NOT NULL DEFAULT 0 COMMENT 'Saldo Kas Teller',

    UserID VARCHAR(50) NOT NULL COMMENT 'ID Anggota',
    NamaAnggota VARCHAR(100) NOT NULL COMMENT 'Nama Anggota',
    NoAnggota VARCHAR(50) NULL COMMENT 'No. Anggota',
    NIK VARCHAR(20) NULL COMMENT 'NIK Anggota',

    AccountNumber VARCHAR(50) NOT NULL COMMENT 'No. Rekening Simpanan',
    Code_simpanan VARCHAR(20) NOT NULL COMMENT 'Kode Jenis Simpanan',
    NamaAkun VARCHAR(100) NULL COMMENT 'Nama Akun Simpanan',
    SaldoSebelum DECIMAL(18,2) NOT NULL DEFAULT 0 COMMENT 'Saldo Sebelum Transaksi',

    NamaCabang VARCHAR(100) NULL COMMENT 'Nama Cabang',
    GLRAK VARCHAR(100) NULL COMMENT 'GL RAK',

    TanggalTransaksi DATETIME NOT NULL COMMENT 'Tanggal Transaksi',
    JenisTransaksi VARCHAR(10) NOT NULL COMMENT 'SETOR atau TARIK',
    Nominal DECIMAL(18,2) NOT NULL DEFAULT 0 COMMENT 'Nominal Transaksi',
    SaldoSesudah DECIMAL(18,2) NOT NULL DEFAULT 0 COMMENT 'Saldo Sesudah Transaksi',

    Referensi VARCHAR(100) NULL COMMENT 'Referensi Transaksi',
    NoJurnal VARCHAR(50) NULL COMMENT 'No. Jurnal',
    Keterangan TEXT NULL COMMENT 'Keterangan Transaksi',

    Status TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = Batal, 1 = Berhasil',
    CreatedBy VARCHAR(50) NULL,
    CreatedDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UpdatedBy VARCHAR(50) NULL,
    UpdatedDate TIMESTAMP NULL,

    INDEX idx_account (AccountNumber),
    INDEX idx_user (UserID),
    INDEX idx_tanggal (TanggalTransaksi),
    INDEX idx_jenis (JenisTransaksi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel transaksi setor dan tarik simpanan';
