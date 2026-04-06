<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : produkUsahaList.php
 *      Modul     : Serba Usaha - Daftar Produk per Usaha
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 30;
if (!isset($q))        $q        = '';
if (!isset($filter))   $filter   = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

// Validasi usaha
$rsUsaha = $conn->Execute("SELECT * FROM usaha WHERE usahaID=" . tosql($usahaID, "Number"));
if (!$rsUsaha || $rsUsaha->EOF) {
    print '<script>alert("Usaha tidak ditemukan.");window.location="?vw=usahaList&mn=' . $mn . '";</script>';
    exit;
}
$namaUsaha  = $rsUsaha->fields('nama_usaha');
$ownerID    = $rsUsaha->fields('memberID');

// Keselamatan: anggota hanya boleh akses usaha sendiri
if (!$isAdmin && $ownerID != $myMemberID) {
    print '<script>alert("Akses tidak diizinkan.");window.location="?vw=usahaList&mn=' . $mn . '";</script>';
    exit;
}

$sFileName = "?vw=produkUsahaList&mn=$mn&usahaID=$usahaID";
$sFileRef  = "?vw=produkUsaha&mn=$mn&usahaID=$usahaID";
$title     = "Produk Usaha: $namaUsaha";

// --- Hapus ---
if ($action == "delete") {
    for ($i = 0; $i < count($pk); $i++) {
        $sSQL = "DELETE FROM produk_usaha WHERE produkID=" . tosql($pk[$i], "Number") . " AND usahaID=" . tosql($usahaID, "Number");
        $conn->Execute($sSQL);
        activityLog($sSQL, "Hapus Produk ID " . $pk[$i], get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
    }
}

// --- Query ---
$sWhere = " WHERE a.usahaID=" . tosql($usahaID, "Number");
if ($q != '') $sWhere .= " AND a.nama_produk LIKE '%" . $q . "%'";
if ($filter != '') $sWhere .= " AND a.status=" . tosql($filter, "Number");

$sSQL    = "SELECT * FROM produk_usaha a" . $sWhere . " ORDER BY a.createdDate DESC";
$GetList = $conn->Execute($sSQL);
$GetList->Move($StartRec - 1);
$TotalRec  = $GetList->RowCount();
$TotalPage = ($TotalRec / $pg);

$statusLabel = array('1' => '<span class="badge bg-success">Aktif</span>',
                     '0' => '<span class="badge bg-secondary">Tidak Aktif</span>');
?>

<div class="maroon" align="left">
    <a class="maroon" href="?vw=usahaList&mn=<?= $mn ?>">DAFTAR USAHA</a>
    &nbsp;&gt;&nbsp;
    <a class="maroon" href="?vw=usaha&mn=<?= $mn ?>&action=view&usahaID=<?= $usahaID ?>"><?= strtoupper($namaUsaha) ?></a>
    &nbsp;&gt;&nbsp;<b>PRODUK</b>
</div>
<div>&nbsp;</div>

<form name="MyForm" action="<?= $sFileName ?>" method="post">
<input type="hidden" name="action">
<input type="hidden" name="usahaID" value="<?= $usahaID ?>">

<h5 class="card-title"><?= strtoupper($title) ?></h5>

<div class="row mb-2">
    <div class="col-md-6">
        Cari nama produk:
        <input type="text" name="q" value="<?= $q ?>" maxlength="50" size="25" class="form-control-sm d-inline">
        <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
    </div>
    <div class="col-md-3">
        Status:
        <select name="filter" class="form-select-sm d-inline w-auto" onchange="document.MyForm.submit()">
            <option value="">- Semua -</option>
            <option value="1" <?= $filter==='1'?'selected':'' ?>>Aktif</option>
            <option value="0" <?= $filter==='0'?'selected':'' ?>>Tidak Aktif</option>
        </select>
    </div>
    <div class="col-md-3 text-end">
        <a href="<?= $sFileRef ?>&action=new" class="btn btn-sm btn-primary">+ Tambah Produk</a>
    </div>
</div>

<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
    <thead class="table-primary">
        <tr>
            <th width="3%"><input type="checkbox" onclick="checkAll(this)"></th>
            <th width="5%">No</th>
            <th>Nama Produk</th>
            <th>SKU</th>
            <th>Kategori</th>
            <th class="text-end">Harga Jual (Rp)</th>
            <th class="text-end">Harga Beli (Rp)</th>
            <th class="text-end">Stok</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = $StartRec;
    $found = false;
    while (!$GetList->EOF && $i < ($StartRec + $pg)):
        $found = true;
        $produkID    = $GetList->fields('produkID');
        $namaProduk  = $GetList->fields('nama_produk');
        $sku         = $GetList->fields('sku');
        $kategori    = $GetList->fields('kategori');
        $hargaJual   = $GetList->fields('harga_jual');
        $hargaBeli   = $GetList->fields('harga_beli');
        $statusProd  = $GetList->fields('status');

        // Hitung stok terkini
        $rsSaldo = $conn->Execute(
            "SELECT COALESCE(SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END), 0) as saldo
             FROM stok_usaha WHERE produkID=" . tosql($produkID, "Number")
        );
        $saldo = $rsSaldo ? number_format($rsSaldo->fields('saldo'), 2) : '0.00';
    ?>
    <tr>
        <td><input type="checkbox" name="pk[]" value="<?= $produkID ?>"></td>
        <td><?= $i ?></td>
        <td><?= $namaProduk ?></td>
        <td><?= $sku ?></td>
        <td><?= $kategori ?></td>
        <td class="text-end"><?= number_format($hargaJual, 2) ?></td>
        <td class="text-end"><?= number_format($hargaBeli, 2) ?></td>
        <td class="text-end"><?= $saldo ?></td>
        <td><?= isset($statusLabel[$statusProd]) ? $statusLabel[$statusProd] : '-' ?></td>
        <td nowrap>
            <a href="?vw=produkUsaha&mn=<?= $mn ?>&action=view&produkID=<?= $produkID ?>&usahaID=<?= $usahaID ?>"
               class="btn btn-sm btn-info">Lihat</a>
            <a href="?vw=stokUsahaList&mn=<?= $mn ?>&produkID=<?= $produkID ?>&usahaID=<?= $usahaID ?>"
               class="btn btn-sm btn-secondary">Stok</a>
        </td>
    </tr>
    <?php
        $i++;
        $GetList->MoveNext();
    endwhile;
    if (!$found): ?>
    <tr><td colspan="10" class="text-center text-muted">Tidak ada produk. Klik "+ Tambah Produk" untuk memulai.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- Pagination -->
<?php
$EndRec = min($StartRec + $pg - 1, $TotalRec);
print "<div class='row'>";
print "<div class='col-md-6'>Jumlah: <b>$TotalRec</b> | Paparan: <b>$StartRec - $EndRec</b>&nbsp;&nbsp;" . papar_ms($pg) . "</div>";
print "<div class='col-md-6 text-end'>";
if ($StartRec > 1) {
    $PrevRec = max(1, $StartRec - $pg);
    print "<a href='$sFileName&StartRec=$PrevRec&pg=$pg&q=$q&filter=$filter' class='btn btn-sm btn-outline-secondary'>&laquo; Prev</a> ";
}
if ($EndRec < $TotalRec) {
    $NextRec = $StartRec + $pg;
    print "<a href='$sFileName&StartRec=$NextRec&pg=$pg&q=$q&filter=$filter' class='btn btn-sm btn-outline-secondary'>Next &raquo;</a>";
}
print "</div></div>";
?>

<div class="mt-2">
    <input type="submit" name="action" value="delete" class="btn btn-sm btn-danger"
           onclick="return confirm('Hapus produk yang dipilih?')"> Hapus Terpilih
    &nbsp;
    <a href="?vw=usaha&mn=<?= $mn ?>&action=view&usahaID=<?= $usahaID ?>"
       class="btn btn-sm btn-outline-secondary">Kembali ke Usaha</a>
</div>

</form>

<script>
function checkAll(source) {
    document.querySelectorAll('input[name="pk[]"]').forEach(cb => cb.checked = source.checked);
}
</script>
