<?php
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$TellerID = get_session("Cookie_userID");
$TellerName = get_session("Cookie_userName");

if (empty($TellerID)) {
    $TellerID = get_session("Cookie_UserID");
}
if (empty($TellerID)) {
    echo "<script>alert('Session Teller tidak ditemukan! Silakan login ulang.'); window.location.href='index.php';</script>";
    exit;
}

$UserID = '';
$NamaAnggota = '';
$NoAnggota = '';
$NIK = '';
$AccountNumber = '';
$NamaAkun = '';
$SaldoRekening = 0;
$Code_simpanan = '';
$GLBankCode = '';
$GLBankName = '';
$GLSimpananCode = '';
$GLSimpananName = '';
$HadSehari = 0;
$HadTransaksi = 0;
$JumlahMinimum = 0;
$accountList = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action == 'pilih_member') {
        $UserID = isset($_POST['UserID']) ? trim($_POST['UserID']) : '';
        $NamaAnggota = isset($_POST['loanName']) ? trim($_POST['loanName']) : '';

        if ($UserID != '') {
            $NIK = dlookup("userdetails", "newIC", "userID=" . tosql($UserID, "Text"));
            $NoAnggota = dlookup("userdetails", "memberID", "userID=" . tosql($UserID, "Text"));
            if ($NamaAnggota == '') $NamaAnggota = dlookup("users", "name", "userID=" . tosql($UserID, "Text"));

            $sqlAccList = "SELECT d.AccountNumber, d.Balance, d.Code_simpanan, g.name as NamaAkun
                           FROM depositoracc d
                           LEFT JOIN general g ON CONVERT(d.Code_simpanan USING utf8) = CONVERT(g.ID USING utf8)
                           WHERE d.UserID = " . tosql($UserID, "Text") . " AND d.Status IN (1,3)
                           ORDER BY d.AccountNumber";
            $rsAccList = $conn->Execute($sqlAccList);
            if ($rsAccList) {
                while (!$rsAccList->EOF) {
                    $accountList[] = array(
                        'AccountNumber' => $rsAccList->fields['AccountNumber'],
                        'Balance' => $rsAccList->fields['Balance'],
                        'Code_simpanan' => $rsAccList->fields['Code_simpanan'],
                        'NamaAkun' => $rsAccList->fields['NamaAkun']
                    );
                    $rsAccList->MoveNext();
                }
            }
            if (count($accountList) == 0) {
                echo "<script>alert('Anggota ini tidak memiliki rekening simpanan aktif!');</script>";
            }
        }

    } elseif ($action == 'pilih_account') {
        $UserID = isset($_POST['UserID']) ? trim($_POST['UserID']) : '';
        $AccountNumber = isset($_POST['AccountNumber']) ? trim($_POST['AccountNumber']) : '';

        if ($UserID != '' && $AccountNumber != '') {
            $sqlAcc = "SELECT d.*, u.Name as NamaAnggota, b.memberID, b.newIC as NIK,
                              g.name as NamaAkun, g.gl_bank_code, g.gl_bank_name,
                              g.loanCode as GLSimpananCode,
                              ga.name as GLSimpananName,
                              g.had_pengeluaran_sehari, g.had_pengeluaran_transaksi, g.jumlah_minimum
                       FROM depositoracc d
                       LEFT JOIN users u ON CONVERT(d.UserID USING utf8) = CONVERT(u.UserID USING utf8)
                       LEFT JOIN userdetails b ON CONVERT(u.userID USING utf8) = CONVERT(b.userID USING utf8)
                       LEFT JOIN general g ON CONVERT(d.Code_simpanan USING utf8) = CONVERT(g.ID USING utf8)
                       LEFT JOIN generalacc ga ON g.loanCode = ga.code
                       WHERE d.AccountNumber = " . tosql($AccountNumber, "Text") . " AND d.Status IN (1,3)";

            $rsAcc = $conn->Execute($sqlAcc);
            if ($rsAcc && !$rsAcc->EOF) {
                $UserID = $rsAcc->fields['UserID'];
                $NamaAnggota = $rsAcc->fields['NamaAnggota'];
                $NoAnggota = $rsAcc->fields['memberID'] ? $rsAcc->fields['memberID'] : '';
                $NIK = $rsAcc->fields['NIK'] ? $rsAcc->fields['NIK'] : '';
                $NamaAkun = $rsAcc->fields['NamaAkun'] ? $rsAcc->fields['NamaAkun'] : '';
                $SaldoRekening = $rsAcc->fields['Balance'];
                $Code_simpanan = $rsAcc->fields['Code_simpanan'];
                $GLBankCode = $rsAcc->fields['gl_bank_code'] ? $rsAcc->fields['gl_bank_code'] : '-';
                $GLBankName = $rsAcc->fields['gl_bank_name'] ? $rsAcc->fields['gl_bank_name'] : '-';
                $GLSimpananCode = $rsAcc->fields['GLSimpananCode'] ? $rsAcc->fields['GLSimpananCode'] : '-';
                $GLSimpananName = $rsAcc->fields['GLSimpananName'] ? $rsAcc->fields['GLSimpananName'] : '-';
                $HadSehari = floatval($rsAcc->fields['had_pengeluaran_sehari']);
                $HadTransaksi = floatval($rsAcc->fields['had_pengeluaran_transaksi']);
                $JumlahMinimum = floatval($rsAcc->fields['jumlah_minimum']);
            }
        }

    } elseif ($action == 'transaction') {
        $AccountNumber = isset($_POST['AccountNumber']) ? trim($_POST['AccountNumber']) : '';
        $JenisTransaksi = isset($_POST['JenisTransaksi']) ? trim($_POST['JenisTransaksi']) : '';
        $Nominal = isset($_POST['Nominal']) ? floatval($_POST['Nominal']) : 0;
        $TanggalTransaksi = isset($_POST['TanggalTransaksi']) ? trim($_POST['TanggalTransaksi']) : date('Y-m-d');
        $Ref1 = isset($_POST['Ref1']) ? trim($_POST['Ref1']) : '';
        $Ref2 = isset($_POST['Ref2']) ? trim($_POST['Ref2']) : '';
        $Keterangan = isset($_POST['Keterangan']) ? trim($_POST['Keterangan']) : '';

        if ($AccountNumber != '' && $JenisTransaksi != '' && $Nominal > 0) {
            $sqlAcc = "SELECT d.*, u.Name as NamaAnggota, b.memberID, b.newIC as NIK,
                              g.name as NamaAkun, g.gl_bank_code, g.gl_bank_name,
                              g.had_pengeluaran_sehari, g.had_pengeluaran_transaksi, g.jumlah_minimum
                       FROM depositoracc d
                       LEFT JOIN users u ON CONVERT(d.UserID USING utf8) = CONVERT(u.UserID USING utf8)
                       LEFT JOIN userdetails b ON CONVERT(u.userID USING utf8) = CONVERT(b.userID USING utf8)
                       LEFT JOIN general g ON CONVERT(d.Code_simpanan USING utf8) = CONVERT(g.ID USING utf8)
                       WHERE d.AccountNumber = " . tosql($AccountNumber, "Text") . " AND d.Status IN (1,3)";
            $rsAcc = $conn->Execute($sqlAcc);

            if ($rsAcc && !$rsAcc->EOF) {
                $UserID = $rsAcc->fields['UserID'];
                $NamaAnggota = $rsAcc->fields['NamaAnggota'];
                $NoAnggota = $rsAcc->fields['memberID'] ? $rsAcc->fields['memberID'] : '-';
                $NIK = $rsAcc->fields['NIK'] ? $rsAcc->fields['NIK'] : '-';
                $NamaAkun = $rsAcc->fields['NamaAkun'] ? $rsAcc->fields['NamaAkun'] : '-';
                $SaldoSebelum = $rsAcc->fields['Balance'];
                $Code_simpanan_db = $rsAcc->fields['Code_simpanan'];
                $HadSehari = floatval($rsAcc->fields['had_pengeluaran_sehari']);
                $HadTransaksi = floatval($rsAcc->fields['had_pengeluaran_transaksi']);

                if ($JenisTransaksi == 'TARIK') {
                    $namaSimpanan = strtolower($NamaAkun);
                    if (strpos($namaSimpanan, 'modal') !== false || strpos($namaSimpanan, 'pokok') !== false) {
                        echo "<script>alert('Simpanan modal/pokok tidak boleh ditarik!'); window.history.back();</script>";
                        exit;
                    }
                    if ($Nominal > $SaldoSebelum) {
                        echo "<script>alert('Saldo tidak mencukupi!'); window.history.back();</script>";
                        exit;
                    }
                    if ($HadTransaksi > 0 && $Nominal > $HadTransaksi) {
                        echo "<script>alert('Melebihi had pengeluaran per transaksi (RM " . number_format($HadTransaksi, 2) . ")!'); window.history.back();</script>";
                        exit;
                    }
                    if ($HadSehari > 0) {
                        $todayTotal = $conn->Execute("SELECT COALESCE(SUM(Nominal),0) as total FROM transactionsimpanan WHERE AccountNumber = " . tosql($AccountNumber, "Text") . " AND JenisTransaksi = 'TARIK' AND DATE(TanggalTransaksi) = CURDATE() AND Status IN (1,3)")->fields['total'];
                        if (($todayTotal + $Nominal) > $HadSehari) {
                            echo "<script>alert('Melebihi had pengeluaran sehari (RM " . number_format($HadSehari, 2) . ")!\\nSudah keluar hari ini: RM " . number_format($todayTotal, 2) . "'); window.history.back();</script>";
                            exit;
                        }
                    }
                }

                $SaldoSesudah = ($JenisTransaksi == 'SETOR') ? $SaldoSebelum + $Nominal : $SaldoSebelum - $Nominal;

                // Generate No Jurnal: JN + tahun(4) + bulan(2) + auto(12 digit)
                $jnPrefix = 'JN' . date('Ym');
                $rsMaxJN = $conn->Execute("SELECT MAX(NoJurnal) as maxjn FROM transactionsimpanan WHERE NoJurnal LIKE '" . $jnPrefix . "%'");
                $lastJN = 0;
                if ($rsMaxJN && $rsMaxJN->fields['maxjn'] != null) {
                    $lastJN = intval(substr($rsMaxJN->fields['maxjn'], strlen($jnPrefix)));
                }
                $NoJurnal = $jnPrefix . str_pad($lastJN + 1, 12, "0", STR_PAD_LEFT);

                $sqlInsert = "INSERT INTO transactionsimpanan
                    (TellerID, TellerName, UserID, NamaAnggota, NoAnggota,
                     AccountNumber, Code_simpanan, NamaAkun, NIK, NamaCabang, GLRAK,
                     JenisTransaksi, Nominal, SaldoSebelum, SaldoSesudah, TanggalTransaksi,
                     NoJurnal, Referensi, Keterangan, Status, CreatedBy, CreatedDate)
                VALUES (
                    " . tosql($TellerID, "Text") . ", " . tosql($TellerName, "Text") . ",
                    " . tosql($UserID, "Text") . ", " . tosql($NamaAnggota, "Text") . ", " . tosql($NoAnggota, "Text") . ",
                    " . tosql($AccountNumber, "Text") . ", " . tosql($Code_simpanan_db, "Text") . ",
                    " . tosql($NamaAkun, "Text") . ", " . tosql($NIK, "Text") . ",
                    'UNIT SIMPAN PINJAM (USP)', 'RAK PUSAT',
                    " . tosql($JenisTransaksi, "Text") . ", " . $Nominal . ", " . $SaldoSebelum . ", " . $SaldoSesudah . ",
                    " . tosql($TanggalTransaksi, "Text") . ", " . tosql($NoJurnal, "Text") . ",
                    " . tosql($Ref1, "Text") . ", " . tosql($Keterangan, "Text") . ",
                    1, " . tosql($TellerID, "Text") . ", NOW())";

                $resultInsert = $conn->Execute($sqlInsert);
                if (!$resultInsert) {
                    echo "<script>alert('Error: " . addslashes($conn->ErrorMsg()) . "'); window.history.back();</script>";
                    exit;
                }

                $conn->Execute("UPDATE depositoracc SET Balance = " . $SaldoSesudah . ", UpdateUserID = " . tosql($TellerID, "Text") . ", DateUpdateUserID = NOW() WHERE AccountNumber = " . tosql($AccountNumber, "Text"));

                // Insert ke transaction & transactionacc
                $yrmth = date('Ym', strtotime($TanggalTransaksi));
                $addminus = ($JenisTransaksi == 'SETOR') ? 1 : 0;
                $createdDate = date('Y-m-d H:i:s');

                $Master  = dlookup("general", "c_master", "ID = " . tosql($Code_simpanan_db, "Number"));
                $Master2 = dlookup("generalacc", "parentID", "ID = " . tosql($Master, "Number"));
                $coreID  = dlookup("generalacc", "coreID", "ID = " . tosql($Master, "Number"));

                $sqlTrans = "INSERT INTO `transaction` (docNo, userID, yrmth, deductID, addminus, pymtID, pymtRefer, pymtAmt, cajAmt, createdDate, createdBy, updatedDate, updatedBy) VALUES ("
                    . tosql($NoJurnal, "Text") . ", " . tosql($UserID, "Text") . ", " . tosql($yrmth, "Text") . ", "
                    . tosql($Code_simpanan_db, "Number") . ", '" . $addminus . "', 66, "
                    . tosql($Ref1, "Text") . ", '" . $Nominal . "', 0, "
                    . tosql($createdDate, "Text") . ", " . tosql($TellerID, "Text") . ", "
                    . tosql($createdDate, "Text") . ", " . tosql($TellerID, "Text") . ")";
                $conn->Execute($sqlTrans);
                $last_id = $conn->Insert_ID();

                $desc_akaun = $NamaAkun . ' - ' . $JenisTransaksi;
                $sqlTransAcc = "INSERT INTO transactionacc (docNo, docID, IDtrans, tarikh_doc, userID, yrmth, JdeductID, deductID, MdeductID, addminus, pymtID, pymtRefer, pymtAmt, coreID, desc_akaun, createdDate, createdBy, updatedDate, updatedBy) VALUES ("
                    . tosql($NoJurnal, "Text") . ", 10, '" . $last_id . "', "
                    . tosql($TanggalTransaksi, "Text") . ", " . tosql($UserID, "Text") . ", " . tosql($yrmth, "Text") . ", "
                    . tosql($Code_simpanan_db, "Text") . ", " . tosql($Master, "Text") . ", '" . (int)$Master2 . "', "
                    . "'" . $addminus . "', 66, " . tosql($Ref1, "Text") . ", '" . $Nominal . "', '" . (int)$coreID . "', "
                    . tosql($desc_akaun, "Text") . ", "
                    . tosql($createdDate, "Text") . ", " . tosql($TellerID, "Text") . ", "
                    . tosql($createdDate, "Text") . ", " . tosql($TellerID, "Text") . ")";
                $conn->Execute($sqlTransAcc);

                activityLog($sqlTrans, 'Transaksi Simpanan ' . $JenisTransaksi . ' - ' . $NoJurnal, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

                echo "<script>alert('Transaksi " . $JenisTransaksi . " berhasil!\\nNo. Jurnal: " . $NoJurnal . "'); window.location.href='?vw=transactionSimpanan&mn=902';</script>";
                exit;
            } else {
                echo "<script>alert('Rekening tidak ditemukan!'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
        }
    }
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container mt-4">
    <h3>Transaksi Pengeluaran & Penambahan Simpanan</h3>

    <!-- Step 1: Pilih Anggota -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Step 1 - Pilih Anggota</h5>
            <form method="post" action="" id="formMember">
                <input type="hidden" name="action" value="pilih_member">
                <input type="hidden" name="UserID" id="UserID" value="<?php echo htmlspecialchars($UserID); ?>">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Nama Anggota</label>
                        <div class="input-group">
                            <input type="text" name="loanName" id="loanName" class="form-control"
                                   value="<?php echo htmlspecialchars($NamaAnggota); ?>" readonly
                                   placeholder="Klik tombol Pilih untuk memilih anggota">
                            <button type="button" class="btn btn-info"
                                    onclick="window.open('selMember.php','sel','top=10,left=10,width=900,height=600,scrollbars=yes,resizable=yes')">
                                Pilih Anggota
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Cari Rekening</button>
                    </div>
                </div>
                <?php if ($UserID != '' && $NamaAnggota != ''): ?>
                <div class="row mt-2">
                    <div class="col-md-4"><small class="text-muted">No Anggota: <strong><?php echo htmlspecialchars($NoAnggota); ?></strong></small></div>
                    <div class="col-md-4"><small class="text-muted">No IC: <strong><?php echo htmlspecialchars($NIK); ?></strong></small></div>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($UserID != '' && count($accountList) > 0): ?>
    <!-- Step 2: Pilih Account -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Step 2 - Pilih Account Simpanan</h5>
            <form method="post" action="">
                <input type="hidden" name="action" value="pilih_account">
                <input type="hidden" name="UserID" value="<?php echo htmlspecialchars($UserID); ?>">
                <div class="row">
                    <div class="col-md-8">
                        <select name="AccountNumber" class="form-control" required>
                            <option value="">-- Pilih Account --</option>
                            <?php foreach ($accountList as $acc): ?>
                            <option value="<?php echo htmlspecialchars($acc['AccountNumber']); ?>">
                                <?php echo htmlspecialchars($acc['AccountNumber']); ?> -
                                <?php echo htmlspecialchars($acc['NamaAkun']); ?> -
                                Saldo: Rp <?php echo number_format($acc['Balance'], 2, ',', '.'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">Pilih Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($AccountNumber != '' && $NamaAkun != ''): ?>
    <!-- Step 3: Info & Form Transaksi -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Informasi Rekening</h5>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless" style="font-size: 10pt;">
                        <tr><td width="40%"><strong>Nama Anggota</strong></td><td>: <?php echo htmlspecialchars($NamaAnggota); ?></td></tr>
                        <tr><td><strong>No Anggota</strong></td><td>: <?php echo htmlspecialchars($NoAnggota); ?></td></tr>
                        <tr><td><strong>No IC</strong></td><td>: <?php echo htmlspecialchars($NIK); ?></td></tr>
                        <tr><td><strong>No Account</strong></td><td>: <?php echo htmlspecialchars($AccountNumber); ?></td></tr>
                        <tr><td><strong>Jenis Simpanan</strong></td><td>: <?php echo htmlspecialchars($NamaAkun); ?></td></tr>
                        <tr><td><strong>Saldo</strong></td><td>: <span class="text-primary fw-bold">Rp <?php echo number_format($SaldoRekening, 2, ',', '.'); ?></span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless" style="font-size: 10pt;">
                        <tr class="table-warning"><td colspan="2"><strong>Informasi GL</strong></td></tr>
                        <tr><td width="45%"><strong>Kod GL Bank</strong></td><td>: <?php echo htmlspecialchars($GLBankCode . ' - ' . $GLBankName); ?></td></tr>
                        <tr><td><strong>Kod GL Simpanan</strong></td><td>: <?php echo htmlspecialchars($GLSimpananCode . ' - ' . $GLSimpananName); ?></td></tr>
                        <tr class="table-info"><td colspan="2"><strong>Had / Limit</strong></td></tr>
                        <tr><td><strong>Jumlah Minimum</strong></td><td>: Rp <?php echo number_format($JumlahMinimum, 2, ',', '.'); ?></td></tr>
                        <tr><td><strong>Had Pengeluaran/Hari</strong></td><td>: Rp <?php echo number_format($HadSehari, 2, ',', '.'); ?></td></tr>
                        <tr><td><strong>Had Pengeluaran/Trx</strong></td><td>: Rp <?php echo number_format($HadTransaksi, 2, ',', '.'); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Step 3 - Input Transaksi</h5>
            <form method="post" action="" onsubmit="return confirm('Apakah Anda yakin ingin memproses transaksi ini?')">
                <input type="hidden" name="action" value="transaction">
                <input type="hidden" name="AccountNumber" value="<?php echo htmlspecialchars($AccountNumber); ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="TanggalTransaksi" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Jenis Transaksi <span class="text-danger">*</span></label>
                        <select name="JenisTransaksi" class="form-control" required id="jenisTransaksi">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="SETOR">Tambah (Setor)</option>
                            <option value="TARIK">Keluar (Tarik)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Amount (RM) <span class="text-danger">*</span></label>
                    <input type="number" name="Nominal" id="nominalInput" class="form-control" placeholder="Masukkan jumlah" step="0.01" min="1" required>
                    <small class="text-muted" id="limitInfo" style="display:none;"></small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Ref1</label>
                        <input type="text" name="Ref1" class="form-control" placeholder="Referensi 1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Ref2</label>
                        <input type="text" name="Ref2" class="form-control" placeholder="Referensi 2">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Keterangan</label>
                    <textarea name="Keterangan" id="keteranganInput" class="form-control" rows="2" readonly></textarea>
                    <small class="text-muted">Auto-generate</small>
                </div>

                <button type="submit" class="btn btn-primary">Proses Transaksi</button>
                <a href="?vw=transactionSimpanan&mn=902" class="btn btn-secondary">Reset</a>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
window.setSelectedMember = function(userId, userName) {
    document.getElementById('UserID').value = userId;
    document.getElementById('loanName').value = userName;
};

$(document).ready(function() {
    var glBankName = "<?php echo addslashes($GLBankName); ?>";
    var namaAkun = "<?php echo addslashes($NamaAkun); ?>";
    var accountNumber = "<?php echo addslashes($AccountNumber); ?>";
    var namaAnggota = "<?php echo addslashes($NamaAnggota); ?>";
    var hadTransaksi = <?php echo $HadTransaksi; ?>;
    var hadSehari = <?php echo $HadSehari; ?>;

    function generateKeterangan() {
        var jenis = $('#jenisTransaksi').val();
        if (jenis) {
            var jenisLabel = (jenis == 'SETOR') ? 'Penambahan' : 'Pengeluaran';
            var ket = 'Transaksi ' + jenisLabel + ' daripada ' + glBankName + ' melalui ' + namaAkun + ' ke ' + accountNumber + ' pemilik ' + namaAnggota;
            $('#keteranganInput').val(ket);
        }
    }

    $('#jenisTransaksi').change(function() {
        generateKeterangan();
        if ($(this).val() == 'TARIK' && (hadTransaksi > 0 || hadSehari > 0)) {
            var info = '';
            if (hadTransaksi > 0) info += 'Max/transaksi: Rp ' + hadTransaksi.toLocaleString('id-ID') + '  ';
            if (hadSehari > 0) info += 'Max/hari: Rp ' + hadSehari.toLocaleString('id-ID');
            $('#limitInfo').text(info).show();
        } else {
            $('#limitInfo').hide();
        }
    });
    $('#nominalInput').on('input', function() { generateKeterangan(); });
});
</script>

<?php include("footer.php"); ?>
