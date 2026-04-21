<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanKreditList.php
 *      Modul     : Pendanaan Usaha - Manajemen Akun Kredit
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 30;
if (!isset($q))        $q        = '';
if (!isset($filter))   $filter   = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
if (!$isAdmin) {
    print '<script>alert("Akses tidak diizinkan.");window.location="?vw=pendanaanMonitoring&mn=' . $mn . '";</script>';
    exit;
}

// Cek tabel sudah dibuat
$cekTabel = $conn->Execute("SHOW TABLES LIKE 'pendanaan_kredit'");
if (!$cekTabel || $cekTabel->EOF) {
    print '<div class="alert alert-warning"><b>Tabel modul kredit belum dibuat.</b><br>
        Jalankan <code>alter_pendanaan.sql</code> di phpMyAdmin terlebih dahulu.<br>
        <code>CREATE TABLE pendanaan_kredit, pendanaan_kredit_trx, pendanaan_notifikasi</code></div>';
    exit;
}

$sFileName = "?vw=pendanaanKreditList&mn=$mn";
$sFileRef  = "?vw=pendanaanKredit&mn=$mn";

// --- Suspend / Aktifkan ---
if (($action == 'suspend' || $action == 'aktif') && $kreditID) {
    $now = date("Y-m-d H:i:s");
    $newStatus = ($action == 'suspend') ? 2 : 1;
    $conn->Execute("UPDATE pendanaan_kredit SET status=$newStatus, updatedDate='$now', updatedBy=" .
        tosql(get_session("Cookie_userName"), "Text") . " WHERE kreditID=" . tosql($kreditID, "Number"));
}

// --- Query ---
$sWhere = " WHERE 1=1";
if ($q != '') $sWhere .= " AND (k.no_akun LIKE '%" . $q . "%' OR u.nama_usaha LIKE '%" . $q . "%' OR k.memberID LIKE '%" . $q . "%')";
if ($filter !== '') $sWhere .= " AND k.status=" . tosql($filter, "Number");

$sSQL = "SELECT k.*, u.nama_usaha,
    ROUND(IF(k.limit_kredit>0, (k.saldo_terpakai/k.limit_kredit)*100, 0),1) AS pct_pakai
    FROM pendanaan_kredit k
    LEFT JOIN usaha u ON k.usahaID = u.usahaID"
    . $sWhere . " ORDER BY k.createdDate DESC";
$GetList = $conn->Execute($sSQL);
$TotalRec = 0;
if ($GetList && !$GetList->EOF) {
    $GetList->Move($StartRec - 1);
    $TotalRec = $GetList->RowCount();
}

$statusLabel = array(
    '0' => '<span class="badge bg-secondary">Pending</span>',
    '1' => '<span class="badge bg-success">Aktif</span>',
    '2' => '<span class="badge bg-warning text-dark">Suspend</span>',
    '3' => '<span class="badge bg-dark">Nonaktif</span>',
);
?>

<div class="maroon"><b>MANAJEMEN AKUN KREDIT PENDANAAN</b></div>
<div>&nbsp;</div>
<form name="MyForm" action="<?= $sFileName ?>" method="post">
<input type="hidden" name="filter" value="<?= $filter ?>">

<div class="row mb-2">
    <div class="col-md-5">
        <input type="text" name="q" value="<?= $q ?>" maxlength="50" size="28"
               class="form-control-sm d-inline" placeholder="Cari no. akun / usaha / anggota...">
        <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
    </div>
    <div class="col-md-3">
        Status:
        <select name="filter" class="form-select-sm d-inline w-auto" onchange="document.MyForm.submit()">
            <option value="">- Semua -</option>
            <option value="0" <?= $filter==='0'?'selected':'' ?>>Pending</option>
            <option value="1" <?= $filter==='1'?'selected':'' ?>>Aktif</option>
            <option value="2" <?= $filter==='2'?'selected':'' ?>>Suspend</option>
        </select>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= $sFileRef ?>&action=new" class="btn btn-sm btn-primary">+ Buka Akun Kredit</a>
    </div>
</div>

