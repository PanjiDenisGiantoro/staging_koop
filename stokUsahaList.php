<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : stokUsahaList.php
 *      Modul     : Serba Usaha - Riwayat & Saldo Stok
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 30;
if (!isset($jenis))    $jenis    = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

// Validasi produk & usaha
$rsProduk = $conn->Execute(
    "SELECT p.*, u.nama_usaha, u.memberID as ownerID
     FROM produk_usaha p
     JOIN usaha u ON p.usahaID = u.usahaID
     WHERE p.produkID=" . tosql($produkID, "Number") . " AND p.usahaID=" . tosql($usahaID, "Number")
);
if (!$rsProduk || $rsProduk->EOF) {
    print '<script>alert("Produk tidak ditemukan.");history.back();</script>';
    exit;
}
$namaProduk = $rsProduk->fields('nama_produk');
$namaUsaha  = $rsProduk->fields('nama_usaha');
$ownerID    = $rsProduk->fields('ownerID');
$hargaJual  = $rsProduk->fields('harga_jual');

if (!$isAdmin && $ownerID != $myMemberID) {
    print '<script>alert("Akses tidak diizinkan.");history.back();</script>';
    exit;
}

// Hitung stok saat ini
$rsSaldo = $conn->Execute(
    "SELECT COALESCE(SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END), 0) as saldo,
            COALESCE(SUM(CASE WHEN jenis='masuk' THEN qty ELSE 0 END), 0) as totalMasuk,
            COALESCE(SUM(CASE WHEN jenis='keluar' THEN qty ELSE 0 END), 0) as totalKeluar
     FROM stok_usaha WHERE produkID=" . tosql($produkID, "Number")
);
$stokSemasa  = $rsSaldo ? floatval($rsSaldo->fields('saldo')) : 0;
$totalMasuk  = $rsSaldo ? floatval($rsSaldo->fields('totalMasuk')) : 0;
$totalKeluar = $rsSaldo ? floatval($rsSaldo->fields('totalKeluar')) : 0;

$sFileName   = "?vw=stokUsahaList&mn=$mn&produkID=$produkID&usahaID=$usahaID";
$sProdukFile = "?vw=produkUsaha&mn=$mn&action=view&produkID=$produkID&usahaID=$usahaID";

