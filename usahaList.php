<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : usahaList.php
 *      Modul     : Serba Usaha - Daftar Usaha (Shopee Style)
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 24;
if (!isset($q))        $q        = '';
if (!isset($filter))   $filter   = '';
if (!isset($katFilter)) $katFilter = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

$sFileName = "?vw=usahaList&mn=$mn";
$sFileRef  = "?vw=usaha&mn=$mn";

// --- Hapus ---
if ($action == "delete" && $isAdmin) {
    for ($i = 0; $i < count($pk); $i++) {
        $conn->Execute("DELETE FROM usaha WHERE usahaID=" . tosql($pk[$i], "Number"));
        activityLog("DELETE usaha ID " . $pk[$i], "Hapus Usaha", get_session('Cookie_userID'), get_session('Cookie_userName'), 3);
    }
}

// --- Approve / Reject ---
if ($action == "approve" && $isAdmin && $usahaID) {
    $conn->Execute("UPDATE usaha SET status=1, updatedDate='" . date("Y-m-d H:i:s") . "', updatedBy='" . get_session("Cookie_userName") . "' WHERE usahaID=" . tosql($usahaID, "Number"));
    print '<script>window.location="' . $sFileName . '";</script>'; exit;
}
if ($action == "reject" && $isAdmin && $usahaID) {
    $conn->Execute("UPDATE usaha SET status=2, updatedDate='" . date("Y-m-d H:i:s") . "', updatedBy='" . get_session("Cookie_userName") . "' WHERE usahaID=" . tosql($usahaID, "Number"));
    print '<script>window.location="' . $sFileName . '";</script>'; exit;
}

// --- Kategori list ---
$kategoriList = array('Makanan & Minuman','Pakaian & Aksesori','Elektronik','Pertanian','Perkhidmatan','Kraftangan','Lain-lain');
$katIcons = array(
    'Makanan & Minuman' => 'mdi-food',
    'Pakaian & Aksesori'=> 'mdi-tshirt-crew',
    'Elektronik'        => 'mdi-laptop',
    'Pertanian'         => 'mdi-sprout',
    'Perkhidmatan'      => 'mdi-tools',
    'Kraftangan'        => 'mdi-hand-extended',
    'Lain-lain'         => 'mdi-dots-horizontal-circle',
);
$katColors = array(
    'Makanan & Minuman' => array('#ff6b35','#f7c59f'),
    'Pakaian & Aksesori'=> array('#a855f7','#e9d5ff'),
    'Elektronik'        => array('#0ea5e9','#bae6fd'),
    'Pertanian'         => array('#22c55e','#bbf7d0'),
    'Perkhidmatan'      => array('#f59e0b','#fde68a'),
    'Kraftangan'        => array('#ec4899','#fbcfe8'),
    'Lain-lain'         => array('#64748b','#e2e8f0'),
);

// --- Build query ---
$sWhere = "WHERE 1=1";
if (!$isAdmin) $sWhere .= " AND a.memberID=" . tosql($myMemberID, "Text");
if ($filter != '') $sWhere .= " AND a.status=" . tosql($filter, "Number");
if ($katFilter != '') $sWhere .= " AND a.kategori=" . tosql($katFilter, "Text");

// Enhanced query with stats
$sSQL = "SELECT a.*,
    COALESCE((SELECT COUNT(*) FROM produk_usaha WHERE usahaID=a.usahaID AND status=1), 0) AS jml_produk,
    COALESCE((SELECT SUM(d.qty) FROM pos_order_detail d JOIN pos_order o ON d.orderID=o.orderID WHERE d.usahaID=a.usahaID), 0) AS total_terjual,
    COALESCE((SELECT SUM(d.subtotal) FROM pos_order_detail d JOIN pos_order o ON d.orderID=o.orderID WHERE d.usahaID=a.usahaID), 0) AS total_omzet
    FROM usaha a
    $sWhere ORDER BY a.status ASC, a.nama_usaha ASC";

$GetList = $conn->Execute($sSQL);
$allUsaha = array();
while ($GetList && !$GetList->EOF) {
    $allUsaha[] = array(
        'usahaID'     => $GetList->fields('usahaID'),
        'memberID'    => $GetList->fields('memberID'),
        'nama_usaha'  => $GetList->fields('nama_usaha'),
        'kategori'    => $GetList->fields('kategori'),
        'deskripsi'   => $GetList->fields('deskripsi'),
        'alamat'      => $GetList->fields('alamat'),
        'no_telefon'  => $GetList->fields('no_telefon'),
        'status'      => $GetList->fields('status'),
        'createdDate' => $GetList->fields('createdDate'),
        'jml_produk'  => (int)$GetList->fields('jml_produk'),
        'total_terjual'=> (float)$GetList->fields('total_terjual'),
        'total_omzet' => (float)$GetList->fields('total_omzet'),
    );
    $GetList->MoveNext();
}
$TotalRec = count($allUsaha);

