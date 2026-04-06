<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : usaha.php
 *      Modul     : Serba Usaha - Form Tambah/Edit/View Usaha
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

$sListFile    = "?vw=usahaList&mn=$mn";
$kategoriList = array('Makanan & Minuman', 'Pakaian & Aksesori', 'Elektronik', 'Pertanian', 'Perkhidmatan', 'Kraftangan', 'Lain-lain');

// --- Load data jika view/edit ---
if ($action == 'view' || $action == 'edit') {
    $rs = $conn->Execute("SELECT * FROM usaha WHERE usahaID=" . tosql($usahaID, "Number"));
    if ($rs && !$rs->EOF) {
        $memberID    = $rs->fields('memberID');
        $nama_usaha  = $rs->fields('nama_usaha');
        $kategori    = $rs->fields('kategori');
        $deskripsi   = $rs->fields('deskripsi');
        $alamat      = $rs->fields('alamat');
        $no_telefon  = $rs->fields('no_telefon');
        $status      = $rs->fields('status');
        $createdDate = toDate("d/m/Y", $rs->fields('createdDate'));

        // Nama anggota dari tabel users
        $namaAnggota = dlookup("users", "name", "userID=(SELECT userID FROM userdetails WHERE memberID=" . tosql($memberID, "Text") . " LIMIT 1)");

        // Keselamatan: anggota biasa hanya boleh lihat/edit usaha sendiri
        if (!$isAdmin && $memberID != $myMemberID) {
            print '<script>alert("Akses tidak diizinkan.");window.location="' . $sListFile . '";</script>';
            exit;
        }
    }
}

