<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanPool.php
 *      Modul     : Pendanaan Usaha - Pool Dana & Histori Transaksi
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 30;

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

// ---- Helper: Post Jurnal Pendanaan ----
function postJurnalPendanaan($conn, $jenis, $ref_tabel, $ref_id, $keterangan, $tarikh, $lines, $by_user) {
    // $lines = array of array(no_akaun, nama_akaun, debit, kredit, keterangan)
    $ckTbl = $conn->Execute("SHOW TABLES LIKE 'pendanaan_jurnal'");
    if (!$ckTbl || $ckTbl->EOF) return false;

    // Generate no_jurnal: JN-PDN-YYYYMM-0001
    $prefix = 'JN-PDN-' . date('Ym', strtotime($tarikh)) . '-';
    $rsLast = $conn->Execute("SELECT no_jurnal FROM pendanaan_jurnal WHERE no_jurnal LIKE '" . $prefix . "%' ORDER BY jurnalID DESC LIMIT 1");
    if ($rsLast && !$rsLast->EOF) {
        $urut = (int) substr($rsLast->fields('no_jurnal'), -4) + 1;
    } else {
        $urut = 1;
    }
    $no_jurnal = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);

    $total_debit = 0; $total_kredit = 0;
    foreach ($lines as $ln) {
        $total_debit  += (float)$ln[2];
        $total_kredit += (float)$ln[3];
    }
    $now = date('Y-m-d H:i:s');

    $conn->Execute("INSERT INTO pendanaan_jurnal
        (no_jurnal, jenis, ref_tabel, ref_id, keterangan, tarikh, total_debit, total_kredit, createdDate, createdBy)
        VALUES ("
        . tosql($no_jurnal, "Text") . ","
        . tosql($jenis, "Text") . ","
        . tosql($ref_tabel, "Text") . ","
        . tosql($ref_id, "Number") . ","
        . tosql($keterangan, "Text") . ","
        . "'" . $tarikh . "',"
        . tosql($total_debit, "Number") . ","
        . tosql($total_kredit, "Number") . ","
        . "'" . $now . "',"
        . tosql($by_user, "Text") . ")");
    $jurnalID = $conn->Insert_ID();

    foreach ($lines as $ln) {
        $conn->Execute("INSERT INTO pendanaan_jurnal_detail
            (jurnalID, no_akaun, nama_akaun, debit, kredit, keterangan)
            VALUES ("
            . tosql($jurnalID, "Number") . ","
            . tosql($ln[0], "Text") . ","
            . tosql($ln[1], "Text") . ","
            . tosql((float)$ln[2], "Number") . ","
            . tosql((float)$ln[3], "Number") . ","
            . tosql($ln[4], "Text") . ")");
    }
    return $jurnalID;
}

$isAdmin = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
if (!$isAdmin) {
    print '<script>alert("Akses tidak diizinkan.");window.location="?vw=pendanaanList&mn=' . $mn . '";</script>';
    exit;
}

$by_user = get_session("Cookie_userName");

// --- Tambah dana ke pool ---
if ($action == 'TambahDana') {
    $now = date("Y-m-d H:i:s");
    $nominalTambah = (float) str_replace('.', '', str_replace(',', '.', $nominal_tambah));
    if ($nominalTambah <= 0) {
        $errMsg = "Nominal harus lebih dari 0.";
    } else {
        $saldoLama   = dlookup("pendanaan_pool", "saldo", "poolID=1");
        $saldo_baru  = $saldoLama + $nominalTambah;
        $total_masuk = dlookup("pendanaan_pool", "total_masuk", "poolID=1") + $nominalTambah;

        $conn->Execute("UPDATE pendanaan_pool SET saldo=" . tosql($saldo_baru, "Number") .
            ", total_masuk=" . tosql($total_masuk, "Number") .
            ", updatedDate='" . $now . "', updatedBy=" . tosql($by_user, "Text") . " WHERE poolID=1");

        $conn->Execute("INSERT INTO pendanaan_pool_trx
            (jenis, ref_tabel, ref_id, nominal, saldo_sesudah, keterangan, createdDate, createdBy)
            VALUES ('MASUK', 'manual', 0,"
            . tosql($nominalTambah, "Number") . ","
            . tosql($saldo_baru, "Number") . ","
            . tosql($keterangan_tambah ? $keterangan_tambah : 'Penambahan dana manual', "Text") . ","
            . "'" . $now . "',"
            . tosql($by_user, "Text") . ")");

        // Post jurnal akuntansi
        $jrnKet = ($keterangan_tambah ? $keterangan_tambah : 'Penambahan dana manual') . ' — Rp ' . number_format($nominalTambah, 0, ',', '.');
        $jrnLines = array(
            array('1-1001', 'Kas Pool Pendanaan',      $nominalTambah, 0,              $jrnKet),
            array('3-1001', 'Modal / Dana Pendanaan',  0,              $nominalTambah, $jrnKet),
        );
        postJurnalPendanaan($conn, 'TAMBAH_POOL', 'pendanaan_pool', 1, $jrnKet, date('Y-m-d'), $jrnLines, $by_user);

        activityLog("Tambah Dana Pool: Rp " . number_format($nominalTambah, 0, ',', '.'), "Tambah Pool Pendanaan", get_session('Cookie_userID'), $by_user, 3);
        print '<script>alert("Dana berhasil ditambahkan ke pool.");window.location="?vw=pendanaanPool&mn=' . $mn . '";</script>';
        exit;
    }
}

// --- Load pool info ---
$rsPool = $conn->Execute("SELECT * FROM pendanaan_pool WHERE poolID=1");
$saldo       = 0; $total_masuk = 0; $total_keluar = 0;
if ($rsPool && !$rsPool->EOF) {
    $saldo        = $rsPool->fields('saldo');
    $total_masuk  = $rsPool->fields('total_masuk');
    $total_keluar = $rsPool->fields('total_keluar');
}

// --- Load transaksi pool ---
$sSQL = "SELECT * FROM pendanaan_pool_trx ORDER BY createdDate DESC";
$GetList = $conn->Execute($sSQL);
$TotalRec = 0;
if ($GetList && !$GetList->EOF) {
    $GetList->Move($StartRec - 1);
    $TotalRec = $GetList->RowCount();
}
$sFileName = "?vw=pendanaanPool&mn=$mn";
?>

<div class="maroon" align="left">
    <a class="maroon" href="?vw=pendanaanList&mn=<?= $mn ?>">PENDANAAN USAHA</a>
    <b>&nbsp;&gt;&nbsp;POOL DANA</b>
</div>
<div>&nbsp;</div>

<?php if (!empty($errMsg)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <?= $errMsg ?>
</div>
<?php endif; ?>

<!-- Ringkasan Pool -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body p-2">
                <div class="text-white-50 small">Saldo Pool</div>
                <div class="fw-bold">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body p-2">
                <div class="text-white-50 small">Total Masuk</div>
                <div class="fw-bold">Rp <?= number_format($total_masuk, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body p-2">
                <div class="text-white-50 small">Total Keluar</div>
                <div class="fw-bold">Rp <?= number_format($total_keluar, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <!-- Ringkasan Distribusi Aktif -->
        <?php
        $totalOutstanding = $conn->Execute("SELECT IFNULL(SUM(nominal),0) AS jml FROM pendanaan_distribusi WHERE status=1");
        $outstanding = ($totalOutstanding && !$totalOutstanding->EOF) ? $totalOutstanding->fields('jml') : 0;
        ?>
        <div class="card text-white bg-warning">
            <div class="card-body p-2">
                <div class="text-dark small">Outstanding Aktif</div>
                <div class="fw-bold text-dark">Rp <?= number_format($outstanding, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Form Tambah Dana -->
<div class="card mb-3" style="max-width:500px">
    <div class="card-header p-2"><b>Tambah Dana ke Pool</b></div>
    <div class="card-body p-2">
    <form method="post" action="?vw=pendanaanPool&mn=<?= $mn ?>">
        <table class="table table-sm mb-0">
        <tr>
            <td width="140">Nominal (Rp)</td>
            <td><input type="text" name="nominal_tambah" class="form-control-sm" size="15" placeholder="Contoh: 10000000" required></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td><input type="text" name="keterangan_tambah" class="form-control-sm" size="30" placeholder="Sumber dana..."></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="action" value="TambahDana" class="btn btn-primary btn-sm"
                       onclick="return confirm('Tambah dana ke pool pendanaan?')"></td>
        </tr>
        </table>
    </form>
    </div>
</div>

<!-- Histori Transaksi Pool -->
<h6 class="card-title">Histori Transaksi Pool Dana</h6>
<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
    <thead class="table-primary">
        <tr>
            <th width="4%">No</th>
            <th>Jenis</th>
            <th>Keterangan</th>
            <th class="text-end">Nominal</th>
            <th class="text-end">Saldo Sesudah</th>
            <th>Tanggal</th>
            <th>Oleh</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = $StartRec;
    $found = false;
    while ($GetList && !$GetList->EOF && $i < ($StartRec + $pg)):
        $found = true;
        $jenis     = $GetList->fields('jenis');
        $ket       = $GetList->fields('keterangan');
        $nom       = $GetList->fields('nominal');
        $saldoSsd  = $GetList->fields('saldo_sesudah');
        $tglTrx    = toDate("d/m/Y H:i", $GetList->fields('createdDate'));
        $createdBy = $GetList->fields('createdBy');
        $jenisLabel = $jenis == 'MASUK'
            ? '<span class="badge bg-success">MASUK</span>'
            : '<span class="badge bg-danger">KELUAR</span>';
    ?>
    <tr>
        <td><?= $i ?></td>
        <td><?= $jenisLabel ?></td>
        <td><?= $ket ?></td>
        <td class="text-end">Rp <?= number_format($nom, 0, ',', '.') ?></td>
        <td class="text-end">Rp <?= number_format($saldoSsd, 0, ',', '.') ?></td>
        <td><?= $tglTrx ?></td>
        <td><?= $createdBy ?></td>
    </tr>
    <?php
        $i++;
        $GetList->MoveNext();
    endwhile;
    if (!$found):
    ?>
    <tr><td colspan="7" class="text-center text-muted">Belum ada transaksi.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<?php
$EndRec = min($StartRec + $pg - 1, $TotalRec);
print "<div class='row'>";
print "<div class='col-md-6'>Jumlah: <b>$TotalRec</b> | Paparan: <b>$StartRec - $EndRec</b>&nbsp;&nbsp;";
print papar_ms($pg);
print "</div><div class='col-md-6 text-end'>";
if ($StartRec > 1) {
    $PrevRec = max(1, $StartRec - $pg);
    print "<a href='$sFileName&StartRec=$PrevRec&pg=$pg' class='btn btn-sm btn-outline-secondary'>&laquo; Prev</a> ";
}
if ($EndRec < $TotalRec) {
    $NextRec = $StartRec + $pg;
    print "<a href='$sFileName&StartRec=$NextRec&pg=$pg' class='btn btn-sm btn-outline-secondary'>Next &raquo;</a>";
}
print "</div></div>";
?>
