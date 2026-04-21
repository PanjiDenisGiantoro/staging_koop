<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanJurnal.php
 *      Modul     : Pendanaan Usaha - Jurnal Akuntansi
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 30;
if (!isset($mm))       $mm       = date('m');
if (!isset($yy))       $yy       = date('Y');
if (!isset($jenis_filter)) $jenis_filter = '';
if (!isset($q))        $q        = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
if (!$isAdmin) {
    print '<script>alert("Akses tidak diizinkan.");window.location="?vw=pendanaanList&mn=' . $mn . '";</script>';
    exit;
}

// --- Cek tabel jurnal tersedia ---
$ckTabel = $conn->Execute("SHOW TABLES LIKE 'pendanaan_jurnal'");
if (!$ckTabel || $ckTabel->EOF) {
    print '<div class="alert alert-warning">Tabel jurnal belum dibuat. Jalankan <b>alter_pendanaan_jurnal.sql</b> di phpMyAdmin terlebih dahulu.</div>';
    include("footer.php");
    exit;
}

$sFileName = "?vw=pendanaanJurnal&mn=$mn";

// --- Label jenis ---
$jenisLabel = array(
    'TAMBAH_POOL'   => '<span class="badge bg-primary">Tambah Pool</span>',
    'DISTRIBUSI'    => '<span class="badge bg-danger">Distribusi</span>',
    'BAYAR_CICILAN' => '<span class="badge bg-success">Bayar Cicilan</span>',
    'BAYAR_DENDA'   => '<span class="badge bg-warning text-dark">Bayar Denda</span>',
    'ADJUSTMENT'    => '<span class="badge bg-secondary">Adjustment</span>',
);

// --- Build WHERE ---
$sWhere = "WHERE YEAR(tarikh)=" . (int)$yy;
if ($mm != 'ALL') $sWhere .= " AND MONTH(tarikh)=" . (int)$mm;
if ($jenis_filter != '') $sWhere .= " AND jenis=" . tosql($jenis_filter, "Text");
if ($q != '') $sWhere .= " AND (no_jurnal LIKE " . tosql('%'.$q.'%', "Text") . " OR keterangan LIKE " . tosql('%'.$q.'%', "Text") . ")";

// --- Count total ---
$rsCount = $conn->Execute("SELECT COUNT(*) AS jml FROM pendanaan_jurnal $sWhere");
$TotalRec = ($rsCount && !$rsCount->EOF) ? (int)$rsCount->fields('jml') : 0;

// --- Load list ---
$GetList = $conn->Execute("SELECT * FROM pendanaan_jurnal $sWhere ORDER BY tarikh DESC, jurnalID DESC LIMIT " . ($StartRec - 1) . "," . $pg);