<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
    <thead class="table-primary">
        <tr>
            <th>No</th>
            <th>No. Akun</th>
            <th>No. Anggota</th>
            <th>Nama Usaha</th>
            <th class="text-end">Limit</th>
            <th class="text-end">Terpakai</th>
            <th class="text-end">Tersedia</th>
            <th>Utilisasi</th>
            <th>Skor</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = $StartRec; $found = false;
    while ($GetList && !$GetList->EOF && $i < ($StartRec + $pg)):
        $found     = true;
        $kID       = $GetList->fields('kreditID');
        $noAkun    = $GetList->fields('no_akun');
        $memberID  = $GetList->fields('memberID');
        $namaUsaha = $GetList->fields('nama_usaha');
        $limit     = $GetList->fields('limit_kredit');
        $terpakai  = $GetList->fields('saldo_terpakai');
        $tersedia  = $limit - $terpakai;
        $pct       = $GetList->fields('pct_pakai');
        $skor      = $GetList->fields('skor_kredit');
        $sts       = $GetList->fields('status');

        $barClass = $pct < 50 ? 'bg-success' : ($pct < 80 ? 'bg-warning' : 'bg-danger');
        $skorClass = $skor >= 80 ? 'text-success' : ($skor >= 60 ? 'text-warning' : 'text-danger');
    ?>
    <tr>
        <td><?= $i ?></td>
        <td><a href="?vw=pendanaanKredit&mn=<?= $mn ?>&action=view&kreditID=<?= $kID ?>"><?= $noAkun ?></a></td>
        <td><?= $memberID ?></td>
        <td><?= $namaUsaha ?></td>
        <td class="text-end">Rp <?= number_format($limit, 0, ',', '.') ?></td>
        <td class="text-end text-danger">Rp <?= number_format($terpakai, 0, ',', '.') ?></td>
        <td class="text-end text-success">Rp <?= number_format($tersedia, 0, ',', '.') ?></td>
        <td style="min-width:100px">
            <div class="progress" style="height:10px">
                <div class="progress-bar <?= $barClass ?>" style="width:<?= $pct ?>%"></div>
            </div>
            <small><?= $pct ?>%</small>
        </td>
        <td class="<?= $skorClass ?> fw-bold"><?= $skor ?></td>
        <td><?= isset($statusLabel[$sts]) ? $statusLabel[$sts] : '-' ?></td>
        <td nowrap>
            <a href="?vw=pendanaanKredit&mn=<?= $mn ?>&action=view&kreditID=<?= $kID ?>"
               class="btn btn-xs btn-info btn-sm">Lihat</a>
            <a href="?vw=pendanaanMonitoring&mn=<?= $mn ?>&usahaID=<?= $GetList->fields('usahaID') ?>"
               class="btn btn-xs btn-dark btn-sm">Monitor</a>
            <?php if ($sts == 1): ?>
            <a href="?vw=pendanaanKreditList&mn=<?= $mn ?>&action=suspend&kreditID=<?= $kID ?>"
               class="btn btn-xs btn-warning btn-sm"
               onclick="return confirm('Suspend akun ini?')">Suspend</a>
            <?php elseif ($sts == 2): ?>
            <a href="?vw=pendanaanKreditList&mn=<?= $mn ?>&action=aktif&kreditID=<?= $kID ?>"
               class="btn btn-xs btn-success btn-sm"
               onclick="return confirm('Aktifkan kembali akun ini?')">Aktifkan</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php $i++; $GetList->MoveNext(); endwhile; ?>
    <?php if (!$found): ?>
    <tr><td colspan="11" class="text-center text-muted">Tidak ada data.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<?php
$EndRec = min($StartRec + $pg - 1, $TotalRec);
print "<div class='row'><div class='col-md-6'>Jumlah: <b>$TotalRec</b> | Paparan: <b>$StartRec - $EndRec</b>&nbsp;&nbsp;";
print papar_ms($pg);
print "</div><div class='col-md-6 text-end'>";
if ($StartRec > 1) print "<a href='$sFileName&StartRec=" . max(1,$StartRec-$pg) . "&pg=$pg&q=$q&filter=$filter' class='btn btn-sm btn-outline-secondary'>&laquo; Prev</a> ";
if ($EndRec < $TotalRec) print "<a href='$sFileName&StartRec=" . ($StartRec+$pg) . "&pg=$pg&q=$q&filter=$filter' class='btn btn-sm btn-outline-secondary'>Next &raquo;</a>";
print "</div></div>";
?>
</form>
