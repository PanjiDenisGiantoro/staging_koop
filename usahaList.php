<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : usahaList.php
 *      Modul     : Serba Usaha - Daftar Usaha
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 30;
if (!isset($q))        $q        = '';
if (!isset($by))       $by       = '1';
if (!isset($filter))   $filter   = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID  = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
$isAdmin     = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID  = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

$sFileName = "?vw=usahaList&mn=$mn";
$sFileRef  = "?vw=usaha&mn=$mn";
$title     = "Daftar Usaha";

// --- Hapus ---
if ($action == "delete" && $isAdmin) {
    for ($i = 0; $i < count($pk); $i++) {
        $sSQL = "DELETE FROM usaha WHERE usahaID=" . tosql($pk[$i], "Number");
        $conn->Execute($sSQL);
        activityLog($sSQL, "Hapus Usaha ID " . $pk[$i], get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
    }
}

// --- Approve / Reject (Admin) ---
if ($action == "approve" && $isAdmin && $usahaID) {
    $sSQL = "UPDATE usaha SET status=1, updatedDate='" . date("Y-m-d H:i:s") . "', updatedBy='" . get_session("Cookie_userName") . "' WHERE usahaID=" . tosql($usahaID, "Number");
    $conn->Execute($sSQL);
}
if ($action == "reject" && $isAdmin && $usahaID) {
    $sSQL = "UPDATE usaha SET status=2, updatedDate='" . date("Y-m-d H:i:s") . "', updatedBy='" . get_session("Cookie_userName") . "' WHERE usahaID=" . tosql($usahaID, "Number");
    $conn->Execute($sSQL);
}

// --- Query ---
$sWhere = " WHERE 1=1";
if (!$isAdmin) {
    $sWhere .= " AND a.memberID=" . tosql($myMemberID, "Text");
}
if ($q != '') {
    if ($by == 1) $sWhere .= " AND a.nama_usaha LIKE '%" . $q . "%'";
    if ($by == 2) $sWhere .= " AND a.memberID LIKE '%" . $q . "%'";
}
if ($filter != '') {
    $sWhere .= " AND a.status=" . tosql($filter, "Number");
}

$sSQL = "SELECT a.*, b.name as nama_anggota FROM usaha a
         LEFT JOIN users b ON a.memberID = (SELECT memberID FROM userdetails WHERE userID = b.userID LIMIT 1)"
       . $sWhere . " ORDER BY a.createdDate DESC";
$GetList = $conn->Execute($sSQL);
$GetList->Move($StartRec - 1);
$TotalRec  = $GetList->RowCount();
$TotalPage = ($TotalRec / $pg);

$statusLabel = array('0' => '<span class="badge bg-warning text-dark">Pending</span>',
                     '1' => '<span class="badge bg-success">Aktif</span>',
                     '2' => '<span class="badge bg-danger">Tidak Aktif</span>');
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
    <div class="col-md-6">
        Cari: <select name="by" class="form-select-sm d-inline w-auto">
            <option value="1" <?= $by==1?'selected':'' ?>>Nama Usaha</option>
            <option value="2" <?= $by==2?'selected':'' ?>>Nomor Anggota</option>
        </select>
        <input type="text" name="q" value="<?= $q ?>" maxlength="50" size="25" class="form-control-sm d-inline">
        <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
    </div>
    <div class="col-md-3">
        Status:
        <select name="filter" class="form-select-sm d-inline w-auto" onchange="document.MyForm.submit()">
            <option value="">- Semua -</option>
            <option value="0" <?= $filter==='0'?'selected':'' ?>>Pending</option>
            <option value="1" <?= $filter==='1'?'selected':'' ?>>Aktif</option>
            <option value="2" <?= $filter==='2'?'selected':'' ?>>Tidak Aktif</option>
        </select>
    </div>
    <div class="col-md-3 text-end">
        <a href="<?= $sFileRef ?>&action=new" class="btn btn-sm btn-primary">+ Daftar Usaha Baru</a>
    </div>
</div>

<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
    <thead class="table-primary">
        <tr>
            <th width="3%"><input type="checkbox" onclick="checkAll(this)"></th>
            <th width="5%">No</th>
            <?php if ($isAdmin): ?><th>No. Anggota</th><?php endif; ?>
            <th>Nama Usaha</th>
            <th>Kategori</th>
            <th>No. Telepon</th>
            <th>Status</th>
            <th>Tanggal Daftar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = $StartRec;
    $found = false;
    while (!$GetList->EOF && $i < ($StartRec + $pg)):
        $found = true;
        $usahaID   = $GetList->fields('usahaID');
        $memberID  = $GetList->fields('memberID');
        $namaUsaha = $GetList->fields('nama_usaha');
        $kategori  = $GetList->fields('kategori');
        $noTel     = $GetList->fields('no_telefon');
        $status    = $GetList->fields('status');
        $tglDaftar = toDate("d/m/Y", $GetList->fields('createdDate'));
    ?>
    <tr>
        <td><input type="checkbox" name="pk[]" value="<?= $usahaID ?>"></td>
        <td><?= $i ?></td>
        <?php if ($isAdmin): ?><td><?= $memberID ?></td><?php endif; ?>
        <td><?= $namaUsaha ?></td>
        <td><?= $kategori ?></td>
        <td><?= $noTel ?></td>
        <td><?= isset($statusLabel[$status]) ? $statusLabel[$status] : '-' ?></td>
        <td><?= $tglDaftar ?></td>
        <td nowrap>
            <a href="?vw=usaha&mn=<?= $mn ?>&action=view&usahaID=<?= $usahaID ?>" class="btn btn-xs btn-info btn-sm">Lihat</a>
            <a href="?vw=produkUsahaList&mn=<?= $mn ?>&usahaID=<?= $usahaID ?>" class="btn btn-xs btn-secondary btn-sm">Produk</a>
            <?php if ($isAdmin && $status == 0): ?>
                <a href="?vw=usahaList&mn=<?= $mn ?>&action=approve&usahaID=<?= $usahaID ?>" class="btn btn-xs btn-success btn-sm" onclick="return confirm('Approve usaha ini?')">Approve</a>
                <a href="?vw=usahaList&mn=<?= $mn ?>&action=reject&usahaID=<?= $usahaID ?>" class="btn btn-xs btn-danger btn-sm" onclick="return confirm('Reject usaha ini?')">Reject</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php
        $i++;
        $GetList->MoveNext();
    endwhile;
    if (!$found):
    ?>
    <tr><td colspan="9" class="text-center text-muted">Tidak ada data.</td></tr>
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
           onclick="return confirm('Hapus usaha yang dipilih?')"> Hapus Terpilih
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
