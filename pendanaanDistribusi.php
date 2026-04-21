<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanDistribusi.php
 *      Modul     : Pendanaan Usaha - Proses Distribusi Dana
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

// ---- Helper: Post Jurnal Pendanaan ----
function postJurnalPendanaan($conn, $jenis, $ref_tabel, $ref_id, $keterangan, $tarikh, $lines, $by_user) {
    $ckTbl = $conn->Execute("SHOW TABLES LIKE 'pendanaan_jurnal'");
    if (!$ckTbl || $ckTbl->EOF) return false;
    $prefix = 'JN-PDN-' . date('Ym', strtotime($tarikh)) . '-';
    $rsLast = $conn->Execute("SELECT no_jurnal FROM pendanaan_jurnal WHERE no_jurnal LIKE '" . $prefix . "%' ORDER BY jurnalID DESC LIMIT 1");
    if ($rsLast && !$rsLast->EOF) {
        $urut = (int) substr($rsLast->fields('no_jurnal'), -4) + 1;
    } else {
        $urut = 1;
    }
    $no_jurnal = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    $total_debit = 0; $total_kredit = 0;
    foreach ($lines as $ln) { $total_debit += (float)$ln[2]; $total_kredit += (float)$ln[3]; }
    $now = date('Y-m-d H:i:s');
    $conn->Execute("INSERT INTO pendanaan_jurnal
        (no_jurnal, jenis, ref_tabel, ref_id, keterangan, tarikh, total_debit, total_kredit, createdDate, createdBy)
        VALUES ("
        . tosql($no_jurnal, "Text") . "," . tosql($jenis, "Text") . "," . tosql($ref_tabel, "Text") . ","
        . tosql($ref_id, "Number") . "," . tosql($keterangan, "Text") . ",'" . $tarikh . "',"
        . tosql($total_debit, "Number") . "," . tosql($total_kredit, "Number") . ",'" . $now . "',"
        . tosql($by_user, "Text") . ")");
    $jurnalID = $conn->Insert_ID();
    foreach ($lines as $ln) {
        $conn->Execute("INSERT INTO pendanaan_jurnal_detail
            (jurnalID, no_akaun, nama_akaun, debit, kredit, keterangan)
            VALUES (" . tosql($jurnalID, "Number") . "," . tosql($ln[0], "Text") . "," . tosql($ln[1], "Text") . ","
            . tosql((float)$ln[2], "Number") . "," . tosql((float)$ln[3], "Number") . "," . tosql($ln[4], "Text") . ")");
    }
    return $jurnalID;
}

$isAdmin = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
if (!$isAdmin) {
    print '<script>alert("Akses tidak diizinkan.");window.location="?vw=pendanaanList&mn=' . $mn . '";</script>';
    exit;
}

$by_user   = get_session("Cookie_userName");
$sListFile = "?vw=pendanaanList&mn=$mn";

// Cek tabel kredit tersedia
$ckKrdTbl2 = $conn->Execute("SHOW TABLES LIKE 'pendanaan_kredit'");
$kreditTableExists = ($ckKrdTbl2 && !$ckKrdTbl2->EOF);

// --- Load pengajuan ---
$rsPeng = $conn->Execute("SELECT p.*, u.nama_usaha FROM pendanaan_pengajuan p
    LEFT JOIN usaha u ON p.usahaID = u.usahaID
    WHERE p.pengajuanID=" . tosql($pengajuanID, "Number") . " AND p.status=2");

if (!$rsPeng || $rsPeng->EOF) {
    print '<script>alert("Pengajuan tidak ditemukan atau belum disetujui.");window.location="' . $sListFile . '";</script>';
    exit;
}

// Cek sudah didistribusi
$cekDist = dlookup("pendanaan_distribusi", "distribusiID", "pengajuanID=" . tosql($pengajuanID, "Number"));
if ($cekDist) {
    print '<script>alert("Pengajuan ini sudah didistribusi. No. Distribusi: '
        . dlookup("pendanaan_distribusi", "no_distribusi", "pengajuanID=" . tosql($pengajuanID, "Number"))
        . '");window.location="?vw=pendanaanCicilanList&mn=' . $mn . '&distribusiID=' . $cekDist . '";</script>';
    exit;
}

$peng_usahaID  = $rsPeng->fields('usahaID');
$peng_memberID = $rsPeng->fields('memberID');
$peng_nominal  = $rsPeng->fields('nominal');
$peng_tenor    = $rsPeng->fields('tenor');
$peng_nama_usaha = $rsPeng->fields('nama_usaha');
$peng_no       = $rsPeng->fields('no_pengajuan');

// Cek saldo pool
$saldoPool = dlookup("pendanaan_pool", "saldo", "poolID=1");
if (!$saldoPool) $saldoPool = 0;

// --- Generate No Distribusi ---
function generate_no_distribusi($conn) {
    $prefix = 'DS-' . date('Ym') . '-';
    $rs = $conn->Execute("SELECT no_distribusi FROM pendanaan_distribusi
        WHERE no_distribusi LIKE '" . $prefix . "%' ORDER BY distribusiID DESC LIMIT 1");
    if ($rs && !$rs->EOF) {
        $urut = (int) substr($rs->fields('no_distribusi'), -4) + 1;
    } else {
        $urut = 1;
    }
    return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
}

// --- Proses Distribusi ---
if ($action == 'Distribusi') {
    $bunga_per_thn = (float) str_replace(',', '.', $bunga_per_thn);
    $tenor         = ((int) $tenor_dist) ? (int) $tenor_dist : (int) $peng_tenor;
    $nominal       = (float) $peng_nominal;
    $now           = date("Y-m-d H:i:s");
    $tgl_dist      = date('Y-m-d');
    $tgl_jtempo    = date('Y-m-d', strtotime("+$tenor month"));

    if ($nominal > $saldoPool) {
        $errMsg = "Saldo pool dana tidak mencukupi. Saldo: Rp " . number_format($saldoPool, 0, ',', '.');
    } elseif ($bunga_per_thn < 0 || $bunga_per_thn > 100) {
        $errMsg = "Bunga per tahun tidak valid.";
    } else {
        // Hitung cicilan flat
        $bunga_per_bln = ($nominal * ($bunga_per_thn / 100)) / 12;
        $pokok_per_bln = $nominal / $tenor;
        $cicilan_per_bln = $pokok_per_bln + $bunga_per_bln;

        $no_dist = generate_no_distribusi($conn);

        // Insert distribusi
        $conn->Execute("INSERT INTO pendanaan_distribusi
            (no_distribusi, pengajuanID, usahaID, memberID, nominal, bunga_per_thn, tenor,
             cicilan_per_bln, tgl_distribusi, tgl_jatuh_tempo, status,
             createdDate, createdBy, updatedDate, updatedBy)
            VALUES ("
            . tosql($no_dist, "Text") . ","
            . tosql($pengajuanID, "Number") . ","
            . tosql($peng_usahaID, "Number") . ","
            . tosql($peng_memberID, "Text") . ","
            . tosql($nominal, "Number") . ","
            . tosql($bunga_per_thn, "Number") . ","
            . tosql($tenor, "Number") . ","
            . tosql($cicilan_per_bln, "Number") . ","
            . "'" . $tgl_dist . "',"
            . "'" . $tgl_jtempo . "',"
            . "1,"
            . "'" . $now . "',"
            . tosql($by_user, "Text") . ","
            . "'" . $now . "',"
            . tosql($by_user, "Text") . ")");
        $distribusiID = $conn->Insert_ID();

        // Generate jadwal cicilan
        for ($i = 1; $i <= $tenor; $i++) {
            $tgl_cicilan = date('Y-m-d', strtotime("+$i month", strtotime($tgl_dist)));
            $conn->Execute("INSERT INTO pendanaan_cicilan
                (distribusiID, angsuran_ke, nominal_pokok, nominal_bunga, nominal_denda,
                 total_tagihan, tgl_jatuh_tempo, status, createdDate)
                VALUES ("
                . tosql($distribusiID, "Number") . ","
                . tosql($i, "Number") . ","
                . tosql(round($pokok_per_bln, 2), "Number") . ","
                . tosql(round($bunga_per_bln, 2), "Number") . ","
                . "0,"
                . tosql(round($cicilan_per_bln, 2), "Number") . ","
                . "'" . $tgl_cicilan . "',"
                . "0,"
                . "'" . $now . "')");
        }

        // Kurangi saldo pool
        $saldo_baru = $saldoPool - $nominal;
        $total_keluar = dlookup("pendanaan_pool", "total_keluar", "poolID=1") + $nominal;
        $conn->Execute("UPDATE pendanaan_pool SET saldo=" . tosql($saldo_baru, "Number") .
            ", total_keluar=" . tosql($total_keluar, "Number") .
            ", updatedDate='" . $now . "', updatedBy=" . tosql($by_user, "Text") . " WHERE poolID=1");

        // Log transaksi pool
        $conn->Execute("INSERT INTO pendanaan_pool_trx
            (jenis, ref_tabel, ref_id, nominal, saldo_sesudah, keterangan, createdDate, createdBy)
            VALUES ('KELUAR', 'pendanaan_distribusi', " . tosql($distribusiID, "Number") . ","
            . tosql($nominal, "Number") . ","
            . tosql($saldo_baru, "Number") . ","
            . tosql("Distribusi ke usaha: $peng_nama_usaha ($no_dist)", "Text") . ","
            . "'" . $now . "',"
            . tosql($by_user, "Text") . ")");

        // Update akun kredit (saldo_terpakai += nominal)
        $kreditID_cek = $kreditTableExists ? dlookup("pendanaan_kredit", "kreditID",
            "usahaID=" . tosql($peng_usahaID, "Number") . " AND status=1") : null;
        if ($kreditID_cek) {
            $oldTerpakai = dlookup("pendanaan_kredit", "saldo_terpakai", "kreditID=" . tosql($kreditID_cek, "Number"));
            $newTerpakai = $oldTerpakai + $nominal;
            $newTotalPinj = dlookup("pendanaan_kredit", "total_pinjaman", "kreditID=" . tosql($kreditID_cek, "Number")) + $nominal;
            $conn->Execute("UPDATE pendanaan_kredit SET
                saldo_terpakai=" . tosql($newTerpakai, "Number") . ",
                total_pinjaman=" . tosql($newTotalPinj, "Number") . ",
                updatedDate='$now', updatedBy=" . tosql($by_user, "Text") .
                " WHERE kreditID=" . tosql($kreditID_cek, "Number"));
            // Log kredit trx
            $conn->Execute("INSERT INTO pendanaan_kredit_trx
                (kreditID, usahaID, jenis, ref_tabel, ref_id, nominal, saldo_terpakai, keterangan, createdDate, createdBy)
                VALUES (" . tosql($kreditID_cek, "Number") . ","
                . tosql($peng_usahaID, "Number") . ",'DEBIT','pendanaan_distribusi',"
                . tosql($distribusiID, "Number") . ","
                . tosql($nominal, "Number") . ","
                . tosql($newTerpakai, "Number") . ","
                . tosql("Distribusi $no_dist — Rp " . number_format($nominal, 0, ',', '.'), "Text") . ","
                . "'$now'," . tosql($by_user, "Text") . ")");
        }

        // Notifikasi ke anggota
        $conn->Execute("INSERT INTO pendanaan_notifikasi
            (usahaID, memberID, distribusiID, jenis, pesan, createdDate)
            VALUES (" . tosql($peng_usahaID, "Number") . ","
            . tosql($peng_memberID, "Text") . ","
            . tosql($distribusiID, "Number") . ",'APPROVED',"
            . tosql("Dana $no_dist sebesar Rp " . number_format($nominal, 0, ',', '.') . " telah dicairkan. Cicilan mulai " . toDate("d/m/Y", $tgl_dist), "Text") . ","
            . "'$now')");

        // Post jurnal akuntansi: Distribusi
        $jrnKet = "Distribusi $no_dist kepada $peng_nama_usaha — Rp " . number_format($nominal, 0, ',', '.');
        $jrnLines = array(
            array('1-2001', 'Piutang Pendanaan Usaha', $nominal, 0,       $jrnKet),
            array('1-1001', 'Kas Pool Pendanaan',      0,        $nominal, $jrnKet),
        );
        postJurnalPendanaan($conn, 'DISTRIBUSI', 'pendanaan_distribusi', $distribusiID, $jrnKet, $tgl_dist, $jrnLines, $by_user);

        activityLog("Distribusi Dana $no_dist ke $peng_nama_usaha", "Distribusi Pendanaan", get_session('Cookie_userID'), $by_user, 3);

        print '<script>alert("Distribusi berhasil! No: ' . $no_dist . '. Jadwal cicilan ' . $tenor . ' bulan telah dibuat.");'
            . 'window.location="?vw=pendanaanCicilanList&mn=' . $mn . '&distribusiID=' . $distribusiID . '";</script>';
        exit;
    }
}

// Nilai default bunga dari setup (gunakan 0 jika tidak ada)
$defaultBunga = 0;
?>

<div class="maroon" align="left">
    <a class="maroon" href="<?= $sListFile ?>">DAFTAR PENGAJUAN</a><b>&nbsp;&gt;&nbsp;PROSES DISTRIBUSI</b>
</div>
<div>&nbsp;</div>

<?php if (!empty($errMsg)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <?= $errMsg ?>
</div>
<?php endif; ?>

<div class="alert alert-info">
    <b>Saldo Pool Dana:</b> <span class="text-success fw-bold">Rp <?= number_format($saldoPool, 0, ',', '.') ?></span>
</div>

<form name="MyForm" action="?vw=pendanaanDistribusi&mn=<?= $mn ?>" method="post">
<input type="hidden" name="pengajuanID" value="<?= $pengajuanID ?>">

<table class="table table-sm mb-3" style="max-width:600px">
<tbody>
<tr>
    <td width="200"><b>No. Pengajuan</b></td>
    <td><?= $peng_no ?></td>
</tr>
<tr>
    <td><b>Nama Usaha</b></td>
    <td><?= $peng_nama_usaha ?></td>
</tr>
<tr>
    <td><b>No. Anggota</b></td>
    <td><?= $peng_memberID ?></td>
</tr>
<tr>
    <td><b>Nominal Disetujui</b></td>
    <td><b class="text-primary">Rp <?= number_format($peng_nominal, 0, ',', '.') ?></b></td>
</tr>
<tr>
    <td><b>Tenor Pengajuan</b></td>
    <td><?= $peng_tenor ?> bulan</td>
</tr>
<tr><td colspan="2"><hr></td></tr>
<tr>
    <td>* Tenor Distribusi (bln)</td>
    <td>
        <select name="tenor_dist" class="form-select-sm" style="width:auto">
            <?php foreach (array(3,6,12,18,24,36,48,60) as $t): ?>
            <option value="<?= $t ?>" <?= ($peng_tenor == $t ? 'selected' : '') ?>><?= $t ?> bulan</option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <td>* Bunga per Tahun (%)</td>
    <td>
        <input type="text" name="bunga_per_thn" value="<?= $defaultBunga ?>"
               class="form-control-sm" style="width:80px"> %
        <small class="text-muted">(0 = tanpa bunga)</small>
    </td>
</tr>
<tr><td colspan="2"><hr></td></tr>
<tr>
    <td colspan="2">
        <input type="submit" name="action" value="Distribusi"
               class="btn btn-primary btn-sm"
               onclick="return confirm('Proses distribusi dana Rp <?= number_format($peng_nominal, 0, ',', '.') ?> ke usaha <?= addslashes($peng_nama_usaha) ?>?')">
        &nbsp;
        <a href="<?= $sListFile ?>" class="btn btn-outline-secondary btn-sm">Batal</a>
    </td>
</tr>
</tbody>
</table>
</form>
