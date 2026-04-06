<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : stokUsaha.php
 *      Modul     : Serba Usaha - Input Stok Masuk / Keluar
 *********************************************************************************/
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

if (!$isAdmin && $ownerID != $myMemberID) {
    print '<script>alert("Akses tidak diizinkan.");history.back();</script>';
    exit;
}

// Hitung stok saat ini
$rsSaldo = $conn->Execute(
    "SELECT COALESCE(SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END), 0) as saldo
     FROM stok_usaha WHERE produkID=" . tosql($produkID, "Number")
);
$stokSemasa = $rsSaldo ? floatval($rsSaldo->fields('saldo')) : 0;

$sListFile   = "?vw=stokUsahaList&mn=$mn&produkID=$produkID&usahaID=$usahaID";
$sProdukFile = "?vw=produkUsaha&mn=$mn&action=view&produkID=$produkID&usahaID=$usahaID";
$jenisStok   = ($action == 'keluar') ? 'keluar' : 'masuk';
$title       = ($jenisStok == 'masuk') ? 'Input Stok Masuk' : 'Input Stok Keluar';

// --- Simpan ---
if ($action == 'Simpan') {
    if (!$qty || floatval($qty) <= 0) {
        $errMsg = "Kuantitas harus lebih dari 0.";
    } elseif ($jenisStok2 == 'keluar' && floatval($qty) > $stokSemasa) {
        $errMsg = "Stok tidak cukup. Stok saat ini: " . number_format($stokSemasa, 2);
    } else {
        $now       = date("Y-m-d H:i:s");
        $by        = get_session("Cookie_userName");
        $tarikhDb  = saveDateDb($tarikh ? $tarikh : date("d/m/Y"));

        if ($jenisStok2 == 'masuk') {
            $stokBaru = $stokSemasa + floatval($qty);
        } else {
            $stokBaru = $stokSemasa - floatval($qty);
        }

        $sSQL = "INSERT INTO stok_usaha (produkID, usahaID, jenis, qty, stok_akhir, harga, keterangan, tarikh, createdDate, createdBy)
                 VALUES ("
               . tosql($produkID, "Number") . ","
               . tosql($usahaID, "Number") . ","
               . tosql($jenisStok2, "Text") . ","
               . tosql($qty, "Number") . ","
               . tosql($stokBaru, "Number") . ","
               . tosql($harga ? $harga : '0', "Number") . ","
               . tosql($keterangan, "Text") . ","
               . tosql($tarikhDb, "Text") . ","
               . "'{$now}'," . tosql($by, "Text") . ")";
        $conn->Execute($sSQL);
        activityLog($sSQL, "Stok $jenisStok2 Produk: $namaProduk (Qty: $qty)", get_session('Cookie_userID'), $by, 3);

        $msg = ($jenisStok2 == 'masuk') ? "Stok masuk berhasil dicatat." : "Stok keluar berhasil dicatat.";
        print '<script>alert("' . $msg . '");window.location="' . $sListFile . '";</script>';
        exit;
    }
}

if (!$tarikh) $tarikh = date("d/m/Y");
?>

<div class="maroon" align="left">
    <a class="maroon" href="?vw=usahaList&mn=<?= $mn ?>">DAFTAR USAHA</a>
    &nbsp;&gt;&nbsp;
    <a class="maroon" href="?vw=usaha&mn=<?= $mn ?>&action=view&usahaID=<?= $usahaID ?>"><?= strtoupper($namaUsaha) ?></a>
    &nbsp;&gt;&nbsp;
    <a class="maroon" href="<?= $sProdukFile ?>"><?= strtoupper($namaProduk) ?></a>
    &nbsp;&gt;&nbsp;<b><?= strtoupper($title) ?></b>
</div>
<div>&nbsp;</div>

<?php if (!empty($errMsg)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <?= $errMsg ?>
</div>
<?php endif; ?>

<!-- Info Stok Saat Ini -->
<div class="card mb-3" style="max-width:500px">
    <div class="card-body py-2">
        <table>
            <tr>
                <td width="160"><b>Produk</b></td>
                <td>: <?= $namaProduk ?></td>
            </tr>
            <tr>
                <td><b>Usaha</b></td>
                <td>: <?= $namaUsaha ?></td>
            </tr>
            <tr>
                <td><b>Stok Saat Ini</b></td>
                <td>: <b class="<?= $stokSemasa <= 0 ? 'text-danger' : 'text-success' ?>">
                    <?= number_format($stokSemasa, 2) ?>
                </b></td>
            </tr>
        </table>
    </div>
</div>

<form name="MyForm" action="?vw=stokUsaha&mn=<?= $mn ?>" method="post">
<input type="hidden" name="produkID"   value="<?= $produkID ?>">
<input type="hidden" name="usahaID"    value="<?= $usahaID ?>">
<input type="hidden" name="jenisStok2" value="<?= $jenisStok ?>">

<table border="0" cellspacing="1" cellpadding="4" style="max-width:550px">
    <tr>
        <td width="180"><b>Jenis</b></td>
        <td>
            <span class="badge <?= $jenisStok == 'masuk' ? 'bg-success' : 'bg-danger' ?> fs-6">
                <?= strtoupper($jenisStok) ?>
            </span>
        </td>
    </tr>
    <tr>
        <td>* Tanggal</td>
        <td>
            <div class="input-group" id="tarikh_div" style="width:200px">
                <input type="text" name="tarikh" id="tarikh_input" class="form-control-sm"
                       placeholder="dd/mm/yyyy" data-provide="datepicker"
                       data-date-container="#tarikh_div" data-date-autoclose="true"
                       value="<?= $tarikh ?>">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>* Kuantitas</td>
        <td>
            <input class="form-control-sm" type="number" name="qty" value="<?= $qty ?>"
                   min="0.01" step="0.01" size="15" style="text-align:right">
        </td>
    </tr>
    <tr>
        <td>Harga Satuan (Rp)</td>
        <td>
            <input class="form-control-sm" type="number" name="harga" value="<?= $harga ?>"
                   min="0" step="0.01" size="15" style="text-align:right">
        </td>
    </tr>
    <tr>
        <td valign="top">Keterangan</td>
        <td>
            <textarea name="keterangan" class="form-control-sm" rows="3" cols="35"
                      maxlength="200"><?= $keterangan ?></textarea>
        </td>
    </tr>
    <tr>
        <td></td>
        <td class="pt-2">
            <input type="submit" name="action" value="Simpan"
                   class="btn btn-<?= $jenisStok == 'masuk' ? 'success' : 'danger' ?> btn-sm"
                   onclick="return confirm('Konfirmasi rekod stok <?= $jenisStok ?>?')">
            &nbsp;
            <a href="<?= $sProdukFile ?>" class="btn btn-outline-secondary btn-sm">Batal</a>
        </td>
    </tr>
</table>
</form>
