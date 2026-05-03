<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : produkUsaha.php
 *      Modul     : Serba Usaha - Form Tambah/Edit/View Produk
 *********************************************************************************/
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
$namaUsaha = $rsUsaha->fields('nama_usaha');
$ownerID   = $rsUsaha->fields('memberID');

if (!$isAdmin && $ownerID != $myMemberID) {
    print '<script>alert("Akses tidak diizinkan.");window.location="?vw=usahaList&mn=' . $mn . '";</script>';
    exit;
}

$sListFile = "?vw=produkUsahaList&mn=$mn&usahaID=$usahaID";
$kategoriList = array('Makanan', 'Minuman', 'Pakaian', 'Elektronik', 'Pertanian', 'Perkhidmatan', 'Lain-lain');

// --- Load data jika view/edit ---
if ($action == 'view' || $action == 'edit') {
    $rs = $conn->Execute("SELECT * FROM produk_usaha WHERE produkID=" . tosql($produkID, "Number") . " AND usahaID=" . tosql($usahaID, "Number"));
    if ($rs && !$rs->EOF) {
        $nama_produk = $rs->fields('nama_produk');
        $sku         = $rs->fields('sku');
        $barcode     = $rs->fields('barcode');
        $ledger_code = $rs->fields('ledger_code');
        $ledger_name = $rs->fields('ledger_name');
        $harga_jual        = $rs->fields('harga_jual');
        $harga_anggota     = $rs->fields('harga_anggota');
        $harga_non_anggota = $rs->fields('harga_non_anggota');
        $harga_beli        = $rs->fields('harga_beli');
        $kategori    = $rs->fields('kategori');
        $deskripsi   = $rs->fields('deskripsi');
        $status      = $rs->fields('status');
        $createdDate = toDate("d/m/Y", $rs->fields('createdDate'));
    }
}

