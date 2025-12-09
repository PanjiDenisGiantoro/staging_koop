<?php
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

// Ambil data Teller yang login
$TellerID = get_session("Cookie_userID");
$TellerName = get_session("Cookie_userName");

// Validasi TellerID tidak boleh kosong
if (empty($TellerID)) {
    $TellerID = get_session("Cookie_UserID"); // Coba dengan huruf kapital
}
if (empty($TellerID)) {
    echo "<script>alert('Session Teller tidak ditemukan! Silakan login ulang.'); window.location.href='index.php';</script>";
    exit;
}

// Ambil saldo kas teller (contoh query, sesuaikan dengan struktur database Anda)
$sqlSaldoKas = "SELECT SaldoKas FROM users WHERE UserID = " . tosql($TellerID, "Text");
$rsSaldoKas = $conn->Execute($sqlSaldoKas);
$SaldoKas = ($rsSaldoKas && !$rsSaldoKas->EOF && $rsSaldoKas->fields['SaldoKas'] != null) ? $rsSaldoKas->fields['SaldoKas'] : 0;

// Inisialisasi variabel
$UserID = '';
$NamaAnggota = '';
$NoAnggota = '';
$NIK = '';
$AccountNumber = '';
$NamaAkun = '';
$SaldoRekening = 0;
$Code_simpanan = '';
$NamaCabang = 'UNIT SIMPAN PINJAM (USP)';
$GLRAK = 'RAK PUSAT';

// Ambil semua rekening aktif untuk dropdown
$sqlAllAcc = "
  SELECT
      a.userID,
      a.loginID,
      a.name,
      b.memberID,
      b.newIC,
      b.mobileNo,
      c.id AS depositorID,
      c.accountNumber,
      c.Code_simpanan,
      c.balance,
      c.nominal_simpanan,
      c.status AS depositor_status,
      g.name as NamaAkun
  FROM users a
  LEFT JOIN userdetails b  ON a.userID COLLATE latin1_general_ci  = b.userID COLLATE latin1_general_ci
  LEFT JOIN depositoracc c ON a.userID COLLATE latin1_general_ci  = c.userID COLLATE latin1_general_ci
  LEFT JOIN general g ON CONVERT(c.Code_simpanan USING utf8) = CONVERT(g.ID USING utf8)
  WHERE c.status = 1
  ORDER BY c.accountNumber ASC";