// --- Simpan Baru ---
if ($action == 'Simpan') {
    $updatedBy   = get_session("Cookie_userName");
    $updatedDate = date("Y-m-d H:i:s");

    // Admin pilih anggota via popup (no_anggota), anggota biasa pakai ID sendiri
    $saveMemberID = $isAdmin ? $no_anggota : $myMemberID;
    $statusBaru   = $isAdmin ? 1 : 0; // Admin langsung Aktif, anggota perlu approval

    if (!$saveMemberID || !$nama_usaha) {
        $errMsg = "No. Anggota dan Nama Usaha wajib diisi.";
    } else {
        $sSQL = "INSERT INTO usaha (memberID, nama_usaha, kategori, deskripsi, alamat, no_telefon, status, createdDate, createdBy, updatedDate, updatedBy)
                 VALUES ("
               . tosql($saveMemberID, "Text") . ","
               . tosql($nama_usaha, "Text") . ","
               . tosql($kategori, "Text") . ","
               . tosql($deskripsi, "Text") . ","
               . tosql($alamat, "Text") . ","
               . tosql($no_telefon, "Text") . ","
               . tosql($statusBaru, "Number") . ","
               . "'" . $updatedDate . "',"
               . tosql($updatedBy, "Text") . ","
               . "'" . $updatedDate . "',"
               . tosql($updatedBy, "Text") . ")";

        $conn->Execute($sSQL);
        $newID = $conn->Insert_ID();
        activityLog($sSQL, "Daftar Usaha Baru: $nama_usaha", get_session('Cookie_userID'), $updatedBy, 3);

        $msg = $isAdmin ? "Usaha berhasil didaftarkan." : "Usaha berhasil didaftarkan. Mohon tunggu persetujuan admin.";
        print '<script>alert("' . $msg . '");window.location="?vw=usaha&mn=' . $mn . '&action=view&usahaID=' . $newID . '";</script>';
        exit;
    }
}

// --- Perbarui ---
if ($action == 'Perbarui') {
    $updatedBy   = get_session("Cookie_userName");
    $updatedDate = date("Y-m-d H:i:s");

    $sSQL = "UPDATE usaha SET "
           . "nama_usaha=" . tosql($nama_usaha, "Text") . ","
           . "kategori=" . tosql($kategori, "Text") . ","
           . "deskripsi=" . tosql($deskripsi, "Text") . ","
           . "alamat=" . tosql($alamat, "Text") . ","
           . "no_telefon=" . tosql($no_telefon, "Text") . ","
           . "updatedDate='" . $updatedDate . "',"
           . "updatedBy=" . tosql($updatedBy, "Text")
           . " WHERE usahaID=" . tosql($usahaID, "Number");

    $conn->Execute($sSQL);
    activityLog($sSQL, "Perbarui Usaha ID: $usahaID", get_session('Cookie_userID'), $updatedBy, 3);

    print '<script>window.location="?vw=usaha&mn=' . $mn . '&action=view&usahaID=' . $usahaID . '";</script>';
    exit;
}

$isView    = ($action == 'view');
$isNew     = ($action == 'new');
$readOnly  = $isView ? 'readonly' : '';
$straction = $isNew ? 'Simpan' : 'Perbarui';

$statusLabel = array('0' => 'Pending', '1' => 'Aktif', '2' => 'Tidak Aktif');
?>

<div class="maroon" align="left">
    <a class="maroon" href="<?= $sListFile ?>">DAFTAR USAHA</a><b>&nbsp;&gt;&nbsp;
    <?php print $isNew ? 'DAFTAR BARU' : strtoupper($nama_usaha); ?></b>
</div>
<div>&nbsp;</div>

<?php if (!empty($errMsg)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <?= $errMsg ?>
</div>
<?php endif; ?>

<form name="MyForm" action="?vw=usaha&mn=<?= $mn ?>" method="post">
<input type="hidden" name="usahaID" value="<?= $usahaID ?>">

<table class="table table-sm mb-3" width="100%">
<tbody>
<tr>
    <td width="50%" valign="top">
        <table border="0" cellspacing="1" cellpadding="3">

            <?php if ($isNew && $isAdmin): ?>
            <!-- Admin: pilih anggota via popup -->
            <tr>
                <td width="180">* No. Anggota</td>
                <td></td>
                <td>
                    <input class="form-control-sm" type="text" name="no_anggota" id="no_anggota"
                           value="<?= $no_anggota ?>" size="15" maxlength="20" readonly>
                    &nbsp;
                    <input type="button" class="btn btn-sm btn-info" value="Pilih"
                           onclick="window.open('selToMember.php?refer=f','sel','top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no');">
                </td>
            </tr>
            <tr>
                <td>Nama Anggota</td>
                <td></td>
                <td>
                    <input class="form-control-sm" type="text" name="nama_anggota" id="nama_anggota"
                           value="<?= $nama_anggota ?>" size="35" maxlength="100" readonly>
                </td>
            </tr>

            <?php elseif (!$isNew): ?>
            <!-- View/Edit: tampilkan no anggota -->
            <tr>
                <td width="180">No. Anggota</td>
                <td></td>
                <td>
                    <input class="form-control-sm" type="text" value="<?= $memberID ?>" readonly size="15">
                    &nbsp;<b><?= $namaAnggota ?></b>
                </td>
            </tr>
            <?php endif; ?>

            <tr>
                <td>* Nama Usaha</td>
                <td></td>
                <td><input class="form-control-sm" type="text" name="nama_usaha"
                           value="<?= $nama_usaha ?>" size="40" maxlength="100" <?= $readOnly ?>></td>
            </tr>
            <tr>
                <td>* Kategori</td>
                <td></td>
                <td>
                <?php if ($isView): ?>
                    <input class="form-control-sm" type="text" value="<?= $kategori ?>" readonly size="30">
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
            <tr>
                <td>No. Telepon</td>
                <td></td>
                <td><input class="form-control-sm" type="text" name="no_telefon"
                           value="<?= $no_telefon ?>" size="20" maxlength="20" <?= $readOnly ?>></td>
            </tr>
        </table>
    </td>
    <td width="50%" valign="top">
        <table border="0" cellspacing="1" cellpadding="3">
            <tr>
                <td width="120">Deskripsi</td>
                <td></td>
                <td><textarea name="deskripsi" class="form-control-sm" rows="3" cols="40"
                    <?= $readOnly ?>><?= $deskripsi ?></textarea></td>
            </tr>
            <tr>
                <td valign="top">Alamat</td>
                <td></td>
                <td><textarea name="alamat" class="form-control-sm" rows="3" cols="40"
                    <?= $readOnly ?>><?= $alamat ?></textarea></td>
            </tr>
            <?php if (!$isNew): ?>
            <tr>
                <td>Status</td>
                <td></td>
                <td><b><?= isset($statusLabel[$status]) ? $statusLabel[$status] : '-' ?></b></td>
            </tr>
            <tr>
                <td>Tanggal Daftar</td>
                <td></td>
                <td><?= $createdDate ?></td>
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
        <?php if ($isView && !$isNew): ?>
            <a href="?vw=usaha&mn=<?= $mn ?>&action=edit&usahaID=<?= $usahaID ?>"
               class="btn btn-warning btn-sm">Edit</a>
            &nbsp;
            <a href="?vw=produkUsahaList&mn=<?= $mn ?>&usahaID=<?= $usahaID ?>"
               class="btn btn-secondary btn-sm">Lihat Produk</a>
            &nbsp;
        <?php endif; ?>
        <a href="<?= $sListFile ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </td>
</tr>
</tbody>
</table>
</form>