// Stats ringkasan
$rsStats = $conn->Execute("SELECT
    COUNT(*) AS total,
    SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) AS aktif,
    SUM(CASE WHEN status=0 THEN 1 ELSE 0 END) AS pending
    FROM usaha" . ($isAdmin ? '' : " WHERE memberID=" . tosql($myMemberID, "Text")));
$statTotal = 0; $statAktif = 0; $statPending = 0;
if ($rsStats && !$rsStats->EOF) {
    $statTotal   = (int)$rsStats->fields('total');
    $statAktif   = (int)$rsStats->fields('aktif');
    $statPending = (int)$rsStats->fields('pending');
}

// Helper: initial avatar
function makeInitials($name) {
    $words = explode(' ', trim($name));
    $ini = '';
    foreach ($words as $w) { if ($w) $ini .= strtoupper(substr($w,0,1)); if (strlen($ini)>=2) break; }
    return $ini ? $ini : '??';
}
function hashColor($str) {
    $colors = array('#ee4d2d','#d0021b','#7b2ff7','#0070f3','#00c853','#ff6900','#e91e8c','#00bcd4','#ff5722','#607d8b');
    $idx = abs(crc32($str)) % count($colors);
    return $colors[$idx];
}
?>

<style>
/* ── Shopee-style Usaha List ── */
.su-wrap { margin: -10px; }