// --- Simpan Baru ---
if ($action == 'Simpan') {
    if (!$nama_produk) {
        print '<div class="alert alert-danger">Nama produk wajib diisi.</div>';
    } else {
        $now = date("Y-m-d H:i:s");
        $by  = get_session("Cookie_userName");
        $sSQL = "INSERT INTO produk_usaha (usahaID, nama_produk, sku, barcode, ledger_code, ledger_name, harga_jual, harga_anggota, harga_non_anggota, harga_beli, kategori, deskripsi, status, createdDate, createdBy, updatedDate, updatedBy)
                 VALUES ("
               . tosql($usahaID, "Number") . ","
               . tosql($nama_produk, "Text") . ","
               . tosql($sku, "Text") . ","
               . tosql($barcode, "Text") . ","
               . tosql($ledger_code, "Text") . ","
               . tosql($ledger_name, "Text") . ","
               . tosql($harga_jual ? $harga_jual : '0', "Number") . ","
               . tosql($harga_anggota ? $harga_anggota : '0', "Number") . ","
               . tosql($harga_non_anggota ? $harga_non_anggota : '0', "Number") . ","
               . tosql($harga_beli ? $harga_beli : '0', "Number") . ","
               . tosql($kategori, "Text") . ","
               . tosql($deskripsi, "Text") . ","
               . "1,'{$now}'," . tosql($by, "Text") . ",'{$now}'," . tosql($by, "Text") . ")";
        $conn->Execute($sSQL);
        $newID = $conn->Insert_ID();
        activityLog($sSQL, "Tambah Produk: $nama_produk (Usaha: $usahaID)", get_session('Cookie_userID'), $by, 3);
        print '<script>window.location="?vw=produkUsaha&mn=' . $mn . '&action=view&produkID=' . $newID . '&usahaID=' . $usahaID . '";</script>';
        exit;
    }
}

// --- Perbarui ---
if ($action == 'Perbarui') {
    $now = date("Y-m-d H:i:s");
    $by  = get_session("Cookie_userName");
    $sSQL = "UPDATE produk_usaha SET "
           . "nama_produk=" . tosql($nama_produk, "Text") . ","
           . "sku=" . tosql($sku, "Text") . ","
           . "barcode=" . tosql($barcode, "Text") . ","
           . "ledger_code=" . tosql($ledger_code, "Text") . ","
           . "ledger_name=" . tosql($ledger_name, "Text") . ","
           . "harga_jual=" . tosql($harga_jual ? $harga_jual : '0', "Number") . ","
           . "harga_anggota=" . tosql($harga_anggota ? $harga_anggota : '0', "Number") . ","
           . "harga_non_anggota=" . tosql($harga_non_anggota ? $harga_non_anggota : '0', "Number") . ","
           . "harga_beli=" . tosql($harga_beli ? $harga_beli : '0', "Number") . ","
           . "kategori=" . tosql($kategori, "Text") . ","
           . "deskripsi=" . tosql($deskripsi, "Text") . ","
           . "status=" . tosql($status_produk ? $status_produk : '1', "Number") . ","
           . "updatedDate='{$now}',"
           . "updatedBy=" . tosql($by, "Text")
           . " WHERE produkID=" . tosql($produkID, "Number") . " AND usahaID=" . tosql($usahaID, "Number");
    $conn->Execute($sSQL);
    activityLog($sSQL, "Perbarui Produk ID: $produkID", get_session('Cookie_userID'), $by, 3);
    print '<script>window.location="?vw=produkUsaha&mn=' . $mn . '&action=view&produkID=' . $produkID . '&usahaID=' . $usahaID . '";</script>';
    exit;
}

$isView    = ($action == 'view');
$readOnly  = $isView ? 'readonly' : '';
$straction = (!$produkID || $action == 'new') ? 'Simpan' : 'Perbarui';

// Hitung stok saat ini
$saldo = 0;
if ($produkID) {
    $rsSaldo = $conn->Execute(
        "SELECT COALESCE(SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END), 0) as saldo
         FROM stok_usaha WHERE produkID=" . tosql($produkID, "Number")
    );
    if ($rsSaldo) $saldo = $rsSaldo->fields('saldo');
}
?>

<div class="maroon" align="left">
    <a class="maroon" href="?vw=usahaList&mn=<?= $mn ?>">DAFTAR USAHA</a>
    &nbsp;&gt;&nbsp;
    <a class="maroon" href="?vw=usaha&mn=<?= $mn ?>&action=view&usahaID=<?= $usahaID ?>"><?= strtoupper($namaUsaha) ?></a>
    &nbsp;&gt;&nbsp;
    <a class="maroon" href="<?= $sListFile ?>">PRODUK</a>
    &nbsp;&gt;&nbsp;<b><?= $action == 'new' ? 'TAMBAH BARU' : strtoupper($nama_produk) ?></b>
</div>
<div>&nbsp;</div>

<form name="MyForm" action="?vw=produkUsaha&mn=<?= $mn ?>" method="post">
<input type="hidden" name="usahaID"  value="<?= $usahaID ?>">
<input type="hidden" name="produkID" value="<?= $produkID ?>">

<table class="table table-sm mb-3" width="100%">
<tbody>
<tr>
    <td width="50%" valign="top">
        <table border="0" cellspacing="1" cellpadding="3">
            <tr>
                <td width="160">* Nama Produk</td><td></td>
                <td><input class="form-control-sm" type="text" name="nama_produk"
                    value="<?= $nama_produk ?>" size="40" maxlength="100" <?= $readOnly ?>></td>
            </tr>
            <tr>
                <td>SKU</td><td></td>
                <td><input class="form-control-sm" type="text" name="sku"
                    value="<?= $sku ?>" size="20" maxlength="50" <?= $readOnly ?>></td>
            </tr>
            <tr>
                <td>Barcode</td><td></td>
                <td><input class="form-control-sm" type="text" name="barcode"
                    value="<?= htmlspecialchars($barcode) ?>" size="30" maxlength="100" <?= $readOnly ?>
                    placeholder="Scan atau ketik barcode..."></td>
            </tr>
            <tr>
                <td valign="top">Kode Ledger (COA)</td><td></td>
                <td>
                <?php if ($isView): ?>
                    <input class="form-control-sm" type="text" value="<?= htmlspecialchars($ledger_code) ?>" readonly size="15">
                    <?= $ledger_name ? ' &mdash; ' . htmlspecialchars($ledger_name) : '' ?>
                <?php else: ?>
                    <div class="input-group input-group-sm" style="max-width:400px">
                        <input type="text" name="ledger_code" id="ledger_code" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($ledger_code) ?>" readonly placeholder="Pilih akun...">
                        <button type="button" class="btn btn-sm btn-info"
                                onclick="window.open('listledger.php?targetCode=ledger_code&targetName=ledger_name','selLedger','top=10,left=10,width=900,height=600,scrollbars=yes,resizable=yes')">Pilih</button>
                        <?php if ($ledger_code): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="document.getElementById('ledger_code').value='';document.getElementById('ledger_name').value='';">
                            &times;
                        </button>
                        <?php endif; ?>
                    </div>
                    <input type="text" name="ledger_name" id="ledger_name" class="form-control form-control-sm mt-1"
                           value="<?= htmlspecialchars($ledger_name) ?>" readonly placeholder="Nama akun"
                           style="max-width:400px">
                <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>* Kategori</td><td></td>
                <td>
                <?php if ($isView): ?>
                    <input class="form-control-sm" type="text" value="<?= $kategori ?>" readonly size="25">
                <?php else: ?>
                    <select name="kategori" class="form-select-sm">
                        <option value="">- Pilih -</option>
                        <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= $k ?>" <?= ($kategori == $k ? 'selected' : '') ?>><?= $k ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                </td>
            </tr>
            <?php if ($isView): ?>
            <tr>
                <td>Stok Saat Ini</td><td></td>
                <td><b class="<?= $saldo <= 0 ? 'text-danger' : 'text-success' ?>"><?= number_format($saldo, 2) ?></b></td>
            </tr>
            <?php endif; ?>
            <?php if ($isView && $barcode): ?>
            <tr>
                <td valign="top">Barcode</td><td></td>
                <td>
                    <svg id="barcodeDisplay"></svg>
                    <br>
                    <button type="button" class="btn btn-sm btn-outline-dark mt-1" onclick="printBarcode()">
                        <i class="mdi mdi-printer"></i> Cetak Barcode
                    </button>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </td>
    <td width="50%" valign="top">
        <table border="0" cellspacing="1" cellpadding="3">
            <tr>
                <td width="160">Harga Jual (Rp)</td><td></td>
                <td><input class="form-control-sm" type="text" name="harga_jual"
                    value="<?= $harga_jual ?>" size="20" maxlength="20" <?= $readOnly ?>
                    style="text-align:right"></td>
            </tr>
            <tr>
                <td>Harga Jual Anggota (Rp)</td><td></td>
                <td><input class="form-control-sm" type="text" name="harga_anggota"
                    value="<?= $harga_anggota ?>" size="20" maxlength="20" <?= $readOnly ?>
                    style="text-align:right"></td>
            </tr>
            <tr>
                <td>Harga Jual Bukan Anggota (Rp)</td><td></td>
                <td><input class="form-control-sm" type="text" name="harga_non_anggota"
                    value="<?= $harga_non_anggota ?>" size="20" maxlength="20" <?= $readOnly ?>
                    style="text-align:right"></td>
            </tr>
            <tr>
                <td>Harga Beli (Rp)</td><td></td>
                <td><input class="form-control-sm" type="text" name="harga_beli"
                    value="<?= $harga_beli ?>" size="20" maxlength="20" <?= $readOnly ?>
                    style="text-align:right"></td>
            </tr>
            <tr>
                <td valign="top">Deskripsi</td><td></td>
                <td><textarea name="deskripsi" class="form-control-sm" rows="3" cols="35"
                    <?= $readOnly ?>><?= $deskripsi ?></textarea></td>
            </tr>
            <?php if ($action == 'edit'): ?>
            <tr>
                <td>Status</td><td></td>
                <td>
                    <select name="status_produk" class="form-select-sm">
                        <option value="1" <?= $status==1?'selected':'' ?>>Aktif</option>
                        <option value="0" <?= $status==0?'selected':'' ?>>Tidak Aktif</option>
                    </select>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </td>
</tr>
<tr><td colspan="2"><hr></td></tr>
<tr>
    <td colspan="2">
        <?php if (!$isView): ?>
            <input type="submit" name="action" value="<?= $straction ?>" class="btn btn-primary btn-sm">
            &nbsp;
        <?php endif; ?>
        <?php if ($isView && $produkID): ?>
            <a href="?vw=produkUsaha&mn=<?= $mn ?>&action=edit&produkID=<?= $produkID ?>&usahaID=<?= $usahaID ?>"
               class="btn btn-warning btn-sm">Edit</a>
            &nbsp;
            <a href="?vw=stokUsaha&mn=<?= $mn ?>&action=masuk&produkID=<?= $produkID ?>&usahaID=<?= $usahaID ?>"
               class="btn btn-success btn-sm">+ Stok Masuk</a>
            &nbsp;
            <a href="?vw=stokUsaha&mn=<?= $mn ?>&action=keluar&produkID=<?= $produkID ?>&usahaID=<?= $usahaID ?>"
               class="btn btn-danger btn-sm">- Stok Keluar</a>
            &nbsp;
            <a href="?vw=stokUsahaList&mn=<?= $mn ?>&produkID=<?= $produkID ?>&usahaID=<?= $usahaID ?>"
               class="btn btn-secondary btn-sm">Riwayat Stok</a>
            &nbsp;
        <?php endif; ?>
        <a href="<?= $sListFile ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </td>
</tr>
</tbody>
</table>
</form>

<?php if ($isView && $barcode): ?>
<script src="assets/libs/JsBarcode.all.min.js"></script>
<script>
JsBarcode("#barcodeDisplay", <?= json_encode($barcode) ?>, {
    format: "CODE128",
    width: 2,
    height: 60,
    displayValue: true,
    fontSize: 14,
    margin: 8
});

function printBarcode() {
    var win = window.open('', '_blank', 'width=400,height=320,top=100,left=100');
    win.document.write('<!DOCTYPE html><html><head><title>Cetak Barcode</title><style>');
    win.document.write('body{font-family:Arial,sans-serif;text-align:center;padding:20px;margin:0}');
    win.document.write('.prod-label{font-size:13px;font-weight:bold;margin-bottom:4px}');
    win.document.write('.prod-price{font-size:12px;color:#555;margin-bottom:8px}');
    win.document.write('@media print{button{display:none}}');
    win.document.write('</style></head><body>');
    win.document.write('<div class="prod-label"><?= addslashes(htmlspecialchars($nama_produk)) ?></div>');
    win.document.write('<div class="prod-price">Rp <?= number_format($harga_jual, 0, ",", ".") ?></div>');
    win.document.write('<svg id="bcPrint"></svg>');
    win.document.write('<br><br><button onclick="window.print()">&#128438; Print</button>');
    win.document.write('<script src="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'?'https':'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>/assets/libs/JsBarcode.all.min.js"><\/script>');
    win.document.write('<script>JsBarcode("#bcPrint",<?= json_encode($barcode) ?>,{format:"AUTO",width:2,height:70,displayValue:true,fontSize:14,margin:8});window.onload=function(){setTimeout(function(){window.print();},500)};<\/script>');
    win.document.write('</body></html>');
    win.document.close();
}
</script>
<?php endif; ?>