// --- Hapus rekod stok (admin saja) ---
if ($action == "delete" && $isAdmin) {
    for ($i = 0; $i < count($pk); $i++) {
        $sSQL = "DELETE FROM stok_usaha WHERE stokID=" . tosql($pk[$i], "Number") . " AND produkID=" . tosql($produkID, "Number");
        $conn->Execute($sSQL);
        activityLog($sSQL, "Hapus Rekod Stok ID " . $pk[$i], get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
    }
    print '<script>window.location="' . $sFileName . '";</script>';
    exit;
}

// --- Query riwayat ---
$sWhere = " WHERE produkID=" . tosql($produkID, "Number");
if ($jenis != '') $sWhere .= " AND jenis=" . tosql($jenis, "Text");

$sSQL    = "SELECT * FROM stok_usaha" . $sWhere . " ORDER BY createdDate DESC";
$GetList = $conn->Execute($sSQL);
$GetList->Move($StartRec - 1);
$TotalRec  = $GetList->RowCount();
?>

<div class="maroon" align="left">
    <a class="maroon" href="?vw=usahaList&mn=<?= $mn ?>">DAFTAR USAHA</a>
    &nbsp;&gt;&nbsp;
    <a class="maroon" href="?vw=usaha&mn=<?= $mn ?>&action=view&usahaID=<?= $usahaID ?>"><?= strtoupper($namaUsaha) ?></a>
    &nbsp;&gt;&nbsp;
    <a class="maroon" href="<?= $sProdukFile ?>"><?= strtoupper($namaProduk) ?></a>
    &nbsp;&gt;&nbsp;<b>RIWAYAT STOK</b>
</div>
<div>&nbsp;</div>

<!-- Ringkasan Stok -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body py-2 text-center">
                <div class="text-muted small">Total Masuk</div>
                <div class="fw-bold text-success fs-5"><?= number_format($totalMasuk, 2) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body py-2 text-center">
                <div class="text-muted small">Total Keluar</div>
                <div class="fw-bold text-danger fs-5"><?= number_format($totalKeluar, 2) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body py-2 text-center">
                <div class="text-muted small">Stok Saat Ini</div>
                <div class="fw-bold <?= $stokSemasa <= 0 ? 'text-danger' : 'text-primary' ?> fs-5">
                    <?= number_format($stokSemasa, 2) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-secondary">
            <div class="card-body py-2 text-center">
                <div class="text-muted small">Nilai Stok (Rp)</div>
                <div class="fw-bold text-secondary fs-5">
                    <?= number_format($stokSemasa * floatval($hargaJual), 2) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<form name="MyForm" action="<?= $sFileName ?>" method="post">
<input type="hidden" name="action">
<input type="hidden" name="usahaID"  value="<?= $usahaID ?>">
<input type="hidden" name="produkID" value="<?= $produkID ?>">

<div class="row mb-2">
    <div class="col-md-6">
        <a href="?vw=stokUsaha&mn=<?= $mn ?>&action=masuk&produkID=<?= $produkID ?>&usahaID=<?= $usahaID ?>"
           class="btn btn-success btn-sm">+ Stok Masuk</a>
        &nbsp;
        <a href="?vw=stokUsaha&mn=<?= $mn ?>&action=keluar&produkID=<?= $produkID ?>&usahaID=<?= $usahaID ?>"
           class="btn btn-danger btn-sm">- Stok Keluar</a>
    </div>
    <div class="col-md-3">
        Filter:
        <select name="jenis" class="form-select-sm d-inline w-auto" onchange="document.MyForm.submit()">
            <option value="">- Semua -</option>
            <option value="masuk"  <?= $jenis=='masuk' ?'selected':'' ?>>Masuk</option>
            <option value="keluar" <?= $jenis=='keluar'?'selected':'' ?>>Keluar</option>
        </select>
    </div>
    <div class="col-md-3 text-end">
        <?= papar_ms($pg) ?>
    </div>
</div>

<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
    <thead class="table-primary">
        <tr>
            <?php if ($isAdmin): ?><th width="3%"><input type="checkbox" onclick="checkAll(this)"></th><?php endif; ?>
            <th width="5%">No</th>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th class="text-end">Kuantitas</th>
            <th class="text-end">Harga (Rp)</th>
            <th class="text-end">Stok Akhir</th>
            <th>Keterangan</th>
            <th>Dicatat Oleh</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = $StartRec;
    $found = false;
    while (!$GetList->EOF && $i < ($StartRec + $pg)):
        $found    = true;
        $stokID   = $GetList->fields('stokID');
        $jenisTx  = $GetList->fields('jenis');
        $qty      = $GetList->fields('qty');
        $harga    = $GetList->fields('harga');
        $stokAkhr = $GetList->fields('stok_akhir');
        $ket      = $GetList->fields('keterangan');
        $createdBy= $GetList->fields('createdBy');
        $tarikh   = toDate("d/m/Y", $GetList->fields('tarikh'));
    ?>
    <tr>
        <?php if ($isAdmin): ?>
        <td><input type="checkbox" name="pk[]" value="<?= $stokID ?>"></td>
        <?php endif; ?>
        <td><?= $i ?></td>
        <td><?= $tarikh ?></td>
        <td>
            <?php if ($jenisTx == 'masuk'): ?>
                <span class="badge bg-success">MASUK</span>
            <?php else: ?>
                <span class="badge bg-danger">KELUAR</span>
            <?php endif; ?>
        </td>
        <td class="text-end"><?= number_format($qty, 2) ?></td>
        <td class="text-end"><?= number_format($harga, 2) ?></td>
        <td class="text-end"><?= number_format($stokAkhr, 2) ?></td>
        <td><?= $ket ?></td>
        <td><?= $createdBy ?></td>
    </tr>
    <?php
        $i++;
        $GetList->MoveNext();
    endwhile;
    if (!$found): ?>
    <tr><td colspan="9" class="text-center text-muted">Tidak ada data stok.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- Pagination -->
<?php
$EndRec = min($StartRec + $pg - 1, $TotalRec);
print "<div class='row mt-1'>";
print "<div class='col-md-6'>Jumlah: <b>$TotalRec</b> | Paparan: <b>$StartRec - $EndRec</b></div>";
print "<div class='col-md-6 text-end'>";
if ($StartRec > 1) {
    $PrevRec = max(1, $StartRec - $pg);
    print "<a href='$sFileName&StartRec=$PrevRec&pg=$pg&jenis=$jenis' class='btn btn-sm btn-outline-secondary'>&laquo; Prev</a> ";
}
if ($EndRec < $TotalRec) {
    $NextRec = $StartRec + $pg;
    print "<a href='$sFileName&StartRec=$NextRec&pg=$pg&jenis=$jenis' class='btn btn-sm btn-outline-secondary'>Next &raquo;</a>";
}
print "</div></div>";
?>

<?php if ($isAdmin): ?>
<div class="mt-2">
    <input type="submit" name="action" value="delete" class="btn btn-sm btn-danger"
           onclick="return confirm('Hapus rekod stok yang dipilih?')"> Hapus Terpilih
</div>
<?php endif; ?>

</form>

<div class="mt-2">
    <a href="<?= $sProdukFile ?>" class="btn btn-outline-secondary btn-sm">Kembali ke Produk</a>
</div>

<script>
function checkAll(source) {
    document.querySelectorAll('input[name="pk[]"]').forEach(cb => cb.checked = source.checked);
}
</script>
