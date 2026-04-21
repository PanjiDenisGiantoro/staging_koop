<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanPenyata.php
 *      Modul     : Pendanaan Usaha - Penyata Bulanan (seperti statement kartu kredit)
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

// Validasi akses
if (!$isAdmin && !$usahaID) {
    $rsFirstUsaha = $conn->Execute("SELECT usahaID FROM usaha WHERE memberID=" . tosql($myMemberID, "Text") . " AND status=1 LIMIT 1");
    $usahaID = ($rsFirstUsaha && !$rsFirstUsaha->EOF) ? $rsFirstUsaha->fields('usahaID') : 0;
}
if (!$isAdmin) {
    $cekOwn = dlookup("usaha", "usahaID", "usahaID=" . tosql($usahaID, "Number") . " AND memberID=" . tosql($myMemberID, "Text"));
    if (!$cekOwn) {
        print '<script>alert("Akses tidak diizinkan.");window.location="?vw=pendanaanMonitoring&mn=' . $mn . '";</script>';
        exit;
    }
}

// Default: bulan & tahun saat ini
if (!isset($bulan) || !$bulan) $bulan = date('m');
if (!isset($tahun) || !$tahun) $tahun = date('Y');
$bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);

$firstDay = "$tahun-$bulan-01";
$lastDay  = date('Y-m-t', strtotime($firstDay));
$today    = date('Y-m-d');

// Load info usaha & akun kredit
$rsUsaha = $conn->Execute("SELECT * FROM usaha WHERE usahaID=" . tosql($usahaID, "Number"));
if (!$rsUsaha || $rsUsaha->EOF) {
    print '<div class="alert alert-danger">Usaha tidak ditemukan.</div>'; exit;
}
$namaUsaha = $rsUsaha->fields('nama_usaha');
$memberID  = $rsUsaha->fields('memberID');

$cekKrdTbl = $conn->Execute("SHOW TABLES LIKE 'pendanaan_kredit'");
$rsKrd = ($cekKrdTbl && !$cekKrdTbl->EOF)
    ? $conn->Execute("SELECT * FROM pendanaan_kredit WHERE usahaID=" . tosql($usahaID, "Number") . " AND status IN (1,2) LIMIT 1")
    : null;
$kLimit    = ($rsKrd && !$rsKrd->EOF) ? $rsKrd->fields('limit_kredit') : 0;
$kTerpakai = ($rsKrd && !$rsKrd->EOF) ? $rsKrd->fields('saldo_terpakai') : 0;
$kNoAkun   = ($rsKrd && !$rsKrd->EOF) ? $rsKrd->fields('no_akun') : '-';
$kSkor     = ($rsKrd && !$rsKrd->EOF) ? $rsKrd->fields('skor_kredit') : 0;

// Filter by distribusiID jika ada
$distFilter = '';
if ($distribusiID) $distFilter = " AND d.distribusiID=" . tosql($distribusiID, "Number");

// --- DATA PENYATA ---

