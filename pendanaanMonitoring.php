<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pendanaanMonitoring.php
 *      Modul     : Pendanaan Usaha - Dashboard Monitoring (Kartu Kredit)
 *      Akses     : Admin (semua), Anggota (usaha sendiri)
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

// Cek tabel kredit sudah ada
$cekTabel = $conn->Execute("SHOW TABLES LIKE 'pendanaan_kredit'");
$hasKreditTable = ($cekTabel && !$cekTabel->EOF);

// Tentukan scope: admin bisa lihat by usahaID, anggota hanya milik sendiri
if ($isAdmin && $usahaID) {
    $scopeUsahaID = (int) $usahaID;
    $scopeMember  = dlookup("usaha", "memberID", "usahaID=" . tosql($scopeUsahaID, "Number"));
} elseif (!$isAdmin) {
    // Anggota: ambil usaha pertama yang aktif miliknya (jika lebih dari 1, bisa dipilih)
    $scopeMember = $myMemberID;
    if ($usahaID) {
        // Validasi usaha milik anggota
        $cekOwn = dlookup("usaha", "usahaID", "usahaID=" . tosql($usahaID, "Number") . " AND memberID=" . tosql($myMemberID, "Text"));
        $scopeUsahaID = $cekOwn ? (int)$usahaID : 0;
    }
    if (!$scopeUsahaID) {
        $rsFirstUsaha = $conn->Execute("SELECT usahaID FROM usaha WHERE memberID=" . tosql($myMemberID, "Text") . " AND status=1 LIMIT 1");
        $scopeUsahaID = ($rsFirstUsaha && !$rsFirstUsaha->EOF) ? (int)$rsFirstUsaha->fields('usahaID') : 0;
    }
} else {
    // Admin tanpa filter usahaID → tampilkan overview semua
    $scopeUsahaID = 0;
    $scopeMember  = '';
}

$today = date('Y-m-d');

/* ============================================================
   MODE: OVERVIEW ADMIN (tanpa filter usahaID)
   ============================================================ */
