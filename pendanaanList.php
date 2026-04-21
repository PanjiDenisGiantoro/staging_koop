<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanList.php
 *      Modul     : Pendanaan Usaha - Daftar Pengajuan
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 30;
if (!isset($q))        $q        = '';
if (!isset($by))       $by       = '1';
if (!isset($filter))   $filter   = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

$sFileName = "?vw=pendanaanList&mn=$mn";
$sFileRef  = "?vw=pendanaan&mn=$mn";
$title     = "Daftar Pengajuan Pendanaan Usaha";

// ---- Helper: simpan record approval ----
function simpanApproval($conn, $pengajuanID, $level, $by_user, $now) {
    $cek = dlookup("pendanaan_approval", "approvalID",
        "pengajuanID=" . tosql($pengajuanID, "Number") . " AND level_approval=" . tosql($level, "Text"));
    if (!$cek) {
        $conn->Execute("INSERT INTO pendanaan_approval
            (pengajuanID, level_approval, approverID, status, tgl_approval, createdDate)
            VALUES (" . tosql($pengajuanID, "Number") . "," . tosql($level, "Text") . ","
            . tosql($by_user, "Text") . ",1,'$now','$now')");
    } else {
        $conn->Execute("UPDATE pendanaan_approval SET status=1, approverID=" . tosql($by_user, "Text") .
            ", tgl_approval='$now' WHERE approvalID=" . tosql($cek, "Number"));
    }
}

// --- Approve Langsung (1 klik — selesaikan kedua level sekaligus) ---
if ($action == "approve_all" && $isAdmin && $pengajuanID) {
    $now     = date("Y-m-d H:i:s");
    $by_user = get_session("Cookie_userName");
    simpanApproval($conn, $pengajuanID, 'ADMIN',   $by_user, $now);
    simpanApproval($conn, $pengajuanID, 'MANAGER', $by_user, $now);
    $conn->Execute("UPDATE pendanaan_pengajuan SET status=2, tgl_diproses='$now', updatedDate='$now', updatedBy=" .
        tosql($by_user, "Text") . " WHERE pengajuanID=" . tosql($pengajuanID, "Number"));
    activityLog("Approve Langsung Pengajuan ID: $pengajuanID", "Approve Langsung Pendanaan", get_session('Cookie_userID'), $by_user, 3);
    print '<script>alert("Pengajuan disetujui.");window.location="' . $sFileName . '";</script>';
    exit;
}

// --- Approve (Admin level) ---
if ($action == "approve" && $isAdmin && $pengajuanID) {
    $now     = date("Y-m-d H:i:s");
    $by_user = get_session("Cookie_userName");
    simpanApproval($conn, $pengajuanID, 'ADMIN', $by_user, $now);

    $cekMgr = dlookup("pendanaan_approval", "status",
        "pengajuanID=" . tosql($pengajuanID, "Number") . " AND level_approval='MANAGER'");
    if ($cekMgr == 1) {
        $conn->Execute("UPDATE pendanaan_pengajuan SET status=2, tgl_diproses='$now', updatedDate='$now', updatedBy=" .
            tosql($by_user, "Text") . " WHERE pengajuanID=" . tosql($pengajuanID, "Number"));
    } else {
        $conn->Execute("UPDATE pendanaan_pengajuan SET status=1, updatedDate='$now', updatedBy=" .
            tosql($by_user, "Text") . " WHERE pengajuanID=" . tosql($pengajuanID, "Number"));
    }
    activityLog("Approve Admin Pengajuan ID: $pengajuanID", "Approve Pendanaan (Admin)", get_session('Cookie_userID'), $by_user, 3);
    print '<script>alert("Approval admin dicatat.");window.location="' . $sFileName . '";</script>';
    exit;
}

// --- Approve (Manager level) ---
if ($action == "approve_mgr" && $isAdmin && $pengajuanID) {
    $now     = date("Y-m-d H:i:s");
    $by_user = get_session("Cookie_userName");
    simpanApproval($conn, $pengajuanID, 'MANAGER', $by_user, $now);

    $cekAdm = dlookup("pendanaan_approval", "status",
        "pengajuanID=" . tosql($pengajuanID, "Number") . " AND level_approval='ADMIN'");
    if ($cekAdm == 1) {
        $conn->Execute("UPDATE pendanaan_pengajuan SET status=2, tgl_diproses='$now', updatedDate='$now', updatedBy=" .
            tosql($by_user, "Text") . " WHERE pengajuanID=" . tosql($pengajuanID, "Number"));
    } else {
        $conn->Execute("UPDATE pendanaan_pengajuan SET status=1, updatedDate='$now', updatedBy=" .
            tosql($by_user, "Text") . " WHERE pengajuanID=" . tosql($pengajuanID, "Number"));
    }
    activityLog("Approve Manager Pengajuan ID: $pengajuanID", "Approve Pendanaan (Manager)", get_session('Cookie_userID'), $by_user, 3);
    print '<script>alert("Approval manager dicatat.");window.location="' . $sFileName . '";</script>';
    exit;
}

// --- Tolak ---
if ($action == "tolak" && $isAdmin && $pengajuanID) {
    $now = date("Y-m-d H:i:s");
    $by_user = get_session("Cookie_userName");
    $conn->Execute("UPDATE pendanaan_pengajuan SET status=3, tgl_diproses='$now', updatedDate='$now', updatedBy=" .
        tosql($by_user, "Text") . " WHERE pengajuanID=" . tosql($pengajuanID, "Number"));
    activityLog("Tolak Pengajuan ID: $pengajuanID", "Tolak Pendanaan", get_session('Cookie_userID'), $by_user, 3);
    print '<script>alert("Pengajuan ditolak.");window.location="' . $sFileName . '";</script>';
    exit;
}

// --- Hapus (Admin, hanya status Draft) ---
if ($action == "delete" && $isAdmin) {
    for ($i = 0; $i < count($pk); $i++) {
        $cekSts = dlookup("pendanaan_pengajuan", "status", "pengajuanID=" . tosql($pk[$i], "Number"));
        if ($cekSts == 0) {
            $conn->Execute("DELETE FROM pendanaan_pengajuan WHERE pengajuanID=" . tosql($pk[$i], "Number"));
            activityLog("Hapus Pengajuan ID: " . $pk[$i], "Hapus Pendanaan", get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
        }
    }
}

// --- Query ---
$sWhere = " WHERE 1=1";
if (!$isAdmin) {
    $sWhere .= " AND p.memberID=" . tosql($myMemberID, "Text");
}
if ($q != '') {
    if ($by == 1) $sWhere .= " AND p.no_pengajuan LIKE '%" . $q . "%'";
    if ($by == 2) $sWhere .= " AND u.nama_usaha LIKE '%" . $q . "%'";
    if ($by == 3) $sWhere .= " AND p.memberID LIKE '%" . $q . "%'";
}
if ($filter !== '') {
    $sWhere .= " AND p.status=" . tosql($filter, "Number");
}

$sSQL = "SELECT p.*, u.nama_usaha FROM pendanaan_pengajuan p
         LEFT JOIN usaha u ON p.usahaID = u.usahaID"
       . $sWhere . " ORDER BY p.createdDate DESC";
$GetList = $conn->Execute($sSQL);
$TotalRec = 0;
if ($GetList && !$GetList->EOF) {
    $GetList->Move($StartRec - 1);
    $TotalRec = $GetList->RowCount();
}
$TotalPage = ($TotalRec / $pg);

$statusLabel = array(
    '0' => '<span class="badge bg-secondary">Draft</span>',
    '1' => '<span class="badge bg-warning text-dark">Pending</span>',
    '2' => '<span class="badge bg-success">Disetujui</span>',
    '3' => '<span class="badge bg-danger">Ditolak</span>',
);
?>

<div class="maroon" align="left">
    <b><?= strtoupper($title) ?></b>
</div>
<div style="width:100%;text-align:left">
<div>&nbsp;</div>
<form name="MyForm" action="<?= $sFileName ?>" method="post">
<input type="hidden" name="action">
<input type="hidden" name="filter" value="<?= $filter ?>">

<h5 class="card-title"><?= strtoupper($title) ?></h5>

<div class="row mb-2">
    <div class="col-md-5">
        Cari: <select name="by" class="form-select-sm d-inline w-auto">
            <option value="1" <?= $by==1?'selected':'' ?>>No. Pengajuan</option>
            <option value="2" <?= $by==2?'selected':'' ?>>Nama Usaha</option>
            <option value="3" <?= $by==3?'selected':'' ?>>No. Anggota</option>
        </select>
        <input type="text" name="q" value="<?= $q ?>" maxlength="50" size="25" class="form-control-sm d-inline">
        <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
    </div>
    <div class="col-md-3">
        Status:
        <select name="filter" class="form-select-sm d-inline w-auto" onchange="document.MyForm.submit()">
            <option value="">- Semua -</option>
            <option value="0" <?= $filter==='0'?'selected':'' ?>>Draft</option>
            <option value="1" <?= $filter==='1'?'selected':'' ?>>Pending</option>
            <option value="2" <?= $filter==='2'?'selected':'' ?>>Disetujui</option>
            <option value="3" <?= $filter==='3'?'selected':'' ?>>Ditolak</option>
        </select>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= $sFileRef ?>&action=new" class="btn btn-sm btn-primary">+ Ajukan Pendanaan</a>
        <?php if ($isAdmin): ?>
        &nbsp;<a href="?vw=pendanaanPool&mn=<?= $mn ?>" class="btn btn-sm btn-info">Pool Dana</a>
        <?php endif; ?>
    </div>
</div>

<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
    <thead class="table-primary">
        <tr>
            <th width="3%"><input type="checkbox" onclick="checkAll(this)"></th>
            <th width="4%">No</th>
            <th>No. Pengajuan</th>
            <?php if ($isAdmin): ?><th>No. Anggota</th><?php endif; ?>
            <th>Nama Usaha</th>
            <th>Nominal</th>
            <th>Tenor</th>
            <th>Status</th>
            <th>Tgl. Pengajuan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = $StartRec;
    $found = false;
    while ($GetList && !$GetList->EOF && $i < ($StartRec + $pg)):
        $found = true;
        $pengajuanID  = $GetList->fields('pengajuanID');
        $no_peng      = $GetList->fields('no_pengajuan');
        $memberID     = $GetList->fields('memberID');
        $namaUsaha    = $GetList->fields('nama_usaha');
        $nominal      = $GetList->fields('nominal');
        $tenor        = $GetList->fields('tenor');
        $status       = $GetList->fields('status');
        $tglPeng      = toDate("d/m/Y", $GetList->fields('tgl_pengajuan'));
    ?>
    <tr>
        <td><input type="checkbox" name="pk[]" value="<?= $pengajuanID ?>"></td>
        <td><?= $i ?></td>
        <td><a href="?vw=pendanaan&mn=<?= $mn ?>&action=view&pengajuanID=<?= $pengajuanID ?>"><?= $no_peng ?></a></td>
        <?php if ($isAdmin): ?><td><?= $memberID ?></td><?php endif; ?>
        <td><?= $namaUsaha ?></td>
        <td class="text-end">Rp <?= number_format($nominal, 0, ',', '.') ?></td>
        <td class="text-center"><?= $tenor ?> bln</td>
        <td><?= isset($statusLabel[$status]) ? $statusLabel[$status] : '-' ?></td>
        <td><?= $tglPeng ?></td>
        <td nowrap>
            <a href="?vw=pendanaan&mn=<?= $mn ?>&action=view&pengajuanID=<?= $pengajuanID ?>"
               class="btn btn-xs btn-info btn-sm">Lihat</a>
            <?php if ($status == 0 && (!$isAdmin ? $memberID == $myMemberID : true)): ?>
                <a href="?vw=pendanaan&mn=<?= $mn ?>&action=edit&pengajuanID=<?= $pengajuanID ?>"
                   class="btn btn-xs btn-warning btn-sm">Edit</a>
            <?php endif; ?>
            <?php if ($isAdmin && ($status == 0 || $status == 1)): ?>
                <a href="?vw=pendanaanList&mn=<?= $mn ?>&action=approve_all&pengajuanID=<?= $pengajuanID ?>"
                   class="btn btn-xs btn-success btn-sm"
                   onclick="return confirm('Setujui pengajuan ini?')">&#10003; Setujui</a>
            <?php endif; ?>
            <?php if ($isAdmin && $status == 1): ?>
                <a href="?vw=pendanaanList&mn=<?= $mn ?>&action=approve&pengajuanID=<?= $pengajuanID ?>"
                   class="btn btn-xs btn-outline-success btn-sm"
                   onclick="return confirm('Approval Admin saja (masih perlu Manager)?')">Apv Admin</a>
                <a href="?vw=pendanaanList&mn=<?= $mn ?>&action=approve_mgr&pengajuanID=<?= $pengajuanID ?>"
                   class="btn btn-xs btn-outline-primary btn-sm"
                   onclick="return confirm('Approval Manager saja (masih perlu Admin)?')">Apv Mgr</a>
            <?php endif; ?>
            <?php if ($isAdmin && ($status == 0 || $status == 1)): ?>
                <a href="?vw=pendanaanList&mn=<?= $mn ?>&action=tolak&pengajuanID=<?= $pengajuanID ?>"
                   class="btn btn-xs btn-danger btn-sm"
                   onclick="return confirm('Tolak pengajuan ini?')">Tolak</a>
            <?php endif; ?>
            <?php if ($isAdmin && $status == 2): ?>
                <a href="?vw=pendanaanDistribusi&mn=<?= $mn ?>&pengajuanID=<?= $pengajuanID ?>"
                   class="btn btn-xs btn-dark btn-sm">Distribusi</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php
        $i++;
        $GetList->MoveNext();
    endwhile;
    if (!$found):
    ?>
    <tr><td colspan="10" class="text-center text-muted">Tidak ada data.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- Pagination -->
<?php
$EndRec = min($StartRec + $pg - 1, $TotalRec);
print "<div class='row'>";
print "<div class='col-md-6'>";
print "Jumlah: <b>$TotalRec</b> | Paparan: <b>$StartRec - $EndRec</b>&nbsp;&nbsp;";
print papar_ms($pg);
print "</div><div class='col-md-6 text-end'>";
if ($StartRec > 1) {
    $PrevRec = max(1, $StartRec - $pg);
    print "<a href='$sFileName&StartRec=$PrevRec&pg=$pg&q=$q&by=$by&filter=$filter' class='btn btn-sm btn-outline-secondary'>&laquo; Prev</a> ";
}
if ($EndRec < $TotalRec) {
    $NextRec = $StartRec + $pg;
    print "<a href='$sFileName&StartRec=$NextRec&pg=$pg&q=$q&by=$by&filter=$filter' class='btn btn-sm btn-outline-secondary'>Next &raquo;</a>";
}
print "</div></div>";
?>

<?php if ($isAdmin): ?>
<div class="mt-2">
    <input type="submit" name="action" value="delete" class="btn btn-sm btn-danger"
           onclick="return confirm('Hapus pengajuan Draft yang dipilih?')"> Hapus Terpilih (Draft)
</div>
<?php endif; ?>

</form>
</div>

<script>
function checkAll(source) {
    var checkboxes = document.querySelectorAll('input[name="pk[]"]');
    checkboxes.forEach(cb => cb.checked = source.checked);
}
</script>