$rsAllAcc = $conn->Execute($sqlAllAcc);
$allAccounts = array();
if ($rsAllAcc) {
    while (!$rsAllAcc->EOF) {
        $allAccounts[] = array(
            'AccountNumber' => $rsAllAcc->fields['accountNumber'],
            'NamaAnggota' => $rsAllAcc->fields['name'],
            'MemberID' => $rsAllAcc->fields['memberID'],
            'NewIC' => $rsAllAcc->fields['newIC'],
            'NamaAkun' => isset($rsAllAcc->fields['NamaAkun']) ? $rsAllAcc->fields['NamaAkun'] : '',
            'Balance' => $rsAllAcc->fields['balance']
        );
        $rsAllAcc->MoveNext();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action == 'search') {
        // Cari rekening berdasarkan AccountNumber
        $AccountNumber = isset($_POST['AccountNumber']) ? trim($_POST['AccountNumber']) : '';

        if ($AccountNumber != '') {
            $sqlAcc = "SELECT d.*,
                              u.Name as NamaAnggota,
                              u.loginID as NoAnggota,
                              d.accountNumber,
                              b.newIC as NIK,
                              b.memberID,
                              g.name as NamaAkun
                       FROM depositoracc d
                       LEFT JOIN users u ON CONVERT(d.UserID USING utf8) = CONVERT(u.UserID USING utf8)
                       LEFT JOIN userdetails b ON CONVERT(u.userID USING utf8) = CONVERT(b.userID USING utf8)
                       LEFT JOIN general g ON CONVERT(d.Code_simpanan USING utf8) = CONVERT(g.ID USING utf8)
                       WHERE d.AccountNumber = " . tosql($AccountNumber, "Text") . " and d.Status = 1 ";

            $rsAcc = $conn->Execute($sqlAcc);

            if ($rsAcc && !$rsAcc->EOF) {
                $UserID = $rsAcc->fields['UserID'];
                $NamaAnggota = $rsAcc->fields['NamaAnggota'];
                $NoAnggota = isset($rsAcc->fields['NoAnggota']) ? $rsAcc->fields['NoAnggota'] : (isset($rsAcc->fields['memberID']) ? $rsAcc->fields['memberID'] : '');
                $NIK = isset($rsAcc->fields['NIK']) ? $rsAcc->fields['NIK'] : '';
                $AccountNumber = $rsAcc->fields['AccountNumber'];
                $NamaAkun = isset($rsAcc->fields['NamaAkun']) ? $rsAcc->fields['NamaAkun'] : '';
                $SaldoRekening = $rsAcc->fields['Balance'];
                $Code_simpanan = $rsAcc->fields['Code_simpanan'];
            } else {
                echo "<script>alert('Rekening tidak ditemukan atau tidak aktif!');</script>";
            }
        }
    } elseif ($action == 'transaction') {
        // Proses transaksi setor/tarik
        $AccountNumber = isset($_POST['AccountNumber']) ? trim($_POST['AccountNumber']) : '';
        $JenisTransaksi = isset($_POST['JenisTransaksi']) ? trim($_POST['JenisTransaksi']) : '';
        $Nominal = isset($_POST['Nominal']) ? floatval($_POST['Nominal']) : 0;
        $TanggalTransaksi = isset($_POST['TanggalTransaksi']) ? trim($_POST['TanggalTransaksi']) : date('Y-m-d');
        $Referensi = isset($_POST['Referensi']) ? trim($_POST['Referensi']) : '';
        $Keterangan = isset($_POST['Keterangan']) ? trim($_POST['Keterangan']) : '';
        $Code_simpanan = isset($_POST['Code_simpanan']) ? trim($_POST['Code_simpanan']) : '';

        if ($AccountNumber != '' && $JenisTransaksi != '' && $Nominal > 0) {
            // Ambil data rekening
            $sqlAcc = "
SELECT d.*,
       u.Name as NamaAnggota,
       u.loginID as NoAnggota,
       d.accountNumber,
       b.newIC as NIK,
       b.memberID,
       g.name as NamaAkun
FROM depositoracc d
LEFT JOIN users u ON CONVERT(d.UserID USING utf8) = CONVERT(u.UserID USING utf8)
LEFT JOIN userdetails b ON CONVERT(u.userID USING utf8) = CONVERT(b.userID USING utf8)
LEFT JOIN general g ON CONVERT(d.Code_simpanan USING utf8) = CONVERT(g.ID USING utf8)
WHERE d.AccountNumber = " . tosql($AccountNumber, "Text") . " and d.Status = 1 ";

            $rsAcc = $conn->Execute($sqlAcc);

            if (!$rsAcc) {
                echo "<script>alert('Error query: " . addslashes($conn->ErrorMsg()) . "'); window.history.back();</script>";
                exit;
            }

            if ($rsAcc && !$rsAcc->EOF) {
                $UserID = $rsAcc->fields['UserID'];
                $NamaAnggota = $rsAcc->fields['NamaAnggota'];
                $NoAnggota = isset($rsAcc->fields['NoAnggota']) && $rsAcc->fields['NoAnggota'] != '' ? $rsAcc->fields['NoAnggota'] : (isset($rsAcc->fields['memberID']) && $rsAcc->fields['memberID'] != '' ? $rsAcc->fields['memberID'] : '-');
                $NIK = isset($rsAcc->fields['NIK']) && $rsAcc->fields['NIK'] != '' ? $rsAcc->fields['NIK'] : '-';
                $NamaAkun = isset($rsAcc->fields['NamaAkun']) && $rsAcc->fields['NamaAkun'] != '' ? $rsAcc->fields['NamaAkun'] : '-';
                $SaldoSebelum = $rsAcc->fields['Balance'];
                $Code_simpanan_db = $rsAcc->fields['Code_simpanan'];

                // Validasi: Simpanan modal tidak boleh ditarik
                // Asumsikan Code_simpanan untuk simpanan modal adalah 'MODAL' atau sejenisnya
                // Sesuaikan dengan data di database Anda
                if ($JenisTransaksi == 'TARIK') {
                    // Cek apakah simpanan modal
                    $sqlGeneral = "SELECT name FROM general WHERE ID = " . tosql($Code_simpanan_db, "Text");
                    $rsGeneral = $conn->Execute($sqlGeneral);
                    if ($rsGeneral && !$rsGeneral->EOF) {
                        $namaSimpanan = strtolower($rsGeneral->fields['name']);
                        if (strpos($namaSimpanan, 'modal') !== false || strpos($namaSimpanan, 'pokok') !== false) {
                            echo "<script>alert('Simpanan modal/pokok tidak boleh ditarik!'); window.history.back();</script>";
                            exit;
                        }
                    }

                    // Validasi saldo cukup
                    if ($Nominal > $SaldoSebelum) {
                        echo "<script>alert('Saldo tidak mencukupi untuk penarikan!'); window.history.back();</script>";
                        exit;
                    }
                }

                // Hitung saldo sesudah
                if ($JenisTransaksi == 'SETOR') {
                    $SaldoSesudah = $SaldoSebelum + $Nominal;
                } else {
                    $SaldoSesudah = $SaldoSebelum - $Nominal;
                }

                // Generate No. Jurnal otomatis
                $NoJurnal = 'JRN' . date('YmdHis') . rand(1000, 9999);

                // Validasi final sebelum INSERT
                if (empty($TellerID)) {
                    echo "<script>alert('TellerID tidak boleh kosong!'); window.history.back();</script>";
                    exit;
                }
                if (empty($UserID)) {
                    echo "<script>alert('UserID tidak boleh kosong!'); window.history.back();</script>";
                    exit;
                }

                // Insert ke transactionsimpanan (dengan semua kolom lengkap)
                $sqlInsert = "INSERT INTO transactionsimpanan
                    (TellerID, TellerName, SaldoKas, UserID, NamaAnggota,
                     AccountNumber, Code_simpanan, NamaAkun, NIK, NamaCabang, GLRAK,
                     JenisTransaksi, Nominal, SaldoSebelum, SaldoSesudah, TanggalTransaksi,
                     NoJurnal, Referensi, Keterangan, Status, CreatedBy, CreatedDate)
                VALUES (
                    " . tosql($TellerID, "Text") . ",
                    " . tosql($TellerName, "Text") . ",
                    " . $SaldoKas . ",
                    " . tosql($UserID, "Text") . ",
                    " . tosql($NamaAnggota, "Text") . ",
                    " . tosql($AccountNumber, "Text") . ",
                    " . tosql($Code_simpanan_db, "Text") . ",
                    " . tosql($NamaAkun, "Text") . ",
                    " . tosql($NIK, "Text") . ",
                    " . tosql($NamaCabang, "Text") . ",
                    " . tosql($GLRAK, "Text") . ",
                    " . tosql($JenisTransaksi, "Text") . ",
                    " . $Nominal . ",
                    " . $SaldoSebelum . ",
                    " . $SaldoSesudah . ",
                    " . tosql($TanggalTransaksi, "Text") . ",
                    " . tosql($NoJurnal, "Text") . ",
                    " . tosql($Referensi, "Text") . ",
                    " . tosql($Keterangan, "Text") . ",
                    1,
                    " . tosql($TellerID, "Text") . ",
                    NOW()
                )";

                $resultInsert = $conn->Execute($sqlInsert);

                if (!$resultInsert) {
                    // Tampilkan error jika INSERT gagal
                    $errorMsg = $conn->ErrorMsg();
                    echo "<script>alert('Error saat menyimpan transaksi:\\n" . addslashes($errorMsg) . "\\n\\nQuery: " . addslashes(substr($sqlInsert, 0, 200)) . "...'); window.history.back();</script>";
                    exit;
                }

                // Update saldo di depositoracc
                $sqlUpdate = "UPDATE depositoracc
                             SET Balance = " . $SaldoSesudah . ",
                                 UpdateUserID = " . tosql($TellerID, "Text") . ",
                                 DateUpdateUserID = NOW()
                             WHERE AccountNumber = " . tosql($AccountNumber, "Text");
                $resultUpdate = $conn->Execute($sqlUpdate);

                if (!$resultUpdate) {
                    // Tampilkan error jika UPDATE gagal
                    $errorMsg = $conn->ErrorMsg();
                    echo "<script>alert('Error saat update saldo:\\n" . addslashes($errorMsg) . "'); window.history.back();</script>";
                    exit;
                }

                echo "<script>alert('Transaksi " . $JenisTransaksi . " berhasil!\\nNo. Jurnal: " . $NoJurnal . "');
                      window.location.href='?vw=transactionSimpanan&mn=903';</script>";
                exit;
            } else {
                echo "<script>alert('Rekening tidak ditemukan!'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Semua field wajib diisi!\\nAccountNumber: " . $AccountNumber . "\\nJenisTransaksi: " . $JenisTransaksi . "\\nNominal: " . $Nominal . "'); window.history.back();</script>";
        }
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container mt-4">
    <h3>Transaksi Setor & Tarik Simpanan</h3>

    <!-- Informasi Kasir/Teller -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Informasi Kasir / Teller</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID Kasir:</strong> <?php echo htmlspecialchars($TellerID); ?></p>
                    <p><strong>Nama Kasir:</strong> <?php echo htmlspecialchars($TellerName); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Saldo Kas:</strong> <span class="text-success">Rp <?php echo number_format($SaldoKas, 0, ',', '.'); ?></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Cari Rekening -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Cari Rekening</h5>
            <form method="post" action="">
                <input type="hidden" name="action" value="search">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Pilih Rekening</label>
                        <select name="AccountNumber" id="accountSelect" class="form-control" required>
                            <option value="">-- Pilih Rekening --</option>
                            <?php foreach ($allAccounts as $acc): ?>
                                <option value="<?php echo htmlspecialchars($acc['AccountNumber']); ?>"
                                        <?php echo ($AccountNumber == $acc['AccountNumber']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($acc['AccountNumber']); ?> -
                                    <?php echo htmlspecialchars($acc['NamaAnggota']); ?>
                                    (<?php echo htmlspecialchars($acc['MemberID']); ?>) -
                                    <?php echo htmlspecialchars($acc['NamaAkun']); ?> -
                                    Saldo: Rp <?php echo number_format($acc['Balance'], 0, ',', '.'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Pilih Rekening</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($AccountNumber != '' && $UserID != ''): ?>
    <!-- Informasi Anggota & Rekening -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Informasi Anggota</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($NamaAnggota); ?></p>
                    <p><strong>No. Anggota:</strong> <?php echo htmlspecialchars($NoAnggota); ?></p>
                    <p><strong>NIK:</strong> <?php echo htmlspecialchars($NIK); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Nama Akun:</strong> <?php echo htmlspecialchars($NamaAkun); ?></p>
                    <p><strong>No. Rekening:</strong> <?php echo htmlspecialchars($AccountNumber); ?></p>
                    <p><strong>Saldo:</strong> <span class="text-primary fw-bold">Rp <?php echo number_format($SaldoRekening, 0, ',', '.'); ?></span></p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <p><strong>Nama Cabang:</strong> <?php echo htmlspecialchars($NamaCabang); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>GL RAK:</strong> <?php echo htmlspecialchars($GLRAK); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Transaksi -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Informasi Transaksi</h5>
            <form method="post" action="" onsubmit="return confirm('Apakah Anda yakin ingin memproses transaksi ini?')">
                <input type="hidden" name="action" value="transaction">
                <input type="hidden" name="AccountNumber" value="<?php echo htmlspecialchars($AccountNumber); ?>">
                <input type="hidden" name="Code_simpanan" value="<?php echo htmlspecialchars($Code_simpanan); ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="TanggalTransaksi" class="form-control"
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Jenis Transaksi <span class="text-danger">*</span></label>
                        <select name="JenisTransaksi" class="form-control" required id="jenisTransaksi">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="SETOR">Setor</option>
                            <option value="TARIK">Tarik</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Nominal <span class="text-danger">*</span></label>
                    <input type="number" name="Nominal" id="nominalInput" class="form-control"
                           placeholder="Masukkan nominal" step="0.01" min="1" required>
                    <small class="text-muted" id="warningModal" style="display:none;">
                        <span class="text-danger">⚠ Simpanan modal tidak boleh ditarik</span>
                    </small>
                </div>

                <div class="mb-3">
                    <label>Referensi <span class="text-danger">*</span></label>
                    <input type="text" name="Referensi" id="referensiInput" class="form-control"
                           placeholder="Masukkan referensi transaksi" required>
                </div>

                <div class="mb-3">
                    <label>Keterangan <span class="text-danger">*</span></label>
                    <textarea name="Keterangan" id="keteranganInput" class="form-control" rows="3"
                              placeholder="Keterangan akan terisi otomatis" ></textarea>
                    <small class="text-muted">Keterangan akan terisi otomatis, namun Anda dapat mengeditnya jika diperlukan</small>
                </div>

                <button type="submit" class="btn btn-success">Proses Transaksi</button>
                <a href="?vw=transactionSimpanan&mn=903" class="btn btn-secondary">Reset</a>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Initialize Select2 untuk dropdown rekening
$(document).ready(function() {
    // Initialize Select2 with search
    $('#accountSelect').select2({
        placeholder: '-- Pilih atau cari rekening --',
        allowClear: true,
        width: '100%'
    });

    // Cek apakah simpanan modal
    var namaAkun = "<?php echo strtolower($NamaAkun); ?>";
    var namaAnggota = "<?php echo htmlspecialchars($NamaAnggota); ?>";
    var accountNumber = "<?php echo htmlspecialchars($AccountNumber); ?>";
    var namaAkunLengkap = "<?php echo htmlspecialchars($NamaAkun); ?>";

    // Fungsi untuk generate keterangan otomatis
    function generateKeterangan() {
        var jenisTransaksi = $('#jenisTransaksi').val();
        var nominal = $('#nominalInput').val();

        if (jenisTransaksi && nominal && parseFloat(nominal) > 0) {
            var nominalFormatted = 'Rp ' + parseFloat(nominal).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });

            var keterangan = jenisTransaksi + ' TUNAI - ' +
                           'Rek. ' + accountNumber +
                           ' a.n. ' + namaAnggota +
                           ' (' + namaAkunLengkap + ') - ' +
                           nominalFormatted;

            $('#keteranganInput').val(keterangan);
        } else {
            $('#keteranganInput').val('');
        }
    }

    $('#jenisTransaksi').change(function() {
        if ($(this).val() == 'TARIK' && namaAkun.indexOf('modal') !== -1) {
            $('#warningModal').show();
        } else {
            $('#warningModal').hide();
        }

        // Generate keterangan otomatis
        generateKeterangan();
    });

    // Generate keterangan saat nominal berubah
    $('#nominalInput').on('input change', function() {
        generateKeterangan();
    });
});
</script>

<?php include("footer.php"); ?>