// --- Ringkasan bulan ini ---
$rsSum = $conn->Execute("SELECT
    SUM(CASE WHEN jenis='TAMBAH_POOL'   THEN total_debit ELSE 0 END) AS sum_tambah,
    SUM(CASE WHEN jenis='DISTRIBUSI'    THEN total_kredit ELSE 0 END) AS sum_dist,
    SUM(CASE WHEN jenis='BAYAR_CICILAN' THEN total_debit ELSE 0 END) AS sum_bayar,
    COUNT(*) AS total_trx
    FROM pendanaan_jurnal WHERE YEAR(tarikh)=" . (int)$yy . " AND MONTH(tarikh)=" . (int)$mm);
$sumTambah = 0; $sumDist = 0; $sumBayar = 0; $totalTrx = 0;
if ($rsSum && !$rsSum->EOF) {
    $sumTambah = (float)$rsSum->fields('sum_tambah');
    $sumDist   = (float)$rsSum->fields('sum_dist');
    $sumBayar  = (float)$rsSum->fields('sum_bayar');
    $totalTrx  = (int)$rsSum->fields('total_trx');
}

$bulanNama = array(
    '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
    '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
    '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
);
$mmPad = sprintf('%02d', $mm);
$namaBulan = isset($bulanNama[$mmPad]) ? $bulanNama[$mmPad] : $mm;
?>

<div class="maroon" align="left">
    <a class="maroon" href="?vw=pendanaanList&mn=<?= $mn ?>">PENDANAAN USAHA</a>
    <b>&nbsp;&gt;&nbsp;JURNAL AKUNTANSI</b>
</div>
<div>&nbsp;</div>

<!-- Ringkasan bulan -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body p-2">
                <div class="text-white-50 small">Tambah Pool (<?= $namaBulan ?>)</div>
                <div class="fw-bold">Rp <?= number_format($sumTambah, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body p-2">
                <div class="text-white-50 small">Distribusi (<?= $namaBulan ?>)</div>
                <div class="fw-bold">Rp <?= number_format($sumDist, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body p-2">
                <div class="text-white-50 small">Bayar Cicilan (<?= $namaBulan ?>)</div>
                <div class="fw-bold">Rp <?= number_format($sumBayar, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-light">
            <div class="card-body p-2">
                <div class="text-muted small">Total Entri Bulan Ini</div>
                <div class="fw-bold"><?= $totalTrx ?> transaksi</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<form method="get" action="">
<input type="hidden" name="vw" value="pendanaanJurnal">
<input type="hidden" name="mn" value="<?= $mn ?>">
<div class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <select name="mm" class="form-select-sm">
            <option value="ALL" <?= ($mm=='ALL'?'selected':'') ?>>Semua Bulan</option>
            <?php foreach ($bulanNama as $k => $v): ?>
            <option value="<?= (int)$k ?>" <?= ($mmPad == $k ? 'selected' : '') ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <select name="yy" class="form-select-sm">
            <?php for ($yr = date('Y'); $yr >= date('Y')-3; $yr--): ?>
            <option value="<?= $yr ?>" <?= ($yy==$yr?'selected':'') ?>><?= $yr ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-auto">
        <select name="jenis_filter" class="form-select-sm">
            <option value="">Semua Jenis</option>
            <option value="TAMBAH_POOL"   <?= ($jenis_filter=='TAMBAH_POOL'?'selected':'') ?>>Tambah Pool</option>
            <option value="DISTRIBUSI"    <?= ($jenis_filter=='DISTRIBUSI'?'selected':'') ?>>Distribusi</option>
            <option value="BAYAR_CICILAN" <?= ($jenis_filter=='BAYAR_CICILAN'?'selected':'') ?>>Bayar Cicilan</option>
            <option value="BAYAR_DENDA"   <?= ($jenis_filter=='BAYAR_DENDA'?'selected':'') ?>>Bayar Denda</option>
        </select>
    </div>
    <div class="col-auto">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari no/keterangan..." class="form-control-sm" size="20">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        <a href="<?= $sFileName ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
    </div>
</div>
</form>

<!-- Tabel Jurnal -->
<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th width="5%">No</th>
            <th width="15%">No. Jurnal</th>
            <th width="8%">Tanggal</th>
            <th width="12%">Jenis</th>
            <th>Keterangan</th>
            <th class="text-end" width="12%">Total Debit</th>
            <th class="text-end" width="12%">Total Kredit</th>
            <th width="8%">Oleh</th>
            <th width="5%">Detail</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = $StartRec;
    $found = false;
    while ($GetList && !$GetList->EOF):
        $found = true;
        $jID       = $GetList->fields('jurnalID');
        $noJrn     = $GetList->fields('no_jurnal');
        $jenis     = $GetList->fields('jenis');
        $ket       = $GetList->fields('keterangan');
        $tarikh    = toDate("d/m/Y", $GetList->fields('tarikh'));
        $totDr     = $GetList->fields('total_debit');
        $totCr     = $GetList->fields('total_kredit');
        $crtBy     = $GetList->fields('createdBy');
        $jLabel    = isset($jenisLabel[$jenis]) ? $jenisLabel[$jenis] : '<span class="badge bg-secondary">'.$jenis.'</span>';
    ?>
    <tr>
        <td><?= $i ?></td>
        <td><b><?= $noJrn ?></b></td>
        <td><?= $tarikh ?></td>
        <td><?= $jLabel ?></td>
        <td><?= htmlspecialchars($ket) ?></td>
        <td class="text-end">Rp <?= number_format($totDr, 0, ',', '.') ?></td>
        <td class="text-end">Rp <?= number_format($totCr, 0, ',', '.') ?></td>
        <td><?= $crtBy ?></td>
        <td class="text-center">
            <a href="#" class="btn btn-xs btn-outline-info btn-sm"
               data-bs-toggle="collapse" data-bs-target="#det<?= $jID ?>"
               title="Lihat Detail">&#9660;</a>
        </td>
    </tr>
    <!-- Baris detail (collapse) -->
    <tr class="collapse" id="det<?= $jID ?>">
        <td colspan="9" class="p-0">
        <?php
        $rsDetail = $conn->Execute("SELECT * FROM pendanaan_jurnal_detail WHERE jurnalID=" . tosql($jID, "Number") . " ORDER BY detailID");
        ?>
        <table class="table table-sm mb-0 bg-light">
            <thead class="table-secondary">
                <tr>
                    <th width="5%" class="ps-4"></th>
                    <th width="12%">No. Akaun</th>
                    <th>Nama Akaun</th>
                    <th class="text-end" width="14%">Debit (Rp)</th>
                    <th class="text-end" width="14%">Kredit (Rp)</th>
                    <th width="20%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($rsDetail && !$rsDetail->EOF):
                $dNo    = $rsDetail->fields('no_akaun');
                $dNama  = $rsDetail->fields('nama_akaun');
                $dDr    = (float)$rsDetail->fields('debit');
                $dCr    = (float)$rsDetail->fields('kredit');
                $dKet   = $rsDetail->fields('keterangan');
            ?>
            <tr>
                <td></td>
                <td><code><?= $dNo ?></code></td>
                <td><?= $dDr > 0 ? $dNama : '<span class="ms-4">'.$dNama.'</span>' ?></td>
                <td class="text-end"><?= $dDr > 0 ? number_format($dDr, 0, ',', '.') : '-' ?></td>
                <td class="text-end"><?= $dCr > 0 ? number_format($dCr, 0, ',', '.') : '-' ?></td>
                <td class="text-muted small"><?= htmlspecialchars($dKet) ?></td>
            </tr>
            <?php $rsDetail->MoveNext(); endwhile; ?>
            </tbody>
        </table>
        </td>
    </tr>
    <?php
        $i++;
        $GetList->MoveNext();
    endwhile;
    if (!$found):
    ?>
    <tr><td colspan="9" class="text-center text-muted">Belum ada jurnal untuk periode ini.</td></tr>
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
    print "<a href='$sFileName&mm=$mm&yy=$yy&jenis_filter=".urlencode($jenis_filter)."&StartRec=$PrevRec&pg=$pg' class='btn btn-sm btn-outline-secondary'>&laquo; Prev</a> ";
}
if ($EndRec < $TotalRec) {
    $NextRec = $StartRec + $pg;
    print "<a href='$sFileName&mm=$mm&yy=$yy&jenis_filter=".urlencode($jenis_filter)."&StartRec=$NextRec&pg=$pg' class='btn btn-sm btn-outline-secondary'>Next &raquo;</a>";
}
print "</div></div>";
?>