// 1. Cicilan jatuh tempo pada bulan ini
$rsCilBln = $conn->Execute("SELECT c.*, d.no_distribusi, d.bunga_per_thn, d.tenor
    FROM pendanaan_cicilan c
    JOIN pendanaan_distribusi d ON c.distribusiID=d.distribusiID
    WHERE d.usahaID=" . tosql($usahaID, "Number") . $distFilter . "
      AND c.tgl_jatuh_tempo BETWEEN '$firstDay' AND '$lastDay'
    ORDER BY c.tgl_jatuh_tempo ASC");

// 2. Pembayaran yang masuk pada bulan ini
$rsByrBln = $conn->Execute("SELECT b.*, d.no_distribusi, c.angsuran_ke
    FROM pendanaan_bayar b
    JOIN pendanaan_cicilan c ON b.cicilanID=c.cicilanID
    JOIN pendanaan_distribusi d ON b.distribusiID=d.distribusiID
    WHERE d.usahaID=" . tosql($usahaID, "Number") . $distFilter . "
      AND b.tgl_bayar BETWEEN '$firstDay' AND '$lastDay'
    ORDER BY b.tgl_bayar ASC");

// 3. Distribusi baru bulan ini
$rsDistBaru = $conn->Execute("SELECT d.*, p.no_pengajuan
    FROM pendanaan_distribusi d
    JOIN pendanaan_pengajuan p ON d.pengajuanID=p.pengajuanID
    WHERE d.usahaID=" . tosql($usahaID, "Number") . $distFilter . "
      AND d.tgl_distribusi BETWEEN '$firstDay' AND '$lastDay'
    ORDER BY d.tgl_distribusi ASC");

// 4. Summary totals bulan ini
$rsSumTagihan = $conn->Execute("SELECT
    IFNULL(SUM(c.total_tagihan),0) AS total_tagihan,
    IFNULL(SUM(CASE WHEN c.status=1 THEN c.nominal_bayar ELSE 0 END),0) AS total_lunas,
    IFNULL(SUM(CASE WHEN c.status IN (0,2) THEN c.total_tagihan ELSE 0 END),0) AS total_belum,
    IFNULL(SUM(c.nominal_denda),0) AS total_denda
    FROM pendanaan_cicilan c
    JOIN pendanaan_distribusi d ON c.distribusiID=d.distribusiID
    WHERE d.usahaID=" . tosql($usahaID, "Number") . $distFilter . "
      AND c.tgl_jatuh_tempo BETWEEN '$firstDay' AND '$lastDay'");
$sumTagihan = ($rsSumTagihan && !$rsSumTagihan->EOF) ? $rsSumTagihan->fields : array();

// 5. Cicilan overdue (belum bayar, JT sebelum firstDay)
$rsOverdue = $conn->Execute("SELECT c.*, d.no_distribusi FROM pendanaan_cicilan c
    JOIN pendanaan_distribusi d ON c.distribusiID=d.distribusiID
    WHERE d.usahaID=" . tosql($usahaID, "Number") . $distFilter . "
      AND c.status IN (0,2) AND c.tgl_jatuh_tempo < '$firstDay'
    ORDER BY c.tgl_jatuh_tempo ASC");
$rsSumOverdue = $conn->Execute("SELECT IFNULL(SUM(total_tagihan),0) AS total FROM pendanaan_cicilan c
    JOIN pendanaan_distribusi d ON c.distribusiID=d.distribusiID
    WHERE d.usahaID=" . tosql($usahaID, "Number") . $distFilter . "
      AND c.status IN (0,2) AND c.tgl_jatuh_tempo < '$firstDay'");
$totalOverdue = $rsSumOverdue && !$rsSumOverdue->EOF ? $rsSumOverdue->fields('total') : 0;

// Bulan-bulan untuk dropdown
$bulanNama = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
                   '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des');
$periodLabel = (isset($bulanNama[$bulan]) ? $bulanNama[$bulan] : $bulan) . ' ' . $tahun;
?>

<div class="maroon">
    <a class="maroon" href="?vw=pendanaanMonitoring&mn=<?= $mn ?>&usahaID=<?= $usahaID ?>">MONITORING</a>
    <b>&nbsp;&gt;&nbsp;PENYATA <?= strtoupper($namaUsaha) ?></b>
</div>
<div>&nbsp;</div>

<!-- Filter Periode -->
<form method="get" action="" class="mb-3">
    <input type="hidden" name="vw" value="pendanaanPenyata">
    <input type="hidden" name="mn" value="<?= $mn ?>">
    <input type="hidden" name="usahaID" value="<?= $usahaID ?>">
    <?php if ($distribusiID): ?>
    <input type="hidden" name="distribusiID" value="<?= $distribusiID ?>">
    <?php endif; ?>
    <div class="d-flex align-items-center gap-2">
        <label>Periode:</label>
        <select name="bulan" class="form-select-sm" style="width:auto">
            <?php foreach ($bulanNama as $bVal => $bNama): ?>
            <option value="<?= $bVal ?>" <?= ($bulan==$bVal?'selected':'') ?>><?= $bNama ?></option>
            <?php endforeach; ?>
        </select>
        <select name="tahun" class="form-select-sm" style="width:auto">
            <?php for ($y = date('Y'); $y >= date('Y')-3; $y--): ?>
            <option value="<?= $y ?>" <?= ($tahun==$y?'selected':'') ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-secondary">Tampilkan</button>
        <a href="javascript:window.print()" class="btn btn-sm btn-outline-dark ms-2">
            <i class="mdi mdi-printer"></i> Cetak
        </a>
    </div>
</form>

<!-- Header Penyata (seperti statement kartu kredit) -->
<div id="print-area">
<div style="border:1px solid #dee2e6;border-radius:10px;padding:20px;margin-bottom:16px;background:#fff">
    <div class="row">
        <div class="col-md-7">
            <h5 style="color:#1a3a5c;margin-bottom:4px">PENYATA PENDANAAN USAHA</h5>
            <div style="font-size:0.85em;color:#666">Periode: <b><?= $periodLabel ?></b></div>
            <div style="font-size:0.85em;color:#666">Tanggal Penyata: <?= toDate("d/m/Y", $today) ?></div>
        </div>
        <div class="col-md-5 text-end">
            <div style="font-size:0.8em;color:#888">No. Akun</div>
            <div style="font-size:1.1em;font-weight:600;color:#1a3a5c;letter-spacing:2px"><?= $kNoAkun ?></div>
            <div style="font-size:0.85em"><?= strtoupper($namaUsaha) ?></div>
            <div style="font-size:0.8em;color:#888">No. Anggota: <?= $memberID ?></div>
        </div>
    </div>
    <hr>
    <!-- Summary -->
    <div class="row text-center">
        <div class="col-3">
            <div style="font-size:0.75em;color:#888">Limit Kredit</div>
            <div class="fw-bold text-primary">Rp <?= number_format($kLimit,0,',','.') ?></div>
        </div>
        <div class="col-3">
            <div style="font-size:0.75em;color:#888">Saldo Terpakai</div>
            <div class="fw-bold text-danger">Rp <?= number_format($kTerpakai,0,',','.') ?></div>
        </div>
        <div class="col-3">
            <div style="font-size:0.75em;color:#888">Tagihan Bulan Ini</div>
            <div class="fw-bold" style="color:#e67e22">Rp <?= number_format(isset($sumTagihan['total_tagihan']) ? $sumTagihan['total_tagihan'] : 0,0,',','.') ?></div>
        </div>
        <div class="col-3">
            <div style="font-size:0.75em;color:#888">Sudah Dibayar</div>
            <div class="fw-bold text-success">Rp <?= number_format(isset($sumTagihan['total_lunas']) ? $sumTagihan['total_lunas'] : 0,0,',','.') ?></div>
        </div>
    </div>
    <?php if ($totalOverdue > 0): ?>
    <div class="alert alert-danger p-2 mt-2 mb-0 text-center">
        <b><i class="mdi mdi-alert"></i> Terdapat tunggakan Rp <?= number_format($totalOverdue,0,',','.') ?> dari periode sebelumnya!</b>
    </div>
    <?php endif; ?>
</div>

<!-- Tunggakan Sebelumnya -->
<?php if ($rsOverdue && !$rsOverdue->EOF): ?>
<h6 class="text-danger">Tunggakan Sebelumnya</h6>
<div class="table-responsive mb-3">
<table class="table table-sm table-bordered border-danger">
    <thead class="table-danger">
        <tr><th>No. Distribusi</th><th>Angsuran</th><th>Jatuh Tempo</th><th class="text-end">Tagihan</th><th>Hari Telat</th></tr>
    </thead>
    <tbody>
    <?php while (!$rsOverdue->EOF):
        $hariTelat = (int)floor((strtotime($today) - strtotime($rsOverdue->fields('tgl_jatuh_tempo'))) / 86400);
    ?>
    <tr>
        <td><?= $rsOverdue->fields('no_distribusi') ?></td>
        <td>ke-<?= $rsOverdue->fields('angsuran_ke') ?></td>
        <td><?= toDate("d/m/Y", $rsOverdue->fields('tgl_jatuh_tempo')) ?></td>
        <td class="text-end">Rp <?= number_format($rsOverdue->fields('total_tagihan'),0,',','.') ?></td>
        <td class="text-danger"><b><?= $hariTelat ?> hari</b></td>
    </tr>
    <?php $rsOverdue->MoveNext(); endwhile; ?>
    <tr class="table-light fw-bold">
        <td colspan="3">Total Tunggakan</td>
        <td class="text-end text-danger">Rp <?= number_format($totalOverdue,0,',','.') ?></td>
        <td></td>
    </tr>
    </tbody>
</table>
</div>
<?php endif; ?>

<!-- Distribusi baru bulan ini -->
<?php if ($rsDistBaru && !$rsDistBaru->EOF): ?>
<h6>Pencairan Dana Baru Bulan Ini</h6>
<div class="table-responsive mb-3">
<table class="table table-sm table-bordered">
    <thead class="table-success">
        <tr><th>Tgl</th><th>No. Distribusi</th><th>No. Pengajuan</th><th class="text-end">Nominal</th><th>Tenor</th><th>Bunga/Thn</th></tr>
    </thead>
    <tbody>
    <?php while (!$rsDistBaru->EOF): ?>
    <tr>
        <td><?= toDate("d/m/Y", $rsDistBaru->fields('tgl_distribusi')) ?></td>
        <td><?= $rsDistBaru->fields('no_distribusi') ?></td>
        <td><?= $rsDistBaru->fields('no_pengajuan') ?></td>
        <td class="text-end">Rp <?= number_format($rsDistBaru->fields('nominal'),0,',','.') ?></td>
        <td><?= $rsDistBaru->fields('tenor') ?> bln</td>
        <td><?= $rsDistBaru->fields('bunga_per_thn') ?>%</td>
    </tr>
    <?php $rsDistBaru->MoveNext(); endwhile; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<!-- Tagihan Cicilan Bulan Ini -->
<h6>Tagihan Cicilan Bulan <?= $periodLabel ?></h6>
<div class="table-responsive mb-3">
<table class="table table-sm table-bordered">
    <thead class="table-warning">
        <tr>
            <th>No. Distribusi</th>
            <th>Angsuran</th>
            <th>Jatuh Tempo</th>
            <th class="text-end">Pokok</th>
            <th class="text-end">Bunga</th>
            <th class="text-end">Denda</th>
            <th class="text-end">Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $subTotal = 0;
    $statusCilLabel = array('0'=>'Belum Bayar','1'=>'Lunas','2'=>'Telat','3'=>'Macet');
    $statusBadge    = array('0'=>'bg-secondary','1'=>'bg-success','2'=>'bg-warning text-dark','3'=>'bg-danger');
    if ($rsCilBln && !$rsCilBln->EOF):
        while (!$rsCilBln->EOF):
            $rowTotal = $rsCilBln->fields('total_tagihan') + $rsCilBln->fields('nominal_denda');
            $subTotal += $rowTotal;
    ?>
    <tr>
        <td><?= $rsCilBln->fields('no_distribusi') ?></td>
        <td>ke-<?= $rsCilBln->fields('angsuran_ke') ?></td>
        <td><?= toDate("d/m/Y", $rsCilBln->fields('tgl_jatuh_tempo')) ?></td>
        <td class="text-end">Rp <?= number_format($rsCilBln->fields('nominal_pokok'),0,',','.') ?></td>
        <td class="text-end">Rp <?= number_format($rsCilBln->fields('nominal_bunga'),0,',','.') ?></td>
        <td class="text-end"><?= $rsCilBln->fields('nominal_denda') > 0 ? 'Rp '.number_format($rsCilBln->fields('nominal_denda'),0,',','.') : '-' ?></td>
        <td class="text-end fw-bold">Rp <?= number_format($rowTotal,0,',','.') ?></td>
        <?php $stsBadge = isset($statusBadge[$rsCilBln->fields('status')]) ? $statusBadge[$rsCilBln->fields('status')] : 'bg-secondary'; ?>
        <?php $stsText  = isset($statusCilLabel[$rsCilBln->fields('status')]) ? $statusCilLabel[$rsCilBln->fields('status')] : '-'; ?>
        <td><span class="badge <?= $stsBadge ?>"><?= $stsText ?></span></td>
    </tr>
    <?php $rsCilBln->MoveNext(); endwhile;
    else: ?>
    <tr><td colspan="8" class="text-center text-muted">Tidak ada tagihan pada periode ini.</td></tr>
    <?php endif; ?>
    </tbody>
    <tfoot class="table-light">
    <tr>
        <td colspan="6" class="text-end fw-bold">Jumlah Tagihan</td>
        <td class="text-end fw-bold">Rp <?= number_format(isset($sumTagihan['total_tagihan']) ? $sumTagihan['total_tagihan'] : 0,0,',','.') ?></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="6" class="text-end fw-bold text-success">Sudah Dibayar</td>
        <td class="text-end fw-bold text-success">Rp <?= number_format(isset($sumTagihan['total_lunas']) ? $sumTagihan['total_lunas'] : 0,0,',','.') ?></td>
        <td></td>
    </tr>
    <tr class="table-danger">
        <td colspan="6" class="text-end fw-bold">Sisa Belum Bayar</td>
        <td class="text-end fw-bold">Rp <?= number_format(isset($sumTagihan['total_belum']) ? $sumTagihan['total_belum'] : 0,0,',','.') ?></td>
        <td></td>
    </tr>
    </tfoot>
</table>
</div>

<!-- Riwayat Pembayaran Bulan Ini -->
<?php if ($rsByrBln && !$rsByrBln->EOF): ?>
<h6>Riwayat Pembayaran Bulan <?= $periodLabel ?></h6>
<div class="table-responsive mb-3">
<table class="table table-sm table-bordered">
    <thead class="table-success">
        <tr><th>Tgl Bayar</th><th>No. Distribusi</th><th>Angsuran</th><th>Metode</th><th>No. Ref</th><th class="text-end">Nominal</th></tr>
    </thead>
    <tbody>
    <?php $totalByrBln = 0; while (!$rsByrBln->EOF):
        $totalByrBln += $rsByrBln->fields('nominal');
    ?>
    <tr>
        <td><?= toDate("d/m/Y", $rsByrBln->fields('tgl_bayar')) ?></td>
        <td><?= $rsByrBln->fields('no_distribusi') ?></td>
        <td>ke-<?= $rsByrBln->fields('angsuran_ke') ?></td>
        <td><?= $rsByrBln->fields('metode_bayar') ?></td>
        <td><?= $rsByrBln->fields('no_referensi') ? $rsByrBln->fields('no_referensi') : '-' ?></td>
        <td class="text-end text-success fw-bold">Rp <?= number_format($rsByrBln->fields('nominal'),0,',','.') ?></td>
    </tr>
    <?php $rsByrBln->MoveNext(); endwhile; ?>
    <tr class="table-light fw-bold">
        <td colspan="5" class="text-end">Total Bayar Bulan Ini</td>
        <td class="text-end text-success">Rp <?= number_format($totalByrBln,0,',','.') ?></td>
    </tr>
    </tbody>
</table>
</div>
<?php endif; ?>

<!-- Footer penyata -->
<div style="border-top:1px solid #dee2e6;padding-top:10px;font-size:0.78em;color:#888;text-align:center" class="mt-2">
    Penyata ini dicetak pada <?= toDate("d/m/Y H:i", date("Y-m-d H:i:s")) ?> oleh sistem iKOOP.com.my<br>
    Dokumen ini adalah penyata resmi. Hubungi koperasi jika ada pertanyaan.
</div>
</div><!-- end print-area -->

<div class="mt-3 no-print">
    <a href="?vw=pendanaanMonitoring&mn=<?= $mn ?>&usahaID=<?= $usahaID ?>"
       class="btn btn-sm btn-outline-secondary">← Kembali ke Monitoring</a>
</div>

<style>
@media print {
    .no-print, nav, #topbar, #sidebar, .btn, .page-topbar, .vertical-menu { display:none !important; }
    #print-area { margin:0; padding:10px; }
    body { font-size:12px; }
}
</style>
