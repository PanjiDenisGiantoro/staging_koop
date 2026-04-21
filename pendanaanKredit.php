<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanKredit.php
 *      Modul     : Pendanaan Usaha - Form Akun Kredit
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
if (!$isAdmin) {
    print '<script>alert("Akses tidak diizinkan.");window.location="?vw=pendanaanMonitoring&mn=' . $mn . '";</script>';
    exit;
}

$by_user   = get_session("Cookie_userName");
$sListFile = "?vw=pendanaanKreditList&mn=$mn";

// --- Generate No Akun ---
function generate_no_akun($conn) {
    $prefix = 'KPD-' . date('Y') . '-';
    $rs = $conn->Execute("SELECT no_akun FROM pendanaan_kredit
        WHERE no_akun LIKE '$prefix%' ORDER BY kreditID DESC LIMIT 1");
    if ($rs && !$rs->EOF) {
        $urut = (int) substr($rs->fields('no_akun'), -4) + 1;
    } else {
        $urut = 1;
    }
    return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
}

// --- Load data view/edit ---
if ($action == 'view' || $action == 'edit') {
    $rs = $conn->Execute("SELECT k.*, u.nama_usaha FROM pendanaan_kredit k
        LEFT JOIN usaha u ON k.usahaID = u.usahaID
        WHERE k.kreditID=" . tosql($kreditID, "Number"));
    if ($rs && !$rs->EOF) {
        $usahaID        = $rs->fields('usahaID');
        $memberID       = $rs->fields('memberID');
        $no_akun        = $rs->fields('no_akun');
        $nama_usaha     = $rs->fields('nama_usaha');
        $limit_kredit   = $rs->fields('limit_kredit');
        $saldo_terpakai = $rs->fields('saldo_terpakai');
        $skor_kredit    = $rs->fields('skor_kredit');
        $status         = $rs->fields('status');
        $tgl_berlaku    = $rs->fields('tgl_berlaku');
        $tgl_kadaluarsa = $rs->fields('tgl_kadaluarsa');
        $catatan        = $rs->fields('catatan');
    }
}

// --- Simpan Baru ---
if ($action == 'Simpan') {
    $now          = date("Y-m-d H:i:s");
    $limitClean   = (float) str_replace('.', '', $limit_kredit);
    $saveMemberID = $no_anggota;

    if (!$usahaID || $limitClean <= 0) {
        $errMsg = "Usaha dan Limit Kredit wajib diisi.";
    } else {
        // Cek usaha belum punya akun kredit aktif
        $cek = dlookup("pendanaan_kredit", "kreditID",
            "usahaID=" . tosql($usahaID, "Number") . " AND status IN (0,1,2)");
        if ($cek) {
            $errMsg = "Usaha ini sudah memiliki akun kredit aktif.";
        } else {
            $noAkun = generate_no_akun($conn);
            $conn->Execute("INSERT INTO pendanaan_kredit
                (no_akun, usahaID, memberID, limit_kredit, saldo_terpakai, skor_kredit, status,
                 tgl_berlaku, tgl_kadaluarsa, catatan, createdDate, createdBy, updatedDate, updatedBy)
                VALUES ("
                . tosql($noAkun, "Text") . ","
                . tosql($usahaID, "Number") . ","
                . tosql($saveMemberID, "Text") . ","
                . tosql($limitClean, "Number") . ","
                . "0, 100, 1,"
                . tosql($tgl_berlaku ? $tgl_berlaku : date('Y-m-d'), "Text") . ","
                . tosql($tgl_kadaluarsa ? $tgl_kadaluarsa : '', "Text") . ","
                . tosql($catatan, "Text") . ","
                . "'" . $now . "',"
                . tosql($by_user, "Text") . ","
                . "'" . $now . "',"
                . tosql($by_user, "Text") . ")");
            $newID = $conn->Insert_ID();
            activityLog("Buka Akun Kredit $noAkun", "Buka Akun Kredit Pendanaan", get_session('Cookie_userID'), $by_user, 3);
            print '<script>alert("Akun kredit berhasil dibuka: ' . $noAkun . '");window.location="?vw=pendanaanKredit&mn=' . $mn . '&action=view&kreditID=' . $newID . '";</script>';
            exit;
        }
    }
}

// --- Perbarui Limit ---
if ($action == 'Perbarui') {
    $now        = date("Y-m-d H:i:s");
    $limitClean = (float) str_replace('.', '', $limit_kredit);
    if ($limitClean <= 0) {
        $errMsg = "Limit kredit harus lebih dari 0.";
    } elseif ($limitClean < (float)$saldo_terpakai_hidden) {
        $errMsg = "Limit baru tidak boleh lebih kecil dari saldo terpakai saat ini.";
    } else {
        $conn->Execute("UPDATE pendanaan_kredit SET
            limit_kredit=" . tosql($limitClean, "Number") . ","
            . "tgl_berlaku=" . tosql($tgl_berlaku, "Text") . ","
            . "tgl_kadaluarsa=" . tosql($tgl_kadaluarsa, "Text") . ","
            . "catatan=" . tosql($catatan, "Text") . ","
            . "updatedDate='" . $now . "',"
            . "updatedBy=" . tosql($by_user, "Text")
            . " WHERE kreditID=" . tosql($kreditID, "Number"));
        activityLog("Update Limit Kredit ID: $kreditID → Rp $limitClean", "Update Akun Kredit", get_session('Cookie_userID'), $by_user, 3);
        print '<script>window.location="?vw=pendanaanKredit&mn=' . $mn . '&action=view&kreditID=' . $kreditID . '";</script>';
        exit;
    }
}

$isView  = ($action == 'view');
$isNew   = ($action == 'new');
$rdOnly  = $isView ? 'readonly' : '';
$strAction = $isNew ? 'Simpan' : 'Perbarui';

$statusLabel = array('0'=>'Pending','1'=>'Aktif','2'=>'Suspend','3'=>'Nonaktif');

// Dropdown usaha aktif (hanya yang belum punya akun kredit)
if ($isNew) {
    $rsUsaha = $conn->Execute("SELECT u.usahaID, u.nama_usaha, u.memberID FROM usaha u
        WHERE u.status=1
        AND u.usahaID NOT IN (SELECT usahaID FROM pendanaan_kredit WHERE status IN (0,1,2))
        ORDER BY u.nama_usaha ASC");
}

// Load histori transaksi kredit (untuk view)
if ($isView && $kreditID) {
    $rsTrx = $conn->Execute("SELECT * FROM pendanaan_kredit_trx
        WHERE kreditID=" . tosql($kreditID, "Number") . " ORDER BY createdDate DESC LIMIT 20");
}

$saldaTersedia = $limit_kredit - $saldo_terpakai;
$pctPakai = ($limit_kredit > 0) ? round(($saldo_terpakai / $limit_kredit) * 100, 1) : 0;
$barClass = $pctPakai < 50 ? 'bg-success' : ($pctPakai < 80 ? 'bg-warning' : 'bg-danger');
?>

<div class="maroon">
    <a class="maroon" href="<?= $sListFile ?>">AKUN KREDIT</a>
    <b>&nbsp;&gt;&nbsp;<?= $isNew ? 'BUKA AKUN BARU' : $no_akun ?></b>
</div>
<div>&nbsp;</div>

<?php if (!empty($errMsg)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <?= $errMsg ?>
</div>
<?php endif; ?>

<?php if ($isView && !$isNew): ?>
<!-- === VISUAL KARTU KREDIT === -->
<div class="row mb-3">
    <div class="col-md-5">
        <div style="background: linear-gradient(135deg, #1a3a5c 0%, #2d6a9f 60%, #1abc9c 100%);
                    border-radius:16px; padding:24px 28px; color:#fff;
                    min-height:180px; position:relative; box-shadow:0 8px 24px rgba(0,0,0,0.3);">
            <!-- chip -->
            <div style="width:42px;height:32px;background:linear-gradient(135deg,#f9d423,#e0a800);
                        border-radius:5px;margin-bottom:14px;"></div>
            <div style="font-size:1.15em;letter-spacing:3px;font-weight:600;margin-bottom:6px;">
                <?= wordwrap($no_akun, 4, ' ', true) ?>
            </div>
            <div style="font-size:0.95em;opacity:0.85;margin-bottom:4px;"><?= strtoupper($nama_usaha) ?></div>
            <div style="font-size:0.8em;opacity:0.7">No. Anggota: <?= $memberID ?></div>
            <!-- logo pojok kanan -->
            <div style="position:absolute;right:24px;top:20px;font-size:2em;opacity:0.6">
                <i class="mdi mdi-credit-card"></i>
            </div>
            <!-- masa berlaku -->
            <?php if ($tgl_kadaluarsa): ?>
            <div style="position:absolute;right:24px;bottom:24px;font-size:0.78em;opacity:0.7">
                BERLAKU S/D<br><b><?= toDate("m/Y", $tgl_kadaluarsa) ?></b>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-body p-3">
                <table class="table table-sm mb-2">
                <tr>
                    <td width="130">Limit Kredit</td>
                    <td><b class="text-primary fs-6">Rp <?= number_format($limit_kredit, 0, ',', '.') ?></b></td>
                </tr>
                <tr>
                    <td>Terpakai</td>
                    <td><b class="text-danger">Rp <?= number_format($saldo_terpakai, 0, ',', '.') ?></b></td>
                </tr>
                <tr>
                    <td>Tersedia</td>
                    <td><b class="text-success">Rp <?= number_format($saldaTersedia, 0, ',', '.') ?></b></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><?= isset($statusLabel[$status]) ? '<b>' . $statusLabel[$status] . '</b>' : '-' ?></td>
                </tr>
                <tr>
                    <td>Skor Kredit</td>
                    <td>
                        <?php
                        $skorClass = $skor_kredit >= 80 ? 'text-success' : ($skor_kredit >= 60 ? 'text-warning' : 'text-danger');
                        $skorLabel = $skor_kredit >= 80 ? 'Baik' : ($skor_kredit >= 60 ? 'Perhatian' : 'Berisiko');
                        ?>
                        <b class="<?= $skorClass ?>"><?= $skor_kredit ?>/100</b>
                        <span class="badge <?= $skor_kredit >= 80 ? 'bg-success' : ($skor_kredit >= 60 ? 'bg-warning text-dark' : 'bg-danger') ?>"><?= $skorLabel ?></span>
                    </td>
                </tr>
                </table>
                <!-- Utilisasi bar -->
                <div class="mb-1"><small>Utilisasi Kredit: <b><?= $pctPakai ?>%</b></small></div>
                <div class="progress" style="height:12px">
                    <div class="progress-bar <?= $barClass ?> progress-bar-striped" style="width:<?= $pctPakai ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Form -->
<form name="MyForm" action="?vw=pendanaanKredit&mn=<?= $mn ?>" method="post">
<input type="hidden" name="kreditID" value="<?= $kreditID ?>">
<input type="hidden" name="saldo_terpakai_hidden" value="<?= $saldo_terpakai ?>">

<table class="table table-sm mb-3" style="max-width:650px">
<tbody>
    <?php if ($isNew): ?>
    <tr>
        <td width="170">* No. Anggota</td>
        <td>
            <input class="form-control-sm" type="text" name="no_anggota" id="no_anggota"
                   value="<?= $no_anggota ?>" size="15" maxlength="20" readonly>
            &nbsp;
            <input type="button" class="btn btn-sm btn-info" value="Pilih"
                   onclick="window.open('selToMember.php?refer=f','sel','top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes');">
        </td>
    </tr>
    <tr>
        <td>Nama Anggota</td>
        <td><input class="form-control-sm" type="text" name="nama_anggota" id="nama_anggota"
                   value="<?= $nama_anggota ?>" size="35" readonly></td>
    </tr>
    <tr>
        <td>* Usaha</td>
        <td>
            <select name="usahaID" class="form-select-sm" required onchange="setMember(this)">
                <option value="">- Pilih Usaha -</option>
                <?php while ($rsUsaha && !$rsUsaha->EOF):
                    $oID = $rsUsaha->fields('usahaID');
                    $oNama = $rsUsaha->fields('nama_usaha');
                    $oMember = $rsUsaha->fields('memberID');
                ?>
                <option value="<?= $oID ?>" data-member="<?= $oMember ?>"
                    <?= ($usahaID==$oID?'selected':'') ?>><?= $oNama ?></option>
                <?php $rsUsaha->MoveNext(); endwhile; ?>
            </select>
        </td>
    </tr>
    <?php else: ?>
    <tr>
        <td width="170">No. Akun</td>
        <td><input class="form-control-sm" type="text" value="<?= $no_akun ?>" readonly size="20"></td>
    </tr>
    <tr>
        <td>Nama Usaha</td>
        <td><input class="form-control-sm" type="text" value="<?= $nama_usaha ?>" readonly size="35"></td>
    </tr>
    <tr>
        <td>No. Anggota</td>
        <td><input class="form-control-sm" type="text" value="<?= $memberID ?>" readonly size="15"></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td>* Limit Kredit (Rp)</td>
        <td>
            <input class="form-control-sm" type="text" name="limit_kredit"
                   value="<?= $isView ? number_format($limit_kredit,0,',','.') : $limit_kredit ?>"
                   size="20" <?= $rdOnly ?> placeholder="Contoh: 50000000">
        </td>
    </tr>
    <tr>
        <td>Berlaku Mulai</td>
        <td><input class="form-control-sm" type="date" name="tgl_berlaku"
                   value="<?= $tgl_berlaku ? $tgl_berlaku : date('Y-m-d') ?>" <?= $rdOnly ?>></td>
    </tr>
    <tr>
        <td>Berlaku Sampai</td>
        <td><input class="form-control-sm" type="date" name="tgl_kadaluarsa"
                   value="<?= $tgl_kadaluarsa ?>" <?= $rdOnly ?>></td>
    </tr>
    <tr>
        <td valign="top">Catatan</td>
        <td><textarea name="catatan" class="form-control-sm" rows="2" cols="35"
            <?= $rdOnly ?>><?= $catatan ?></textarea></td>
    </tr>
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td colspan="2">
            <?php if (!$isView): ?>
                <input type="submit" name="action" value="<?= $strAction ?>" class="btn btn-primary btn-sm">
                &nbsp;
            <?php endif; ?>
            <?php if ($isView && !$isNew): ?>
                <a href="?vw=pendanaanKredit&mn=<?= $mn ?>&action=edit&kreditID=<?= $kreditID ?>"
                   class="btn btn-warning btn-sm">Edit Limit</a>
                &nbsp;
                <a href="?vw=pendanaanMonitoring&mn=<?= $mn ?>&usahaID=<?= $usahaID ?>"
                   class="btn btn-info btn-sm">Monitoring</a>
                &nbsp;
            <?php endif; ?>
            <a href="<?= $sListFile ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </td>
    </tr>
</tbody>
</table>
</form>

<?php if ($isView && isset($rsTrx) && !$rsTrx->EOF): ?>
<h6 class="card-title mt-2">Histori Mutasi Kredit (20 Terakhir)</h6>
<div class="table-responsive" style="max-width:700px">
<table class="table table-sm table-bordered table-striped">
    <thead class="table-light">
        <tr>
            <th>Jenis</th>
            <th>Keterangan</th>
            <th class="text-end">Nominal</th>
            <th class="text-end">Saldo Terpakai</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
    <?php while (!$rsTrx->EOF):
        $jns = $rsTrx->fields('jenis');
        $jnsLabel = $jns == 'DEBIT'
            ? '<span class="badge bg-danger">PAKAI</span>'
            : '<span class="badge bg-success">BAYAR</span>';
    ?>
    <tr>
        <td><?= $jnsLabel ?></td>
        <td><?= $rsTrx->fields('keterangan') ?></td>
        <td class="text-end">Rp <?= number_format($rsTrx->fields('nominal'), 0, ',', '.') ?></td>
        <td class="text-end">Rp <?= number_format($rsTrx->fields('saldo_terpakai'), 0, ',', '.') ?></td>
        <td><?= toDate("d/m/Y H:i", $rsTrx->fields('createdDate')) ?></td>
    </tr>
    <?php $rsTrx->MoveNext(); endwhile; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<script>
function setMember(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.member) {
        document.getElementById('no_anggota').value = opt.dataset.member;
    }
}
</script>