/* Hero bar */
.su-hero {
    background: linear-gradient(135deg, #ee4d2d 0%, #ff7337 60%, #ffb347 100%);
    padding: 20px 24px 14px;
    color: #fff;
}
.su-hero h4 {
    margin: 0 0 12px;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: .3px;
}
.su-search-row {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}
.su-search-box {
    flex: 1;
    min-width: 200px;
    max-width: 480px;
    position: relative;
}
.su-search-box input {
    width: 100%;
    border: none;
    border-radius: 6px;
    padding: 8px 14px 8px 38px;
    font-size: 13px;
    outline: none;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
.su-search-box i {
    position: absolute;
    left: 11px; top: 50%;
    transform: translateY(-50%);
    color: #aaa; font-size: 16px;
}
.su-hero .btn-new {
    background: #fff;
    color: #ee4d2d;
    font-weight: 700;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 13px;
    white-space: nowrap;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
    transition: opacity .15s;
}
.su-hero .btn-new:hover { opacity: .9; color: #ee4d2d; }

/* Stats bar */
.su-stats {
    background: #fff3f0;
    border-bottom: 1px solid #ffd5cc;
    padding: 10px 24px;
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    align-items: center;
}
.su-stat-item { font-size: 13px; }
.su-stat-item .val { font-weight: 800; font-size: 16px; color: #ee4d2d; }
.su-stat-item .lbl { color: #888; margin-left: 3px; }

/* Category tabs */
.su-cats {
    background: #fff;
    border-bottom: 1px solid #eee;
    padding: 0 20px;
    display: flex;
    gap: 0;
    overflow-x: auto;
    scrollbar-width: none;
}
.su-cats::-webkit-scrollbar { display: none; }
.su-cat-tab {
    padding: 10px 16px;
    font-size: 12.5px;
    font-weight: 600;
    color: #888;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all .15s;
}
.su-cat-tab:hover { color: #ee4d2d; }
.su-cat-tab.active { color: #ee4d2d; border-bottom-color: #ee4d2d; }

/* Filter chips */
.su-filter-row {
    background: #fafafa;
    border-bottom: 1px solid #eee;
    padding: 8px 20px;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
    font-size: 12px;
}
.su-filter-row .lbl { color: #888; font-size: 12px; }
.su-chip {
    padding: 4px 12px;
    border-radius: 20px;
    border: 1.5px solid #e0e0e0;
    background: #fff;
    color: #555;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
}
.su-chip:hover { border-color: #ee4d2d; color: #ee4d2d; }
.su-chip.active { background: #ee4d2d; color: #fff; border-color: #ee4d2d; }

/* Grid */
.su-grid-area { padding: 16px 20px; }
.su-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 14px;
}

/* Store card */
.su-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    border: 1.5px solid #f0f0f0;
    transition: all .18s;
    position: relative;
    display: flex;
    flex-direction: column;
}
.su-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(238,77,45,.15);
    border-color: #ee4d2d;
}

/* Banner */
.su-banner {
    height: 72px;
    position: relative;
    overflow: hidden;
}
.su-banner-bg {
    position: absolute; inset: 0;
}
.su-banner-pattern {
    position: absolute; inset: 0;
    opacity: .15;
    background-image: radial-gradient(circle, #fff 1px, transparent 1px);
    background-size: 12px 12px;
}

/* Avatar */
.su-avatar-wrap {
    position: absolute;
    bottom: -22px;
    left: 14px;
}
.su-avatar {
    width: 44px; height: 44px;
    border-radius: 10px;
    border: 3px solid #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 800; color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
    letter-spacing: .5px;
}

/* Status badge on card */
.su-card-status {
    position: absolute;
    top: 8px; right: 8px;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 700;
}
.su-card-status.aktif    { background: #dcfce7; color: #16a34a; }
.su-card-status.pending  { background: #fef9c3; color: #ca8a04; }
.su-card-status.nonaktif { background: #fee2e2; color: #dc2626; }

/* Card body */
.su-card-body {
    padding: 30px 14px 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.su-store-name {
    font-weight: 800;
    font-size: 14px;
    color: #1e293b;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.su-kat-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 10.5px;
    color: #ee4d2d;
    background: #fff3f0;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 600;
    margin-bottom: 8px;
}
.su-member {
    font-size: 11px;
    color: #94a3b8;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.su-deskripsi {
    font-size: 11.5px;
    color: #64748b;
    margin-bottom: 8px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Stats row */
.su-stats-row {
    display: flex;
    gap: 0;
    border-top: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    margin: 8px 0;
}
.su-stat {
    flex: 1;
    text-align: center;
    padding: 6px 4px;
    border-right: 1px solid #f1f5f9;
}
.su-stat:last-child { border-right: none; }
.su-stat .sv { font-size: 14px; font-weight: 800; color: #1e293b; display: block; }
.su-stat .sk { font-size: 10px; color: #94a3b8; }

/* Card actions */
.su-card-actions {
    padding: 0 14px 12px;
    display: flex;
    gap: 6px;
}
.su-btn-visit {
    flex: 1;
    background: #ee4d2d;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 700;
    text-align: center;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    transition: background .15s;
}
.su-btn-visit:hover { background: #d03f22; color: #fff; }
.su-btn-produk {
    background: #f8fafc;
    color: #475569;
    border: 1.5px solid #e2e8f0;
    border-radius: 6px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    cursor: pointer;
    transition: all .15s;
}
.su-btn-produk:hover { border-color: #ee4d2d; color: #ee4d2d; }

/* Admin approve strip */
.su-pending-strip {
    background: #fef9c3;
    border-top: 1px solid #fde047;
    padding: 6px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
}
.su-btn-apv {
    background: #16a34a;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
}
.su-btn-apv:hover { background: #15803d; color: #fff; }
.su-btn-rej {
    background: none;
    border: 1.5px solid #dc2626;
    color: #dc2626;
    border-radius: 5px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    margin-left: 4px;
}
.su-btn-rej:hover { background: #dc2626; color: #fff; }

/* Empty */
.su-empty {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}
.su-empty i { font-size: 56px; }

/* Pagination */
.su-pagination {
    padding: 12px 20px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13px;
    color: #64748b;
}

@media (max-width: 640px) {
    .su-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .su-hero { padding: 14px 14px 10px; }
    .su-grid-area { padding: 12px; }
}
</style>

<div class="su-wrap">

<!-- ── HERO BAR ── -->
<div class="su-hero">
    <h4><i class="mdi mdi-store-outline"></i> Daftar Usaha Anggota</h4>
    <div class="su-search-row">
        <div class="su-search-box">
            <i class="mdi mdi-magnify"></i>
            <input type="text" id="suSearch" placeholder="Cari nama usaha..." value="<?= htmlspecialchars($q) ?>" autocomplete="off">
        </div>
        <a href="<?= $sFileRef ?>&action=new" class="btn-new">
            <i class="mdi mdi-plus-circle"></i> Daftar Usaha Baru
        </a>
    </div>
</div>

<!-- ── STATS BAR ── -->
<div class="su-stats">
    <div class="su-stat-item"><span class="val"><?= $statTotal ?></span><span class="lbl">Total Usaha</span></div>
    <div class="su-stat-item"><span class="val" style="color:#16a34a"><?= $statAktif ?></span><span class="lbl">Aktif</span></div>
    <?php if ($statPending > 0): ?>
    <div class="su-stat-item"><span class="val" style="color:#ca8a04"><?= $statPending ?></span><span class="lbl">Menunggu Approval</span></div>
    <?php endif; ?>
</div>

<!-- ── CATEGORY TABS ── -->
<div class="su-cats">
    <a href="<?= $sFileName ?>&filter=<?= urlencode($filter) ?>"
       class="su-cat-tab <?= $katFilter == '' ? 'active' : '' ?>">
       <i class="mdi mdi-view-grid"></i> Semua
    </a>
    <?php foreach ($kategoriList as $kat):
        $ico = isset($katIcons[$kat]) ? $katIcons[$kat] : 'mdi-package-variant';
    ?>
    <a href="<?= $sFileName ?>&filter=<?= urlencode($filter) ?>&katFilter=<?= urlencode($kat) ?>"
       class="su-cat-tab <?= $katFilter == $kat ? 'active' : '' ?>">
       <i class="mdi <?= $ico ?>"></i> <?= htmlspecialchars($kat) ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- ── FILTER STATUS CHIPS ── -->
<div class="su-filter-row">
    <span class="lbl">Status:</span>
    <a href="<?= $sFileName ?>&katFilter=<?= urlencode($katFilter) ?>" class="su-chip <?= $filter == '' ? 'active' : '' ?>">Semua</a>
    <a href="<?= $sFileName ?>&filter=1&katFilter=<?= urlencode($katFilter) ?>" class="su-chip <?= $filter === '1' ? 'active' : '' ?>">
        <i class="mdi mdi-check-circle" style="color:#16a34a"></i> Aktif
    </a>
    <a href="<?= $sFileName ?>&filter=0&katFilter=<?= urlencode($katFilter) ?>" class="su-chip <?= $filter === '0' ? 'active' : '' ?>">
        <i class="mdi mdi-clock-outline" style="color:#ca8a04"></i> Pending
    </a>
    <a href="<?= $sFileName ?>&filter=2&katFilter=<?= urlencode($katFilter) ?>" class="su-chip <?= $filter === '2' ? 'active' : '' ?>">
        <i class="mdi mdi-close-circle" style="color:#dc2626"></i> Tidak Aktif
    </a>
    <span style="margin-left:auto;color:#94a3b8;font-size:11px" id="countInfo">
        <?= $TotalRec ?> usaha ditemukan
    </span>
</div>

<!-- ── PRODUCT GRID ── -->
<div class="su-grid-area">
<?php if (count($allUsaha) == 0): ?>
<div class="su-empty">
    <i class="mdi mdi-store-off"></i>
    <p class="mt-3">Belum ada usaha terdaftar.</p>
    <a href="<?= $sFileRef ?>&action=new" class="su-btn-visit" style="display:inline-flex;margin-top:8px">
        <i class="mdi mdi-plus"></i> Daftarkan Usaha Pertama
    </a>
</div>
<?php else: ?>
<div class="su-grid" id="suGrid">
<?php foreach ($allUsaha as $u):
    $nama    = $u['nama_usaha'];
    $kat     = $u['kategori'];
    $status  = $u['status'];
    $ini     = makeInitials($nama);
    $avColor = hashColor($nama);
    $bannerGrad = isset($katColors[$kat]) ? $katColors[$kat] : array('#475569','#94a3b8');

    $stsCls  = ($status == 1) ? 'aktif' : (($status == 0) ? 'pending' : 'nonaktif');
    $stsLbl  = ($status == 1) ? 'Aktif' : (($status == 0) ? 'Pending' : 'Tidak Aktif');

    $ico     = isset($katIcons[$kat]) ? $katIcons[$kat] : 'mdi-store';

    $terjual = $u['total_terjual'] > 0 ? number_format($u['total_terjual'], 0, ',', '.') : '0';
    $omzet   = $u['total_omzet'] > 1000000
                ? 'Rp ' . number_format($u['total_omzet']/1000000, 1, ',', '.') . ' Jt'
                : 'Rp ' . number_format($u['total_omzet'], 0, ',', '.');
?>
<div class="su-card"
     data-name="<?= strtolower(htmlspecialchars($nama)) ?>"
     data-kat="<?= htmlspecialchars($kat) ?>">

    <!-- Banner -->
    <div class="su-banner">
        <div class="su-banner-bg" style="background:linear-gradient(135deg, <?= $bannerGrad[0] ?> 0%, <?= $bannerGrad[1] ?> 100%)"></div>
        <div class="su-banner-pattern"></div>
        <!-- Status badge -->
        <div class="su-card-status <?= $stsCls ?>"><?= $stsLbl ?></div>
        <!-- Avatar -->
        <div class="su-avatar-wrap">
            <div class="su-avatar" style="background:<?= $avColor ?>">
                <?= $ini ?>
            </div>
        </div>
    </div>

    <!-- Body -->
    <div class="su-card-body">
        <div class="su-store-name" title="<?= htmlspecialchars($nama) ?>"><?= htmlspecialchars($nama) ?></div>
        <?php if ($kat): ?>
        <span class="su-kat-badge">
            <i class="mdi <?= $ico ?>"></i>
            <?= htmlspecialchars($kat) ?>
        </span>
        <?php endif; ?>
        <div class="su-member">
            <i class="mdi mdi-account-circle-outline"></i>
            No. Anggota: <b><?= htmlspecialchars($u['memberID']) ?></b>
        </div>
        <?php if ($u['deskripsi']): ?>
        <div class="su-deskripsi"><?= htmlspecialchars($u['deskripsi']) ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="su-stats-row">
            <div class="su-stat">
                <span class="sv"><?= $u['jml_produk'] ?></span>
                <span class="sk">Produk</span>
            </div>
            <div class="su-stat">
                <span class="sv"><?= $terjual ?></span>
                <span class="sk">Terjual</span>
            </div>
            <div class="su-stat">
                <span class="sv" style="font-size:11px"><?= $u['total_omzet'] > 0 ? $omzet : '-' ?></span>
                <span class="sk">Omzet</span>
            </div>
        </div>
    </div>

    <!-- Action buttons -->
    <div class="su-card-actions">
        <a href="?vw=usaha&mn=<?= $mn ?>&action=view&usahaID=<?= $u['usahaID'] ?>" class="su-btn-visit">
            <i class="mdi mdi-storefront-outline"></i> Kunjungi
        </a>
        <a href="?vw=produkUsahaList&mn=<?= $mn ?>&usahaID=<?= $u['usahaID'] ?>" class="su-btn-produk" title="Lihat Produk">
            <i class="mdi mdi-package-variant"></i> Produk
        </a>
    </div>

    <!-- Admin: pending approval strip -->
    <?php if ($isAdmin && $status == 0): ?>
    <div class="su-pending-strip">
        <span><i class="mdi mdi-alert-circle" style="color:#ca8a04"></i> Menunggu persetujuan</span>
        <span>
            <a href="<?= $sFileName ?>&action=approve&usahaID=<?= $u['usahaID'] ?>"
               class="su-btn-apv" onclick="return confirm('Approve usaha <?= addslashes($nama) ?>?')">
               &#10003; Approve
            </a>
            <a href="<?= $sFileName ?>&action=reject&usahaID=<?= $u['usahaID'] ?>"
               class="su-btn-rej" onclick="return confirm('Reject usaha ini?')">
               &#10007;
            </a>
        </span>
    </div>
    <?php endif; ?>

</div>
<?php endforeach; ?>
</div>

<div class="su-empty" id="suNoResult" style="display:none">
    <i class="mdi mdi-emoticon-sad-outline"></i>
    <p class="mt-3">Tidak ditemukan hasil untuk pencarian Anda.</p>
</div>

<?php endif; ?>
</div><!-- /su-grid-area -->

<!-- ── PAGINATION ── -->
<div class="su-pagination">
    <span>Total <b><?= $TotalRec ?></b> usaha</span>
    <span style="color:#aaa;font-size:11px">Semua data dimuat sekaligus &mdash; gunakan pencarian untuk filter</span>
</div>

</div><!-- /su-wrap -->

<script>
// Real-time search
document.getElementById('suSearch').addEventListener('input', function() {
    var q = this.value.toLowerCase().trim();
    var cards = document.querySelectorAll('.su-card');
    var visible = 0;
    cards.forEach(function(card) {
        var name = card.getAttribute('data-name') || '';
        if (!q || name.indexOf(q) >= 0) { card.style.display = ''; visible++; }
        else card.style.display = 'none';
    });
    var noRes = document.getElementById('suNoResult');
    if (noRes) noRes.style.display = (visible === 0 && document.querySelectorAll('.su-card').length > 0) ? 'block' : 'none';
    var ci = document.getElementById('countInfo');
    if (ci) ci.textContent = visible + ' usaha ditemukan';
});
</script>