if ($isAdmin && !$scopeUsahaID) {

    // Pool dana
    $rsPool = $conn->Execute("SELECT * FROM pendanaan_pool WHERE poolID=1");
    $saldoPool = $rsPool && !$rsPool->EOF ? $rsPool->fields('saldo') : 0;
    $masukPool = $rsPool && !$rsPool->EOF ? $rsPool->fields('total_masuk') : 0;
    $keluarPool = $rsPool && !$rsPool->EOF ? $rsPool->fields('total_keluar') : 0;

    // Statistik distribusi
    $rsStats = $conn->Execute("SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) AS aktif,
        SUM(CASE WHEN status=2 THEN 1 ELSE 0 END) AS lunas,
        SUM(CASE WHEN status=3 THEN 1 ELSE 0 END) AS macet,
        SUM(CASE WHEN status=1 THEN nominal ELSE 0 END) AS outstanding,
        SUM(nominal) AS total_disalurkan
        FROM pendanaan_distribusi");
    $stats = ($rsStats && !$rsStats->EOF) ? $rsStats->fields : array();

    // Cicilan jatuh tempo hari ini & overdue
    $rsOverdue = $conn->Execute("SELECT COUNT(*) AS jml, IFNULL(SUM(total_tagihan),0) AS total
        FROM pendanaan_cicilan
        WHERE status IN (0,2) AND tgl_jatuh_tempo < '$today'");
    $overdueJml   = $rsOverdue && !$rsOverdue->EOF ? $rsOverdue->fields('jml') : 0;
    $overdueTotal = $rsOverdue && !$rsOverdue->EOF ? $rsOverdue->fields('total') : 0;

    $rsToday = $conn->Execute("SELECT COUNT(*) AS jml, IFNULL(SUM(total_tagihan),0) AS total
        FROM pendanaan_cicilan
        WHERE status=0 AND tgl_jatuh_tempo='$today'");
    $todayJml   = $rsToday && !$rsToday->EOF ? $rsToday->fields('jml') : 0;
    $todayTotal = $rsToday && !$rsToday->EOF ? $rsToday->fields('total') : 0;

    // NPL
    $npl = ($stats['total'] > 0) ? round(($stats['macet'] / $stats['total']) * 100, 1) : 0;

    // Pengajuan pending
    $pendingPeng = dlookup("pendanaan_pengajuan", "COUNT(*)", "status=1");

    // Top 5 outstanding per usaha
    $rsTop = $conn->Execute("SELECT d.memberID, u.nama_usaha,
        SUM(d.nominal) AS outstanding,
        COUNT(d.distribusiID) AS jml_distribusi
        FROM pendanaan_distribusi d
        LEFT JOIN usaha u ON d.usahaID = u.usahaID
        WHERE d.status=1
        GROUP BY d.usahaID ORDER BY outstanding DESC LIMIT 5");

    // Aging cicilan
    $rsAging = $conn->Execute("SELECT
        SUM(CASE WHEN DATEDIFF('$today',tgl_jatuh_tempo) BETWEEN 1 AND 30 THEN total_tagihan ELSE 0 END) AS a30,
        SUM(CASE WHEN DATEDIFF('$today',tgl_jatuh_tempo) BETWEEN 31 AND 60 THEN total_tagihan ELSE 0 END) AS a60,
        SUM(CASE WHEN DATEDIFF('$today',tgl_jatuh_tempo) BETWEEN 61 AND 90 THEN total_tagihan ELSE 0 END) AS a90,
        SUM(CASE WHEN DATEDIFF('$today',tgl_jatuh_tempo) > 90 THEN total_tagihan ELSE 0 END) AS a90p
        FROM pendanaan_cicilan WHERE status IN (0,2) AND tgl_jatuh_tempo < '$today'");
    $aging = ($rsAging && !$rsAging->EOF) ? $rsAging->fields : array();

    // Bayar bulan ini
    $rsByrBln = $conn->Execute("SELECT IFNULL(SUM(nominal),0) AS total FROM pendanaan_bayar
        WHERE MONTH(tgl_bayar)=MONTH('$today') AND YEAR(tgl_bayar)=YEAR('$today')");
    $byrBln = $rsByrBln && !$rsByrBln->EOF ? $rsByrBln->fields('total') : 0;

    ?>
    <div class="maroon"><b>MONITORING PENDANAAN USAHA — OVERVIEW ADMIN</b></div>
    <div>&nbsp;</div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body p-2">
                    <div class="text-white-50 small"><i class="mdi mdi-bank"></i> Saldo Pool</div>
                    <div class="fw-bold">Rp <?= number_format($saldoPool, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body p-2">
                    <div class="text-white-50 small"><i class="mdi mdi-cash-multiple"></i> Outstanding Aktif</div>
                    <div class="fw-bold">Rp <?= number_format(isset($stats['outstanding']) ? $stats['outstanding'] : 0, 0, ',', '.') ?></div>
                    <div class="text-white-50 small"><?= isset($stats['aktif']) ? $stats['aktif'] : 0 ?> distribusi aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-danger h-100">
                <div class="card-body p-2">
                    <div class="text-white-50 small"><i class="mdi mdi-alert-circle"></i> Overdue</div>
                    <div class="fw-bold">Rp <?= number_format($overdueTotal, 0, ',', '.') ?></div>
                    <div class="text-white-50 small"><?= $overdueJml ?> tagihan | NPL: <?= $npl ?>%</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body p-2">
                    <div class="text-white-50 small"><i class="mdi mdi-check-circle"></i> Bayar Bulan Ini</div>
                    <div class="fw-bold">Rp <?= number_format($byrBln, 0, ',', '.') ?></div>
                    <div class="text-white-50 small"><?= $pendingPeng ?> pengajuan pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub-stats row -->
    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <!-- Aging Report -->
            <div class="card h-100">
                <div class="card-header p-2 fw-bold"><i class="mdi mdi-chart-bar"></i> Aging Cicilan Overdue</div>
                <div class="card-body p-2">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>1 – 30 hari</td>
                            <td class="text-end text-warning fw-bold">Rp <?= number_format(isset($aging['a30']) ? $aging['a30'] : 0, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>31 – 60 hari</td>
                            <td class="text-end text-orange fw-bold" style="color:#fd7e14">Rp <?= number_format(isset($aging['a60']) ? $aging['a60'] : 0, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>61 – 90 hari</td>
                            <td class="text-end text-danger fw-bold">Rp <?= number_format(isset($aging['a90']) ? $aging['a90'] : 0, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>&gt; 90 hari (Macet)</td>
                            <td class="text-end text-danger fw-bold">Rp <?= number_format(isset($aging['a90p']) ? $aging['a90p'] : 0, 0, ',', '.') ?></td>
                        </tr>
                        <?php if ($todayJml > 0): ?>
                        <tr class="table-warning">
                            <td><b>Jatuh Tempo Hari Ini</b></td>
                            <td class="text-end fw-bold">Rp <?= number_format($todayTotal, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Top Outstanding -->
            <div class="card h-100">
                <div class="card-header p-2 fw-bold"><i class="mdi mdi-sort-descending"></i> Top Usaha by Outstanding</div>
                <div class="card-body p-2">
                    <table class="table table-sm mb-0">
                    <thead><tr><th>Usaha</th><th>Anggota</th><th class="text-end">Outstanding</th></tr></thead>
                    <tbody>
                    <?php while ($rsTop && !$rsTop->EOF): ?>
                    <tr>
                        <td><?= $rsTop->fields('nama_usaha') ?></td>
                        <td><?= $rsTop->fields('memberID') ?></td>
                        <td class="text-end">Rp <?= number_format($rsTop->fields('outstanding'), 0, ',', '.') ?></td>
                    </tr>
                    <?php $rsTop->MoveNext(); endwhile; ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Akun Kredit overview -->
    <?php
    $rsKredit = $hasKreditTable ? $conn->Execute("SELECT k.*, u.nama_usaha FROM pendanaan_kredit k
        LEFT JOIN usaha u ON k.usahaID = u.usahaID WHERE k.status=1 ORDER BY k.saldo_terpakai DESC") : null;
    if ($rsKredit && !$rsKredit->EOF):
    ?>
    <h6 class="card-title mt-2">Akun Kredit Aktif</h6>
    <div class="row g-2">
    <?php while (!$rsKredit->EOF):
        $kLimit   = $rsKredit->fields('limit_kredit');
        $kTerpakai = $rsKredit->fields('saldo_terpakai');
        $kPct     = $kLimit > 0 ? round(($kTerpakai / $kLimit) * 100, 0) : 0;
        $kBar     = $kPct < 50 ? 'bg-success' : ($kPct < 80 ? 'bg-warning' : 'bg-danger');
        $kSkor    = $rsKredit->fields('skor_kredit');
        $kSkorClass = $kSkor >= 80 ? 'text-success' : ($kSkor >= 60 ? 'text-warning' : 'text-danger');
    ?>
    <div class="col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm h-100"
             style="background:linear-gradient(135deg,#1a3a5c,#2d6a9f); color:#fff; border-radius:12px;">
            <div class="card-body p-3">
                <div style="font-size:0.7em;opacity:0.7;letter-spacing:2px">AKUN KREDIT</div>
                <div style="font-size:0.85em;font-weight:600;margin:2px 0 4px"><?= $rsKredit->fields('no_akun') ?></div>
                <div style="font-size:0.8em;opacity:0.85;margin-bottom:8px"><?= strtoupper(substr($rsKredit->fields('nama_usaha'),0,22)) ?></div>
                <div class="d-flex justify-content-between" style="font-size:0.75em;opacity:0.7">
                    <span>LIMIT</span><span>TERPAKAI</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span style="font-size:0.85em">Rp <?= number_format($kLimit/1000000,1) ?>jt</span>
                    <span style="font-size:0.85em;color:#ffd700">Rp <?= number_format($kTerpakai/1000000,1) ?>jt</span>
                </div>
                <div class="progress mb-2" style="height:5px;background:rgba(255,255,255,0.2)">
                    <div class="progress-bar <?= $kBar ?>" style="width:<?= $kPct ?>%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small style="opacity:0.7"><?= $kPct ?>% terpakai</small>
                    <a href="?vw=pendanaanMonitoring&mn=<?= $mn ?>&usahaID=<?= $rsKredit->fields('usahaID') ?>"
                       class="btn btn-outline-light btn-sm py-0 px-2" style="font-size:0.7em">Detail</a>
                </div>
                <div class="mt-1" style="font-size:0.72em">
                    Skor: <span class="<?= $kSkorClass ?> fw-bold"><?= $kSkor ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php $rsKredit->MoveNext(); endwhile; ?>
    </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="?vw=pendanaanList&mn=<?= $mn ?>" class="btn btn-sm btn-outline-secondary">Senarai Pengajuan</a>
        &nbsp;<a href="?vw=pendanaanPool&mn=<?= $mn ?>" class="btn btn-sm btn-outline-secondary">Pool Dana</a>
        &nbsp;<a href="?vw=pendanaanKreditList&mn=<?= $mn ?>" class="btn btn-sm btn-outline-secondary">Akun Kredit</a>
    </div>

    <?php
    return; // stop execution untuk admin overview
}

/* ============================================================
   MODE: DETAIL PER USAHA (admin filter / anggota)
   ============================================================ */
if (!$scopeUsahaID) {
    print '<div class="alert alert-warning">Tidak ada usaha aktif yang ditemukan.</div>';
    return;
}

// Load info usaha
$rsUsaha = $conn->Execute("SELECT * FROM usaha WHERE usahaID=" . tosql($scopeUsahaID, "Number"));
if (!$rsUsaha || $rsUsaha->EOF) {
    print '<div class="alert alert-danger">Usaha tidak ditemukan.</div>';
    return;
}
$namaUsaha = $rsUsaha->fields('nama_usaha');

// Load akun kredit usaha
$rsKrd = $hasKreditTable ? $conn->Execute("SELECT * FROM pendanaan_kredit WHERE usahaID=" . tosql($scopeUsahaID, "Number") . " AND status IN (1,2) LIMIT 1") : null;
$hasKredit    = ($rsKrd && !$rsKrd->EOF);
$kLimit       = $hasKredit ? $rsKrd->fields('limit_kredit') : 0;
$kTerpakai    = $hasKredit ? $rsKrd->fields('saldo_terpakai') : 0;
$kTersedia    = $kLimit - $kTerpakai;
$kNoAkun      = $hasKredit ? $rsKrd->fields('no_akun') : '-';
$kSkor        = $hasKredit ? $rsKrd->fields('skor_kredit') : 0;
$kKadaluarsa  = $hasKredit ? $rsKrd->fields('tgl_kadaluarsa') : '';
$kreditID_now = $hasKredit ? $rsKrd->fields('kreditID') : 0;
$kPct         = ($kLimit > 0) ? round(($kTerpakai / $kLimit) * 100, 1) : 0;
$kBarClass    = $kPct < 50 ? 'bg-success' : ($kPct < 80 ? 'bg-warning' : 'bg-danger');
$kSkorClass   = $kSkor >= 80 ? 'text-success' : ($kSkor >= 60 ? 'text-warning' : 'text-danger');
$kSkorLabel   = $kSkor >= 80 ? 'Baik' : ($kSkor >= 60 ? 'Perhatian' : 'Berisiko');

// Cicilan aktif (jatuh tempo berikutnya)
$rsNextCil = $conn->Execute("SELECT c.*, d.no_distribusi FROM pendanaan_cicilan c
    JOIN pendanaan_distribusi d ON c.distribusiID = d.distribusiID
    WHERE d.usahaID=" . tosql($scopeUsahaID, "Number") . "
      AND c.status IN (0,2)
    ORDER BY c.tgl_jatuh_tempo ASC LIMIT 1");
$hasNext = ($rsNextCil && !$rsNextCil->EOF);

// Cicilan overdue
$rsOverCil = $conn->Execute("SELECT COUNT(*) AS jml, IFNULL(SUM(c.total_tagihan),0) AS total
    FROM pendanaan_cicilan c
    JOIN pendanaan_distribusi d ON c.distribusiID=d.distribusiID
    WHERE d.usahaID=" . tosql($scopeUsahaID, "Number") . "
      AND c.status IN (0,2) AND c.tgl_jatuh_tempo < '$today'");
$overdueJml   = $rsOverCil && !$rsOverCil->EOF ? $rsOverCil->fields('jml') : 0;
$overdueTotal = $rsOverCil && !$rsOverCil->EOF ? $rsOverCil->fields('total') : 0;

// Total sudah dibayar
$rsByrTotal = $conn->Execute("SELECT IFNULL(SUM(b.nominal),0) AS total FROM pendanaan_bayar b
    JOIN pendanaan_distribusi d ON b.distribusiID=d.distribusiID
    WHERE d.usahaID=" . tosql($scopeUsahaID, "Number"));
$totalBayar = $rsByrTotal && !$rsByrTotal->EOF ? $rsByrTotal->fields('total') : 0;

// Distribusi aktif
$rsDistAktif = $conn->Execute("SELECT d.*, p.no_pengajuan,
    (SELECT COUNT(*) FROM pendanaan_cicilan WHERE distribusiID=d.distribusiID AND status=1) AS cicilan_lunas,
    (SELECT COUNT(*) FROM pendanaan_cicilan WHERE distribusiID=d.distribusiID) AS cicilan_total
    FROM pendanaan_distribusi d
    LEFT JOIN pendanaan_pengajuan p ON d.pengajuanID=p.pengajuanID
    WHERE d.usahaID=" . tosql($scopeUsahaID, "Number") . "
    ORDER BY d.status ASC, d.tgl_distribusi DESC");

// Usaha list untuk switcher (anggota punya banyak usaha)
$rsMyUsaha = $conn->Execute("SELECT usahaID, nama_usaha FROM usaha WHERE memberID=" . tosql($scopeMember, "Text") . " AND status=1");

// Notifikasi belum dibaca
$rsNotif = $conn->Execute("SELECT * FROM pendanaan_notifikasi
    WHERE usahaID=" . tosql($scopeUsahaID, "Number") . " AND dibaca=0 ORDER BY createdDate DESC LIMIT 5");
?>

<div class="maroon">
    <a class="maroon" href="?vw=pendanaanMonitoring&mn=<?= $mn ?>">MONITORING</a>
    <b>&nbsp;&gt;&nbsp;<?= strtoupper($namaUsaha) ?></b>
    <?php if ($isAdmin): ?>
    &nbsp;<a href="?vw=pendanaanMonitoring&mn=<?= $mn ?>" class="btn btn-xs btn-outline-secondary btn-sm">← Overview</a>
    <?php endif; ?>
</div>

<!-- Switcher usaha (jika anggota punya > 1 usaha) -->
<?php
$countUsaha = 0;
$usahaOpts  = array();
if ($rsMyUsaha) {
    while (!$rsMyUsaha->EOF) {
        $usahaOpts[] = array('id'=>$rsMyUsaha->fields('usahaID'), 'nama'=>$rsMyUsaha->fields('nama_usaha'));
        $rsMyUsaha->MoveNext();
        $countUsaha++;
    }
}
if ($countUsaha > 1):
?>
<div class="mb-2 mt-1">
    <small>Pilih Usaha: </small>
    <?php foreach ($usahaOpts as $opt): ?>
    <a href="?vw=pendanaanMonitoring&mn=<?= $mn ?>&usahaID=<?= $opt['id'] ?>"
       class="btn btn-xs btn-sm <?= ($opt['id']==$scopeUsahaID?'btn-primary':'btn-outline-secondary') ?> me-1">
        <?= $opt['nama'] ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row g-3 mt-1">
    <!-- Kartu Kredit Visual -->
    <div class="col-md-5">
        <?php if ($hasKredit): ?>
        <div style="background:linear-gradient(135deg,#1a3a5c 0%,#2d6a9f 60%,#1abc9c 100%);
                    border-radius:18px;padding:26px 28px;color:#fff;
                    min-height:200px;position:relative;box-shadow:0 10px 30px rgba(0,0,0,0.3);">
            <!-- Chip -->
            <div style="width:44px;height:34px;background:linear-gradient(135deg,#f9d423,#e0a800);
                        border-radius:6px;margin-bottom:16px;display:flex;align-items:center;
                        justify-content:center;">
                <div style="width:24px;height:18px;border:2px solid rgba(0,0,0,0.2);border-radius:3px;
                            background:linear-gradient(90deg,#f9d423 50%,#ffc107 50%)"></div>
            </div>
            <div style="font-size:1.1em;letter-spacing:4px;font-weight:600;margin-bottom:8px">
                <?= chunk_split($kNoAkun, 4, ' ') ?>
            </div>
            <div style="font-size:1em;font-weight:500;margin-bottom:4px"><?= strtoupper($namaUsaha) ?></div>
            <div style="font-size:0.78em;opacity:0.7">No. Anggota: <?= $scopeMember ?></div>
            <!-- Logo -->
            <div style="position:absolute;right:24px;top:18px;opacity:0.5;font-size:2.5em">
                <i class="mdi mdi-credit-card-outline"></i>
            </div>
            <?php if ($kKadaluarsa): ?>
            <div style="position:absolute;right:24px;bottom:20px;font-size:0.72em;opacity:0.65;text-align:right">
                BERLAKU S/D<br><b><?= toDate("m/Y", $kKadaluarsa) ?></b>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info limit di bawah kartu -->
        <div class="card mt-2 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="row text-center mb-2">
                    <div class="col-4">
                        <div class="small text-muted">Limit</div>
                        <div class="fw-bold text-primary small">Rp <?= number_format($kLimit/1000000,1) ?>jt</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Terpakai</div>
                        <div class="fw-bold text-danger small">Rp <?= number_format($kTerpakai/1000000,1) ?>jt</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Tersedia</div>
                        <div class="fw-bold text-success small">Rp <?= number_format($kTersedia/1000000,1) ?>jt</div>
                    </div>
                </div>
                <!-- Utilisasi bar -->
                <div class="progress mb-1" style="height:14px;border-radius:7px">
                    <div class="progress-bar <?= $kBarClass ?> progress-bar-striped progress-bar-animated"
                         style="width:<?= $kPct ?>%" aria-label="<?= $kPct ?>%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Utilisasi: <b><?= $kPct ?>%</b></small>
                    <small class="<?= $kSkorClass ?>">Skor Kredit: <b><?= $kSkor ?>/100</b>
                        <span class="badge <?= $kSkor>=80?'bg-success':($kSkor>=60?'bg-warning text-dark':'bg-danger') ?>"><?= $kSkorLabel ?></span>
                    </small>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card border-dashed text-center p-4" style="border:2px dashed #ccc;border-radius:18px">
            <i class="mdi mdi-credit-card-off" style="font-size:3em;color:#ccc"></i>
            <p class="text-muted mt-2">Belum ada akun kredit aktif untuk usaha ini.</p>
            <?php if ($isAdmin): ?>
            <a href="?vw=pendanaanKredit&mn=<?= $mn ?>&action=new" class="btn btn-sm btn-primary">Buka Akun Kredit</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Panel Kanan: Info cicilan & stats -->
    <div class="col-md-7">
        <!-- Notifikasi -->
        <?php if ($rsNotif && !$rsNotif->EOF): ?>
        <div class="alert alert-warning p-2 mb-2">
            <b><i class="mdi mdi-bell-ring"></i> Notifikasi:</b>
            <ul class="mb-0 mt-1" style="font-size:0.85em">
            <?php while (!$rsNotif->EOF): ?>
                <li><?= $rsNotif->fields('pesan') ?> <small class="text-muted">(<?= toDate("d/m/Y", $rsNotif->fields('createdDate')) ?>)</small></li>
            <?php $rsNotif->MoveNext(); endwhile; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Tagihan Berikutnya -->
        <?php if ($hasNext): ?>
        <?php
        $nextJT    = $rsNextCil->fields('tgl_jatuh_tempo');
        $nextTotal = $rsNextCil->fields('total_tagihan');
        $nextNoDist = $rsNextCil->fields('no_distribusi');
        $nextAngk  = $rsNextCil->fields('angsuran_ke');
        $hariLagi  = (int)ceil((strtotime($nextJT) - strtotime($today)) / 86400);
        $isLate    = $hariLagi < 0;
        $alertClass = $isLate ? 'alert-danger' : ($hariLagi <= 7 ? 'alert-warning' : 'alert-info');
        ?>
        <div class="alert <?= $alertClass ?> p-2 mb-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <b><i class="mdi mdi-calendar-clock"></i>
                    <?= $isLate ? 'OVERDUE — Cicilan Belum Dibayar' : 'Tagihan Berikutnya' ?></b>
                    <div style="font-size:0.85em">
                        <?= $nextNoDist ?> | Angsuran ke-<?= $nextAngk ?> |
                        Jatuh Tempo: <b><?= toDate("d/m/Y", $nextJT) ?></b>
                        <?php if ($isLate): ?>
                        <span class="badge bg-danger"><?= abs($hariLagi) ?> hari telat</span>
                        <?php else: ?>
                        <span class="badge bg-info"><?= $hariLagi ?> hari lagi</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold fs-6">Rp <?= number_format($nextTotal, 0, ',', '.') ?></div>
                    <?php if ($isAdmin): ?>
                    <a href="?vw=pendanaanCicilanList&mn=<?= $mn ?>&distribusiID=<?= $rsNextCil->fields('distribusiID') ?>"
                       class="btn btn-sm btn-outline-dark mt-1 py-0">Bayar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick stats -->
        <div class="row g-2 mb-2">
            <div class="col-6">
                <div class="card border-0 bg-light text-center p-2">
                    <div class="small text-muted">Total Pinjam</div>
                    <div class="fw-bold text-primary">Rp <?= number_format($kTerpakai + $totalBayar, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 bg-light text-center p-2">
                    <div class="small text-muted">Total Bayar</div>
                    <div class="fw-bold text-success">Rp <?= number_format($totalBayar, 0, ',', '.') ?></div>
                </div>
            </div>
            <?php if ($overdueJml > 0): ?>
            <div class="col-12">
                <div class="card border-0 bg-danger text-white text-center p-2">
                    <div class="small">Overdue: <b><?= $overdueJml ?> tagihan</b> —
                        Rp <?= number_format($overdueTotal, 0, ',', '.') ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Aksi cepat -->
        <div>
            <a href="?vw=pendanaanList&mn=<?= $mn ?>" class="btn btn-sm btn-outline-primary me-1">Pengajuan Saya</a>
            <?php if ($isAdmin): ?>
            <a href="?vw=pendanaanKreditList&mn=<?= $mn ?>" class="btn btn-sm btn-outline-secondary me-1">Akun Kredit</a>
            <a href="?vw=pendanaanPenyata&mn=<?= $mn ?>&usahaID=<?= $scopeUsahaID ?>" class="btn btn-sm btn-outline-dark me-1">Penyata</a>
            <?php else: ?>
            <a href="?vw=pendanaanPenyata&mn=<?= $mn ?>&usahaID=<?= $scopeUsahaID ?>" class="btn btn-sm btn-outline-dark me-1">Penyata Bulanan</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Daftar Distribusi Aktif -->
<?php if ($rsDistAktif && !$rsDistAktif->EOF): ?>
<h6 class="card-title mt-3">Rincian Fasilitas Pendanaan</h6>
<div class="table-responsive">
<table class="table table-sm table-bordered table-striped">
    <thead class="table-primary">
        <tr>
            <th>No. Distribusi</th>
            <th class="text-end">Nominal</th>
            <th>Bunga</th>
            <th>Tenor</th>
            <th class="text-end">Cicilan/Bln</th>
            <th>Progress</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $distStatusLabel = array('1'=>'<span class="badge bg-primary">Aktif</span>','2'=>'<span class="badge bg-success">Lunas</span>','3'=>'<span class="badge bg-danger">Macet</span>');
    while (!$rsDistAktif->EOF):
        $dID     = $rsDistAktif->fields('distribusiID');
        $dNo     = $rsDistAktif->fields('no_distribusi');
        $dNom    = $rsDistAktif->fields('nominal');
        $dBunga  = $rsDistAktif->fields('bunga_per_thn');
        $dTenor  = $rsDistAktif->fields('tenor');
        $dCil    = $rsDistAktif->fields('cicilan_per_bln');
        $dJT     = toDate("d/m/Y", $rsDistAktif->fields('tgl_jatuh_tempo'));
        $dSts    = $rsDistAktif->fields('status');
        $cilLunas = $rsDistAktif->fields('cicilan_lunas');
        $cilTotal = $rsDistAktif->fields('cicilan_total');
        $cilPct   = $cilTotal > 0 ? round(($cilLunas / $cilTotal) * 100) : 0;
    ?>
    <tr>
        <td><a href="?vw=pendanaanCicilanList&mn=<?= $mn ?>&distribusiID=<?= $dID ?>"><?= $dNo ?></a></td>
        <td class="text-end">Rp <?= number_format($dNom,0,',','.') ?></td>
        <td class="text-center"><?= $dBunga ?>%</td>
        <td class="text-center"><?= $dTenor ?> bln</td>
        <td class="text-end">Rp <?= number_format($dCil,0,',','.') ?></td>
        <td style="min-width:110px">
            <div class="progress" style="height:10px">
                <div class="progress-bar bg-success" style="width:<?= $cilPct ?>%"></div>
            </div>
            <small><?= $cilLunas ?>/<?= $cilTotal ?> lunas</small>
        </td>
        <td><?= $dJT ?></td>
        <td><?= isset($distStatusLabel[$dSts]) ? $distStatusLabel[$dSts] : '-' ?></td>
        <td>
            <a href="?vw=pendanaanCicilanList&mn=<?= $mn ?>&distribusiID=<?= $dID ?>"
               class="btn btn-xs btn-info btn-sm">Cicilan</a>
            <a href="?vw=pendanaanPenyata&mn=<?= $mn ?>&usahaID=<?= $scopeUsahaID ?>&distribusiID=<?= $dID ?>"
               class="btn btn-xs btn-secondary btn-sm">Penyata</a>
        </td>
    </tr>
    <?php $rsDistAktif->MoveNext(); endwhile; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
