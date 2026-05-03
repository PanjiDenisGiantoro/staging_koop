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
        $memberID      = $rs->fields('memberID');
        $jenis_pemilik = $rs->fields('jenis_pemilik') ? $rs->fields('jenis_pemilik') : 'anggota';
        $nama_pemilik  = $rs->fields('nama_pemilik');
        $no_hp_pemilik = $rs->fields('no_hp_pemilik');
        $nama_usaha    = $rs->fields('nama_usaha');
        $kategori      = $rs->fields('kategori');
        $deskripsi     = $rs->fields('deskripsi');
        $alamat        = $rs->fields('alamat');
        $no_telefon    = $rs->fields('no_telefon');
        $status        = $rs->fields('status');
        $createdDate   = toDate("d/m/Y", $rs->fields('createdDate'));

        if ($jenis_pemilik == 'anggota' && $memberID) {
            $namaAnggota = dlookup("users", "name", "userID=(SELECT userID FROM userdetails WHERE memberID=" . tosql($memberID, "Text") . " LIMIT 1)");
        }

        // Keselamatan: anggota biasa hanya boleh lihat/edit usaha sendiri
        if (!$isAdmin && $memberID != $myMemberID) {
            print '<script>alert("Akses tidak diizinkan.");window.location="' . $sListFile . '";</script>';
            exit;
        }
    }
}

if (!isset($jenis_pemilik)) $jenis_pemilik = isset($_POST['jenis_pemilik']) ? $_POST['jenis_pemilik'] : 'anggota';

