<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanCicilanList.php
 *      Modul     : Pendanaan Usaha - Jadwal Cicilan & Pembayaran
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

$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));
$by_user    = get_session("Cookie_userName");

// Cek tabel kredit tersedia (opsional, fitur skor/notifikasi)
$ckKrdTbl = $conn->Execute("SHOW TABLES LIKE 'pendanaan_kredit'");
$kreditTableExists = ($ckKrdTbl && !$ckKrdTbl->EOF);

// --- Load distribusi ---
$rsDist = $conn->Execute("SELECT d.*, u.nama_usaha, p.no_pengajuan
    FROM pendanaan_distribusi d
    LEFT JOIN usaha u ON d.usahaID = u.usahaID
    LEFT JOIN pendanaan_pengajuan p ON d.pengajuanID = p.pengajuanID
    WHERE d.distribusiID=" . tosql($distribusiID, "Number"));

if (!$rsDist || $rsDist->EOF) {
    print '<script>alert("Data distribusi tidak ditemukan.");window.location="?vw=pendanaanList&mn=' . $mn . '";</script>';
    exit;
}

$dist_memberID    = $rsDist->fields('memberID');
$dist_usahaID     = $rsDist->fields('usahaID');
$dist_namaUsaha   = $rsDist->fields('nama_usaha');
$dist_noPeng      = $rsDist->fields('no_pengajuan');
$dist_noDist      = $rsDist->fields('no_distribusi');
$dist_nominal     = $rsDist->fields('nominal');
$dist_bunga       = $rsDist->fields('bunga_per_thn');
$dist_tenor       = $rsDist->fields('tenor');
$dist_cicilan_bln = $rsDist->fields('cicilan_per_bln');
$dist_tgl         = toDate("d/m/Y", $rsDist->fields('tgl_distribusi'));
$dist_jtempo      = toDate("d/m/Y", $rsDist->fields('tgl_jatuh_tempo'));
$dist_status      = $rsDist->fields('status');

if (!$isAdmin && $dist_memberID != $myMemberID) {
    print '<script>alert("Akses tidak diizinkan.");window.location="?vw=pendanaanList&mn=' . $mn . '";</script>';
    exit;
}

// --- Proses Bayar Cicilan ---
if ($action == 'Bayar' && $isAdmin && $cicilanID) {
    $now        = date("Y-m-d H:i:s");
    $tgl_bayar  = date('Y-m-d');
    $nominalBayar = (float) str_replace('.', '', str_replace(',', '.', $nominal_bayar));

    // Ambil info cicilan
    $rsCil = $conn->Execute("SELECT * FROM pendanaan_cicilan WHERE cicilanID=" . tosql($cicilanID, "Number") .
        " AND distribusiID=" . tosql($distribusiID, "Number"));
    if ($rsCil && !$rsCil->EOF && $rsCil->fields('status') != 1) {
        $total_tagihan  = $rsCil->fields('total_tagihan');
        $nominal_pokok  = $rsCil->fields('nominal_pokok');
        $angsuran_ke    = $rsCil->fields('angsuran_ke');

        // Hitung denda jika telat (0.1% per hari)
        $tgl_jt = $rsCil->fields('tgl_jatuh_tempo');
        $hari_telat = max(0, (int) floor((strtotime($tgl_bayar) - strtotime($tgl_jt)) / 86400));
        $nominal_denda = round($total_tagihan * 0.001 * $hari_telat, 2);
        $total_bayar   = $total_tagihan + $nominal_denda;
        $statusCicilan = ($hari_telat > 0) ? 2 : 1; // 2=Telat, 1=Lunas → sama-sama update ke Lunas setelah bayar

        // Insert pembayaran
        $conn->Execute("INSERT INTO pendanaan_bayar
            (cicilanID, distribusiID, nominal, metode_bayar, no_referensi, tgl_bayar, keterangan, createdDate, createdBy)
            VALUES ("
            . tosql($cicilanID, "Number") . ","
            . tosql($distribusiID, "Number") . ","
            . tosql(($nominalBayar ? $nominalBayar : $total_bayar), "Number") . ","
            . tosql($metode_bayar, "Text") . ","
            . tosql($no_referensi, "Text") . ","
            . "'" . $tgl_bayar . "',"
            . tosql($keterangan_bayar, "Text") . ","
            . "'" . $now . "',"
            . tosql($by_user, "Text") . ")");

        // Update cicilan → Lunas
        $conn->Execute("UPDATE pendanaan_cicilan SET status=1, tgl_bayar='" . $tgl_bayar .
            "', nominal_bayar=" . tosql(($nominalBayar ? $nominalBayar : $total_bayar), "Number") .
            ", nominal_denda=" . tosql($nominal_denda, "Number") .
            ", updatedDate='" . $now . "', updatedBy=" . tosql($by_user, "Text") .
            " WHERE cicilanID=" . tosql($cicilanID, "Number"));

        // Kembalikan pokok ke pool dana
        $saldoPool     = dlookup("pendanaan_pool", "saldo", "poolID=1") + $nominal_pokok;
        $total_masuk   = dlookup("pendanaan_pool", "total_masuk", "poolID=1") + $nominal_pokok;
        $conn->Execute("UPDATE pendanaan_pool SET saldo=" . tosql($saldoPool, "Number") .
            ", total_masuk=" . tosql($total_masuk, "Number") .
            ", updatedDate='" . $now . "', updatedBy=" . tosql($by_user, "Text") . " WHERE poolID=1");
        $conn->Execute("INSERT INTO pendanaan_pool_trx
            (jenis, ref_tabel, ref_id, nominal, saldo_sesudah, keterangan, createdDate, createdBy)
            VALUES ('MASUK', 'pendanaan_cicilan', " . tosql($cicilanID, "Number") . ","
            . tosql($nominal_pokok, "Number") . ","
            . tosql($saldoPool, "Number") . ","
            . tosql("Repayment cicilan ke-$angsuran_ke dari $dist_noDist", "Text") . ","
            . "'" . $now . "',"
            . tosql($by_user, "Text") . ")");

        // Update akun kredit (kurangi saldo_terpakai += pokok yang kembali)
        $krdID = $kreditTableExists ? dlookup("pendanaan_kredit", "kreditID",
            "usahaID=(SELECT usahaID FROM pendanaan_distribusi WHERE distribusiID=" . tosql($distribusiID, "Number") . ") AND status=1") : null;
        if ($krdID) {
            $oldTerpakai = dlookup("pendanaan_kredit", "saldo_terpakai", "kreditID=" . tosql($krdID, "Number"));
            $newTerpakai = max(0, $oldTerpakai - $nominal_pokok);
            $newTotalBayar = dlookup("pendanaan_kredit", "total_bayar", "kreditID=" . tosql($krdID, "Number")) + $nominal_pokok;
            // Hitung skor: +1 jika tidak telat, -2 jika telat
            $oldSkor = dlookup("pendanaan_kredit", "skor_kredit", "kreditID=" . tosql($krdID, "Number"));
            $skorDelta = ($hari_telat > 0) ? -2 : 1;
            $newSkor = max(0, min(100, $oldSkor + $skorDelta));
            $conn->Execute("UPDATE pendanaan_kredit SET
                saldo_terpakai=" . tosql($newTerpakai, "Number") . ",
                total_bayar=" . tosql($newTotalBayar, "Number") . ",
                skor_kredit=" . tosql($newSkor, "Number") . ",
                updatedDate='$now', updatedBy=" . tosql($by_user, "Text") .
                " WHERE kreditID=" . tosql($krdID, "Number"));
            $conn->Execute("INSERT INTO pendanaan_kredit_trx
                (kreditID, usahaID, jenis, ref_tabel, ref_id, nominal, saldo_terpakai, keterangan, createdDate, createdBy)
                VALUES (" . tosql($krdID, "Number") . ",
                (SELECT usahaID FROM pendanaan_distribusi WHERE distribusiID=" . tosql($distribusiID, "Number") . "),'KREDIT',
                'pendanaan_cicilan'," . tosql($cicilanID, "Number") . ","
                . tosql($nominal_pokok, "Number") . ","
                . tosql($newTerpakai, "Number") . ","
                . tosql("Bayar cicilan ke-$angsuran_ke dari $dist_noDist", "Text") . ","
                . "'$now'," . tosql($by_user, "Text") . ")");
        }

        // Cek apakah semua cicilan sudah lunas
        $cekBelum = dlookup("pendanaan_cicilan", "COUNT(*)", "distribusiID=" . tosql($distribusiID, "Number") . " AND status!=1");
        if ($cekBelum == 0) {
            $conn->Execute("UPDATE pendanaan_distribusi SET status=2, updatedDate='" . $now . "', updatedBy=" .
                tosql($by_user, "Text") . " WHERE distribusiID=" . tosql($distribusiID, "Number"));
        }

        // Notifikasi jika telat
        if ($hari_telat > 0) {
            $distUsahaID = dlookup("pendanaan_distribusi", "usahaID", "distribusiID=" . tosql($distribusiID, "Number"));
            $distMemberID = dlookup("pendanaan_distribusi", "memberID", "distribusiID=" . tosql($distribusiID, "Number"));
            $conn->Execute("INSERT INTO pendanaan_notifikasi
                (usahaID, memberID, distribusiID, cicilanID, jenis, pesan, createdDate)
                VALUES (" . tosql($distUsahaID, "Number") . ","
                . tosql($distMemberID, "Text") . ","
                . tosql($distribusiID, "Number") . ","
                . tosql($cicilanID, "Number") . ",'TELAT',"
                . tosql("Cicilan ke-$angsuran_ke ($dist_noDist) dibayar telat $hari_telat hari. Denda: Rp " . number_format($nominal_denda, 0, ',', '.'), "Text") . ","
                . "'$now')");
        }

        // Post jurnal akuntansi: Bayar Cicilan
        $nominal_bunga_rsCil = $rsCil->fields('nominal_bunga');
        $jrnKet = "Bayar cicilan ke-$angsuran_ke dari $dist_noDist ($dist_namaUsaha)";
        $totalPiutangBerkurang = $nominal_pokok + $nominal_bunga_rsCil + $nominal_denda;
        $jrnLines = array(
            array('1-1001', 'Kas Pool Pendanaan',           $nominal_pokok,       0,                      'Pengembalian pokok'),
            array('4-1001', 'Pendapatan Bunga Pendanaan',   $nominal_bunga_rsCil, 0,                      'Bunga cicilan ke-'.$angsuran_ke),
        );
        if ($nominal_denda > 0) {
            $jrnLines[] = array('4-1002', 'Pendapatan Denda Pendanaan', $nominal_denda, 0, 'Denda '.$hari_telat.' hari');
        }
        $jrnLines[] = array('1-2001', 'Piutang Pendanaan Usaha', 0, $totalPiutangBerkurang, $jrnKet);

        $jrnJenis = ($nominal_denda > 0) ? 'BAYAR_DENDA' : 'BAYAR_CICILAN';
        postJurnalPendanaan($conn, $jrnJenis, 'pendanaan_cicilan', $cicilanID, $jrnKet, $tgl_bayar, $jrnLines, $by_user);

        activityLog("Bayar Cicilan ke-$angsuran_ke, Distribusi $dist_noDist", "Bayar Cicilan Pendanaan", get_session('Cookie_userID'), $by_user, 3);
        print '<script>alert("Pembayaran cicilan ke-' . $angsuran_ke . ' berhasil dicatat.");window.location="?vw=pendanaanCicilanList&mn=' . $mn . '&distribusiID=' . $distribusiID . '";</script>';
        exit;
    }
}

// --- Query cicilan ---
$rsCicilan = $conn->Execute("SELECT * FROM pendanaan_cicilan WHERE distribusiID=" . tosql($distribusiID, "Number") . " ORDER BY angsuran_ke ASC");

$totalPokok  = 0; $totalBunga  = 0; $totalTagihan = 0; $totalBayar = 0;
$jmlLunas    = 0; $jmlBelum    = 0;

$statusCilLabel = array(
    '0' => '<span class="badge bg-secondary">Belum Bayar</span>',
    '1' => '<span class="badge bg-success">Lunas</span>',
    '2' => '<span class="badge bg-warning text-dark">Telat</span>',
    '3' => '<span class="badge bg-danger">Macet</span>',
);
$distStatusLabel = array(
    '1' => '<span class="badge bg-primary">Aktif</span>',
    '2' => '<span class="badge bg-success">Lunas</span>',
    '3' => '<span class="badge bg-danger">Macet</span>',
);
?>

<div class="maroon" align="left">
    <a class="maroon" href="?vw=pendanaanList&mn=<?= $mn ?>">DAFTAR PENGAJUAN</a>
    <b>&nbsp;&gt;&nbsp;CICILAN <?= $dist_noDist ?></b>
</div>
<div>&nbsp;</div>

<!-- Info Distribusi -->
<div class="card mb-3" style="max-width:750px">
    <div class="card-body p-2">
    <table class="table table-sm mb-0">
    <tr>
        <td width="170"><b>No. Distribusi</b></td><td><?= $dist_noDist ?></td>
        <td width="150"><b>No. Pengajuan</b></td><td><?= $dist_noPeng ?></td>
    </tr>
    <tr>
        <td><b>Usaha</b></td><td><?= $dist_namaUsaha ?></td>
        <td><b>Anggota</b></td><td><?= $dist_memberID ?></td>
    </tr>
    <tr>
        <td><b>Nominal</b></td>
        <td>Rp <?= number_format($dist_nominal, 0, ',', '.') ?></td>
        <td><b>Bunga/Thn</b></td>
        <td><?= $dist_bunga ?>%</td>
    </tr>
    <tr>
        <td><b>Tenor</b></td>
        <td><?= $dist_tenor ?> bulan</td>
        <td><b>Cicilan/Bln</b></td>
        <td>Rp <?= number_format($dist_cicilan_bln, 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td><b>Tgl. Distribusi</b></td>
        <td><?= $dist_tgl ?></td>
        <td><b>Status</b></td>
        <td><?= isset($distStatusLabel[$dist_status]) ? $distStatusLabel[$dist_status] : '-' ?></td>
    </tr>
    </table>
    </div>
</div>

<!-- Modal Bayar -->
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Input Pembayaran Cicilan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" action="?vw=pendanaanCicilanList&mn=<?= $mn ?>&distribusiID=<?= $distribusiID ?>">
        <input type="hidden" name="action" value="Bayar">
        <input type="hidden" name="cicilanID" id="inp_cicilanID">
        <div class="modal-body">
            <table class="table table-sm">
            <tr>
                <td>Angsuran ke</td>
                <td><b id="inf_angsuran"></b></td>
            </tr>
            <tr>
                <td>Total Tagihan</td>
                <td><b id="inf_tagihan"></b></td>
            </tr>
            <tr>
                <td>Nominal Bayar (Rp)</td>
                <td><input type="text" name="nominal_bayar" id="inp_nominal" class="form-control-sm" size="15" placeholder="Kosongkan = bayar lunas"></td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td>
                    <select name="metode_bayar" class="form-select-sm">
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Potongan Gaji">Potongan Gaji</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>No. Referensi</td>
                <td><input type="text" name="no_referensi" class="form-control-sm" size="20"></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td><input type="text" name="keterangan_bayar" class="form-control-sm" size="30"></td>
            </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm">Simpan Pembayaran</button>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
        </div>
        </form>
    </div>
    </div>
</div>

<!-- Tabel Cicilan -->
<h6 class="card-title">Jadwal Cicilan</h6>
<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
    <thead class="table-primary">
        <tr>
            <th>Ke</th>
            <th>Tgl. Jatuh Tempo</th>
            <th class="text-end">Pokok</th>
            <th class="text-end">Bunga</th>
            <th class="text-end">Denda</th>
            <th class="text-end">Total Tagihan</th>
            <th>Status</th>
            <th>Tgl. Bayar</th>
            <th class="text-end">Nominal Bayar</th>
            <?php if ($isAdmin): ?><th>Aksi</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php while ($rsCicilan && !$rsCicilan->EOF):
        $cID       = $rsCicilan->fields('cicilanID');
        $angk      = $rsCicilan->fields('angsuran_ke');
        $pokok     = $rsCicilan->fields('nominal_pokok');
        $bunga     = $rsCicilan->fields('nominal_bunga');
        $denda     = $rsCicilan->fields('nominal_denda');
        $tagihan   = $rsCicilan->fields('total_tagihan');
        $tglJT     = toDate("d/m/Y", $rsCicilan->fields('tgl_jatuh_tempo'));
        $tglByr    = toDate("d/m/Y", $rsCicilan->fields('tgl_bayar'));
        $nomByr    = $rsCicilan->fields('nominal_bayar');
        $stsCil    = $rsCicilan->fields('status');

        $totalPokok   += $pokok;
        $totalBunga   += $bunga;
        $totalTagihan += $tagihan;
        $totalBayar   += $nomByr;
        if ($stsCil == 1) $jmlLunas++; else $jmlBelum++;

        // Warna row jika telat
        $today = date('Y-m-d');
        $tglJTraw = $rsCicilan->fields('tgl_jatuh_tempo');
        $rowClass = '';
        if ($stsCil == 0 && $tglJTraw < $today) $rowClass = 'table-warning';
        if ($stsCil == 3) $rowClass = 'table-danger';
    ?>
    <tr class="<?= $rowClass ?>">
        <td class="text-center"><?= $angk ?></td>
        <td><?= $tglJT ?></td>
        <td class="text-end">Rp <?= number_format($pokok, 0, ',', '.') ?></td>
        <td class="text-end">Rp <?= number_format($bunga, 0, ',', '.') ?></td>
        <td class="text-end"><?= $denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : '-' ?></td>
        <td class="text-end"><b>Rp <?= number_format($tagihan, 0, ',', '.') ?></b></td>
        <td><?= isset($statusCilLabel[$stsCil]) ? $statusCilLabel[$stsCil] : '-' ?></td>
        <td><?= $tglByr ? $tglByr : '-' ?></td>
        <td class="text-end"><?= $nomByr > 0 ? 'Rp ' . number_format($nomByr, 0, ',', '.') : '-' ?></td>
        <?php if ($isAdmin): ?>
        <td>
            <?php if ($stsCil != 1): ?>
            <button type="button" class="btn btn-xs btn-success btn-sm"
                    data-bs-toggle="modal" data-bs-target="#modalBayar"
                    onclick="setBayar(<?= $cID ?>, <?= $angk ?>, '<?= number_format($tagihan + $denda, 0, ',', '.') ?>')">
                Bayar
            </button>
            <?php else: ?>
            <span class="text-muted">-</span>
            <?php endif; ?>
        </td>
        <?php endif; ?>
    </tr>
    <?php $rsCicilan->MoveNext(); endwhile; ?>
    </tbody>
    <tfoot class="table-light fw-bold">
        <tr>
            <td colspan="2">Total</td>
            <td class="text-end">Rp <?= number_format($totalPokok, 0, ',', '.') ?></td>
            <td class="text-end">Rp <?= number_format($totalBunga, 0, ',', '.') ?></td>
            <td></td>
            <td class="text-end">Rp <?= number_format($totalTagihan, 0, ',', '.') ?></td>
            <td><?= $jmlLunas ?>/<?= ($jmlLunas + $jmlBelum) ?> Lunas</td>
            <td></td>
            <td class="text-end">Rp <?= number_format($totalBayar, 0, ',', '.') ?></td>
            <?php if ($isAdmin): ?><td></td><?php endif; ?>
        </tr>
    </tfoot>
</table>
</div>

<a href="?vw=pendanaanList&mn=<?= $mn ?>" class="btn btn-outline-secondary btn-sm mt-2">Kembali</a>

<script>
function setBayar(cID, angk, tagihan) {
    document.getElementById('inp_cicilanID').value = cID;
    document.getElementById('inf_angsuran').textContent = 'ke-' + angk;
    document.getElementById('inf_tagihan').textContent = 'Rp ' + tagihan;
    document.getElementById('inp_nominal').value = '';
}
</script>
