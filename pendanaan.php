<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaan.php
 *      Modul     : Pendanaan Usaha - Form Pengajuan Pendanaan
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));
$by_user    = get_session("Cookie_userName");

$sListFile = "?vw=pendanaanList&mn=$mn";

// --- Load data jika view/edit ---
if ($action == 'view' || $action == 'edit') {
    $rs = $conn->Execute("SELECT p.*, u.nama_usaha FROM pendanaan_pengajuan p
        LEFT JOIN usaha u ON p.usahaID = u.usahaID
        WHERE p.pengajuanID=" . tosql($pengajuanID, "Number"));
    if ($rs && !$rs->EOF) {
        $usahaID      = $rs->fields('usahaID');
        $memberID     = $rs->fields('memberID');
        $nama_usaha   = $rs->fields('nama_usaha');
        $nominal      = $rs->fields('nominal');
        $tenor        = $rs->fields('tenor');
        $tujuan       = $rs->fields('tujuan');
        $no_pengajuan = $rs->fields('no_pengajuan');
        $status       = $rs->fields('status');
        $alasan_tolak = $rs->fields('alasan_tolak');
        $tgl_pengajuan = toDate("d/m/Y", $rs->fields('tgl_pengajuan'));

        if (!$isAdmin && $memberID != $myMemberID) {
            print '<script>alert("Akses tidak diizinkan.");window.location="' . $sListFile . '";</script>';
            exit;
        }
        if ($action == 'edit' && $status != 0) {
            print '<script>alert("Hanya pengajuan Draft yang boleh diedit.");window.location="' . $sListFile . '";</script>';
            exit;
        }
    }
}

// --- Generate No Pengajuan ---
function generate_no_pendanaan($conn) {
    $prefix = 'PD-' . date('Ym') . '-';
    $rs = $conn->Execute("SELECT no_pengajuan FROM pendanaan_pengajuan
        WHERE no_pengajuan LIKE '" . $prefix . "%' ORDER BY pengajuanID DESC LIMIT 1");
    if ($rs && !$rs->EOF) {
        $urut = (int) substr($rs->fields('no_pengajuan'), -4) + 1;
    } else {
        $urut = 1;
    }
    return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
}

// --- Submit (ubah ke Pending) ---
if ($action == 'Submit') {
    $now = date("Y-m-d H:i:s");
    $conn->Execute("UPDATE pendanaan_pengajuan SET status=1, updatedDate='$now', updatedBy=" .
        tosql($by_user, "Text") . " WHERE pengajuanID=" . tosql($pengajuanID, "Number") .
        " AND memberID=" . tosql($myMemberID, "Text"));
    activityLog("Submit Pengajuan ID: $pengajuanID", "Submit Pendanaan Usaha", get_session('Cookie_userID'), $by_user, 3);
    print '<script>alert("Pengajuan telah disubmit. Mohon tunggu approval admin.");window.location="' . $sListFile . '";</script>';
    exit;
}

// --- Simpan Baru ---
if ($action == 'Simpan') {
    $now = date("Y-m-d H:i:s");
    $saveMemberID = $isAdmin ? $no_anggota : $myMemberID;

    if (!$usahaID || !$nominal || !$tenor) {
        $errMsg = "Usaha, Nominal, dan Tenor wajib diisi.";
    } elseif ((float)$nominal <= 0) {
        $errMsg = "Nominal harus lebih dari 0.";
    } elseif ((int)$tenor < 1 || (int)$tenor > 60) {
        $errMsg = "Tenor harus antara 1 - 60 bulan.";
    } else {
        // Pastikan usaha milik anggota
        $cekUsaha = dlookup("usaha", "usahaID",
            "usahaID=" . tosql($usahaID, "Number") . " AND status=1");
        if (!$cekUsaha) {
            $errMsg = "Usaha tidak ditemukan atau belum aktif.";
        } else {
            $no_peng = generate_no_pendanaan($conn);
            $nominalClean = str_replace('.', '', str_replace(',', '.', $nominal));

            $sSQL = "INSERT INTO pendanaan_pengajuan
                (no_pengajuan, usahaID, memberID, nominal, tenor, tujuan, status,
                 tgl_pengajuan, createdDate, createdBy, updatedDate, updatedBy)
                VALUES ("
                . tosql($no_peng, "Text") . ","
                . tosql($usahaID, "Number") . ","
                . tosql($saveMemberID, "Text") . ","
                . tosql($nominalClean, "Number") . ","
                . tosql($tenor, "Number") . ","
                . tosql($tujuan, "Text") . ","
                . "0,"
                . "'" . date('Y-m-d') . "',"
                . "'" . $now . "',"
                . tosql($by_user, "Text") . ","
                . "'" . $now . "',"
                . tosql($by_user, "Text") . ")";

            $conn->Execute($sSQL);
            $newID = $conn->Insert_ID();
            activityLog($sSQL, "Pengajuan Pendanaan Baru: $no_peng", get_session('Cookie_userID'), $by_user, 3);

            print '<script>alert("Pengajuan pendanaan berhasil disimpan sebagai Draft.");'
                . 'window.location="?vw=pendanaan&mn=' . $mn . '&action=view&pengajuanID=' . $newID . '";</script>';
            exit;
        }
    }
}

// --- Perbarui ---
if ($action == 'Perbarui') {
    $now = date("Y-m-d H:i:s");
    if (!$nominal || !$tenor) {
        $errMsg = "Nominal dan Tenor wajib diisi.";
    } elseif ((float)$nominal <= 0) {
        $errMsg = "Nominal harus lebih dari 0.";
    } else {
        $nominalClean = str_replace('.', '', str_replace(',', '.', $nominal));
        $sSQL = "UPDATE pendanaan_pengajuan SET "
              . "nominal=" . tosql($nominalClean, "Number") . ","
              . "tenor=" . tosql($tenor, "Number") . ","
              . "tujuan=" . tosql($tujuan, "Text") . ","
              . "updatedDate='" . $now . "',"
              . "updatedBy=" . tosql($by_user, "Text")
              . " WHERE pengajuanID=" . tosql($pengajuanID, "Number")
              . " AND status=0";
        $conn->Execute($sSQL);
        activityLog($sSQL, "Perbarui Pengajuan ID: $pengajuanID", get_session('Cookie_userID'), $by_user, 3);
        print '<script>window.location="?vw=pendanaan&mn=' . $mn . '&action=view&pengajuanID=' . $pengajuanID . '";</script>';
        exit;
    }
}

$isView   = ($action == 'view');
$isNew    = ($action == 'new');
$readOnly = $isView ? 'readonly' : '';
$straction = $isNew ? 'Simpan' : 'Perbarui';

$statusLabel = array(
    '0' => '<span class="badge bg-secondary">Draft</span>',
    '1' => '<span class="badge bg-warning text-dark">Pending Approval</span>',
    '2' => '<span class="badge bg-success">Disetujui</span>',
    '3' => '<span class="badge bg-danger">Ditolak</span>',
);

// Ambil daftar usaha aktif milik anggota (untuk dropdown)
if ($isNew) {
    $saveMemberID = $isAdmin ? '' : $myMemberID;
    $qUsaha = "SELECT usahaID, nama_usaha FROM usaha WHERE status=1";
    if (!$isAdmin) $qUsaha .= " AND memberID=" . tosql($saveMemberID, "Text");
    $rsUsaha = $conn->Execute($qUsaha);
}

// Ambil detail approval jika view
if ($isView && $pengajuanID) {
    $rsApv = $conn->Execute("SELECT * FROM pendanaan_approval WHERE pengajuanID=" . tosql($pengajuanID, "Number") . " ORDER BY createdDate ASC");
}

// Cek sudah ada distribusi
$hasDistribusi = false;
if ($isView && $pengajuanID) {
    $hasDistribusi = dlookup("pendanaan_distribusi", "distribusiID", "pengajuanID=" . tosql($pengajuanID, "Number"));
}
?>

<div class="maroon" align="left">
    <a class="maroon" href="<?= $sListFile ?>">DAFTAR PENGAJUAN PENDANAAN</a><b>&nbsp;&gt;&nbsp;
    <?php print $isNew ? 'PENGAJUAN BARU' : strtoupper($no_pengajuan); ?></b>
</div>
<div>&nbsp;</div>

<?php if (!empty($errMsg)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <?= $errMsg ?>
</div>
<?php endif; ?>

<form name="MyForm" action="?vw=pendanaan&mn=<?= $mn ?>" method="post">
<input type="hidden" name="pengajuanID" value="<?= $pengajuanID ?>">
<input type="hidden" name="usahaID" id="usahaID" value="<?= $usahaID ?>">

<table class="table table-sm mb-3" width="100%">
<tbody>
<tr>
<td width="50%" valign="top">
    <table border="0" cellspacing="1" cellpadding="3">

        <?php if ($isNew && $isAdmin): ?>
        <tr>
            <td width="180">* No. Anggota</td>
            <td></td>
            <td>
                <input class="form-control-sm" type="text" name="no_anggota" id="no_anggota"
                       value="<?= $no_anggota ?>" size="15" maxlength="20" readonly>
                &nbsp;
                <input type="button" class="btn btn-sm btn-info" value="Pilih"
                       onclick="window.open('selToMember.php?refer=f','sel','top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no');">
                <span id="usahaLoading" style="display:none;font-size:11px;color:#888"> &nbsp;<i class="mdi mdi-loading mdi-spin"></i> Memuat usaha...</span>
            </td>
        </tr>
        <tr>
            <td>Nama Anggota</td>
            <td></td>
            <td><input class="form-control-sm" type="text" name="nama_anggota" id="nama_anggota"
                       value="<?= $nama_anggota ?>" size="35" readonly></td>
        </tr>
        <?php elseif (!$isNew): ?>
        <tr>
            <td width="180">No. Anggota</td>
            <td></td>
            <td><input class="form-control-sm" type="text" value="<?= $memberID ?>" readonly size="15"></td>
        </tr>
        <?php endif; ?>

        <tr>
            <td>* Usaha</td>
            <td></td>
            <td>
            <?php if ($isView): ?>
                <input class="form-control-sm" type="text" value="<?= $nama_usaha ?>" readonly size="35">
            <?php elseif ($isNew): ?>
                <select name="usahaID" id="usahaID_sel" class="form-select-sm" onchange="document.getElementById('usahaID').value=this.value" required>
                    <option value="">- Pilih Usaha -</option>
                    <?php while ($rsUsaha && !$rsUsaha->EOF):
                        $oID   = $rsUsaha->fields('usahaID');
                        $oNama = $rsUsaha->fields('nama_usaha');
                    ?>
                    <option value="<?= $oID ?>" <?= ($usahaID == $oID ? 'selected' : '') ?>><?= $oNama ?></option>
                    <?php $rsUsaha->MoveNext(); endwhile; ?>
                </select>
            <?php else: ?>
                <input class="form-control-sm" type="text" value="<?= $nama_usaha ?>" readonly size="35">
            <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>No. Pengajuan</td>
            <td></td>
            <td><input class="form-control-sm" type="text" value="<?= $isNew ? '(auto)' : $no_pengajuan ?>" readonly size="20"></td>
        </tr>
        <tr>
            <td>* Nominal (Rp)</td>
            <td></td>
            <td><input class="form-control-sm" type="text" name="nominal"
                       value="<?= $isView ? number_format($nominal, 0, ',', '.') : $nominal ?>"
                       size="20" maxlength="20" <?= $readOnly ?> placeholder="Contoh: 5000000"></td>
        </tr>
        <tr>
            <td>* Tenor (Bulan)</td>
            <td></td>
            <td>
            <?php if ($isView): ?>
                <input class="form-control-sm" type="text" value="<?= $tenor ?> bulan" readonly size="10">
            <?php else: ?>
                <select name="tenor" class="form-select-sm" style="width:auto">
                    <?php foreach (array(3,6,12,18,24,36,48,60) as $t): ?>
                    <option value="<?= $t ?>" <?= ($tenor == $t ? 'selected' : '') ?>><?= $t ?> bulan</option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            </td>
        </tr>
    </table>
</td>
<td width="50%" valign="top">
    <table border="0" cellspacing="1" cellpadding="3">
        <tr>
            <td width="140">Tujuan Penggunaan</td>
            <td></td>
            <td><textarea name="tujuan" class="form-control-sm" rows="4" cols="40"
                <?= $readOnly ?>><?= $tujuan ?></textarea></td>
        </tr>
        <?php if (!$isNew): ?>
        <tr>
            <td>Status</td>
            <td></td>
            <td><b><?= isset($statusLabel[$status]) ? $statusLabel[$status] : '-' ?></b></td>
        </tr>
        <tr>
            <td>Tgl. Pengajuan</td>
            <td></td>
            <td><?= $tgl_pengajuan ?></td>
        </tr>
        <?php if ($status == 3 && $alasan_tolak): ?>
        <tr>
            <td valign="top">Alasan Tolak</td>
            <td></td>
            <td><span class="text-danger"><?= $alasan_tolak ?></span></td>
        </tr>
        <?php endif; ?>
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
            <?php if ($status == 0): ?>
                <a href="?vw=pendanaan&mn=<?= $mn ?>&action=edit&pengajuanID=<?= $pengajuanID ?>"
                   class="btn btn-warning btn-sm">Edit</a>
                &nbsp;
                <input type="submit" name="action" value="Submit"
                       class="btn btn-success btn-sm"
                       onclick="return confirm('Submit pengajuan ini ke admin?')">
                &nbsp;
            <?php endif; ?>
            <?php if ($isAdmin && $status == 2 && !$hasDistribusi): ?>
                <a href="?vw=pendanaanDistribusi&mn=<?= $mn ?>&pengajuanID=<?= $pengajuanID ?>"
                   class="btn btn-dark btn-sm">Proses Distribusi</a>
                &nbsp;
            <?php endif; ?>
            <?php if ($hasDistribusi): ?>
                <a href="?vw=pendanaanCicilanList&mn=<?= $mn ?>&distribusiID=<?= $hasDistribusi ?>"
                   class="btn btn-info btn-sm">Lihat Cicilan</a>
                &nbsp;
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?= $sListFile ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </td>
</tr>
</tbody>
</table>
</form>

<?php if ($isNew && $isAdmin): ?>
<script>
function onMemberSelected(memberID) {
    var sel = document.getElementById('usahaID_sel');
    var loading = document.getElementById('usahaLoading');
    if (!sel) return;

    loading.style.display = 'inline';
    sel.disabled = true;
    sel.innerHTML = '<option value="">Memuat...</option>';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'getUsahaByMember.php?memberID=' + encodeURIComponent(memberID), true);
    xhr.onload = function() {
        loading.style.display = 'none';
        sel.disabled = false;
        var data = [];
        try { data = JSON.parse(xhr.responseText); } catch(e) {}

        if (data.length === 0) {
            sel.innerHTML = '<option value="">- Tidak ada usaha aktif -</option>';
        } else {
            var html = '<option value="">- Pilih Usaha -</option>';
            for (var i = 0; i < data.length; i++) {
                html += '<option value="' + data[i].usahaID + '">' + data[i].nama_usaha + '</option>';
            }
            sel.innerHTML = html;
            // Auto-select jika hanya satu usaha
            if (data.length === 1) {
                sel.value = data[0].usahaID;
                document.getElementById('usahaID').value = data[0].usahaID;
            }
        }
    };
    xhr.onerror = function() {
        loading.style.display = 'none';
        sel.disabled = false;
    };
    xhr.send();
}
</script>
<?php endif; ?>

<?php if ($isView && $pengajuanID && isset($rsApv) && !$rsApv->EOF): ?>
<div class="mt-3">
    <h6 class="card-title">Riwayat Approval</h6>
    <table class="table table-sm table-bordered" style="max-width:600px">
        <thead class="table-light">
            <tr>
                <th>Level</th>
                <th>Approver</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
        <?php while (!$rsApv->EOF):
            $apvStatus = $rsApv->fields('status');
            $apvLabel = $apvStatus == 1
                ? '<span class="badge bg-success">Disetujui</span>'
                : ($apvStatus == 2 ? '<span class="badge bg-danger">Ditolak</span>' : '<span class="badge bg-warning text-dark">Pending</span>');
        ?>
        <tr>
            <td><?= $rsApv->fields('level_approval') ?></td>
            <td><?= $rsApv->fields('approverID') ?></td>
            <td><?= $apvLabel ?></td>
            <td><?= toDate("d/m/Y H:i", $rsApv->fields('tgl_approval')) ?></td>
            <td><?= $rsApv->fields('catatan') ?></td>
        </tr>
        <?php $rsApv->MoveNext(); endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