// --- Simpan Baru ---
if ($action == 'Simpan') {
    $updatedBy   = get_session("Cookie_userName");
    $updatedDate = date("Y-m-d H:i:s");
    $statusBaru  = $isAdmin ? 1 : 0;

    if ($jenis_pemilik == 'non_anggota') {
        // Bukan anggota: wajib nama pemilik
        if (!$nama_pemilik || !$nama_usaha) {
            $errMsg = "Nama Pemilik dan Nama Usaha wajib diisi.";
        } else {
            $sSQL = "INSERT INTO usaha (memberID, jenis_pemilik, nama_pemilik, no_hp_pemilik, nama_usaha, kategori, deskripsi, alamat, no_telefon, status, createdDate, createdBy, updatedDate, updatedBy)
                     VALUES (NULL,"
                   . tosql('non_anggota', "Text") . ","
                   . tosql($nama_pemilik, "Text") . ","
                   . tosql($no_hp_pemilik, "Text") . ","
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
            activityLog($sSQL, "Daftar Usaha Bukan Anggota: $nama_usaha", get_session('Cookie_userID'), $updatedBy, 3);
            $msg = $isAdmin ? "Usaha berhasil didaftarkan." : "Usaha berhasil didaftarkan. Mohon tunggu persetujuan admin.";
            print '<script>alert("' . $msg . '");window.location="?vw=usaha&mn=' . $mn . '&action=view&usahaID=' . $newID . '";</script>';
            exit;
        }
    } else {
        // Anggota: pakai memberID
        $saveMemberID = $isAdmin ? $no_anggota : $myMemberID;
        if (!$saveMemberID || !$nama_usaha) {
            $errMsg = "No. Anggota dan Nama Usaha wajib diisi.";
        } else {
            $sSQL = "INSERT INTO usaha (memberID, jenis_pemilik, nama_usaha, kategori, deskripsi, alamat, no_telefon, status, createdDate, createdBy, updatedDate, updatedBy)
                     VALUES ("
                   . tosql($saveMemberID, "Text") . ","
                   . tosql('anggota', "Text") . ","
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

            <?php if ($isNew): ?>
            <!-- Toggle Anggota / Bukan Anggota -->
            <tr>
                <td width="180">Jenis Pemilik</td>
                <td></td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" id="btnAnggota"
                                class="btn <?= $jenis_pemilik=='anggota' ? 'btn-primary' : 'btn-outline-primary' ?>"
                                onclick="setJenisPemilik('anggota')">
                            <i class="mdi mdi-account-check"></i> Anggota
                        </button>
                        <button type="button" id="btnNonAnggota"
                                class="btn <?= $jenis_pemilik=='non_anggota' ? 'btn-warning' : 'btn-outline-warning' ?>"
                                onclick="setJenisPemilik('non_anggota')">
                            <i class="mdi mdi-account-outline"></i> Bukan Anggota
                        </button>
                    </div>
                    <input type="hidden" name="jenis_pemilik" id="jenis_pemilik" value="<?= $jenis_pemilik ?>">
                </td>
            </tr>

            <!-- Anggota: pilih via popup (admin) atau otomatis (non-admin) -->
            <tbody id="rowAnggota" <?= $jenis_pemilik=='non_anggota' ? 'style="display:none"' : '' ?>>
            <?php if ($isAdmin): ?>
            <tr>
                <td>* No. Anggota</td>
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
            <?php else: ?>
            <tr>
                <td>No. Anggota</td>
                <td></td>
                <td><input class="form-control-sm" type="text" value="<?= $myMemberID ?>" readonly size="15"></td>
            </tr>
            <?php endif; ?>
            </tbody>

            <!-- Bukan Anggota: isi nama & no HP -->
            <tbody id="rowNonAnggota" <?= $jenis_pemilik=='anggota' ? 'style="display:none"' : '' ?>>
            <tr>
                <td>* Nama Pemilik</td>
                <td></td>
                <td><input class="form-control-sm" type="text" name="nama_pemilik"
                           value="<?= htmlspecialchars($nama_pemilik) ?>" size="35" maxlength="100"
                           placeholder="Nama lengkap pemilik..."></td>
            </tr>
            <tr>
                <td>No. HP Pemilik</td>
                <td></td>
                <td><input class="form-control-sm" type="text" name="no_hp_pemilik"
                           value="<?= htmlspecialchars($no_hp_pemilik) ?>" size="20" maxlength="30"
                           placeholder="08xx..."></td>
            </tr>
            </tbody>

            <?php elseif (!$isNew): ?>
            <!-- View/Edit: tampilkan info pemilik -->
            <tr>
                <td width="180">Jenis Pemilik</td>
                <td></td>
                <td>
                    <?php if ($jenis_pemilik == 'non_anggota'): ?>
                        <span class="badge bg-warning text-dark">Bukan Anggota</span>
                    <?php else: ?>
                        <span class="badge bg-primary">Anggota</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($jenis_pemilik == 'non_anggota'): ?>
            <tr>
                <td>Nama Pemilik</td>
                <td></td>
                <td><b><?= htmlspecialchars($nama_pemilik) ?></b></td>
            </tr>
            <tr>
                <td>No. HP Pemilik</td>
                <td></td>
                <td><?= htmlspecialchars($no_hp_pemilik) ?></td>
            </tr>
            <?php else: ?>
            <tr>
                <td>No. Anggota</td>
                <td></td>
                <td>
                    <input class="form-control-sm" type="text" value="<?= $memberID ?>" readonly size="15">
                    &nbsp;<b><?= $namaAnggota ?></b>
                </td>
            </tr>
            <?php endif; ?>
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

<?php if ($isNew): ?>
<script>
function setJenisPemilik(jenis) {
    document.getElementById('jenis_pemilik').value = jenis;
    var isNon = jenis === 'non_anggota';

    document.getElementById('rowAnggota').style.display    = isNon ? 'none' : '';
    document.getElementById('rowNonAnggota').style.display = isNon ? '' : 'none';

    document.getElementById('btnAnggota').className    = isNon ? 'btn btn-outline-primary btn-sm' : 'btn btn-primary btn-sm';
    document.getElementById('btnNonAnggota').className = isNon ? 'btn btn-warning btn-sm'         : 'btn btn-outline-warning btn-sm';
}
</script>
<?php endif; ?>
