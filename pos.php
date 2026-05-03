<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pos.php
 *      Modul     : Point of Sale - Serba Usaha
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$action        = isset($_POST['action'])        ? $_POST['action']        : (isset($_GET['action'])        ? $_GET['action']        : '');
$cartJson      = isset($_POST['cartJson'])      ? $_POST['cartJson']      : '';
$customerName  = isset($_POST['customerName'])  ? trim($_POST['customerName'])  : '';
$customerPhone = isset($_POST['customerPhone']) ? trim($_POST['customerPhone']) : '';
$metodeBayar   = isset($_POST['metodeBayar'])   ? $_POST['metodeBayar']   : 'Tunai';
$uangBayar     = isset($_POST['uangBayar'])     ? floatval(str_replace('.','',str_replace(',','.',$_POST['uangBayar']))) : 0;
$catatan       = isset($_POST['catatan'])       ? trim($_POST['catatan'])       : '';
$memberType    = isset($_POST['memberType'])    ? $_POST['memberType']    : 'non_anggota';
$memberNo      = isset($_POST['memberNo'])      ? trim($_POST['memberNo'])      : '';
$usahaFilter   = isset($_GET['usahaFilter'])    ? $_GET['usahaFilter']    : '';

$coopName = dlookup("setup", "name", "setupID=" . tosql(1, "Text"));

if (get_magic_quotes_gpc()) {
    $cartJson     = stripslashes($cartJson);
    $customerName = stripslashes($customerName);
    $catatan      = stripslashes($catatan);
}

// --- Proses Checkout ---
if ($action == 'checkout') {
    $now     = date("Y-m-d H:i:s");
    $by      = $customerName ? $customerName : 'guest';
    $rsNo    = $conn->Execute("SELECT MAX(CAST(RIGHT(orderNo,6) AS SIGNED INTEGER)) AS n FROM pos_order");
    $nomor   = ($rsNo && !$rsNo->EOF) ? intval($rsNo->fields('n')) + 1 : 1;
    $orderNo = 'POS' . sprintf("%06d", $nomor);

    $cartItems = json_decode($cartJson, true);

    if (!$cartItems || count($cartItems) == 0) {
        $errCheckout = "Keranjang kosong.";
    } elseif (!$customerName) {
        $errCheckout = "Nama pembeli wajib diisi.";
    } else {
        $totalAmt = 0;
        foreach ($cartItems as $item) { $totalAmt += floatval($item['subtotal']); }

        $conn->Execute("INSERT INTO pos_order
            (orderNo, customerName, customerPhone, member_type, member_no, catatan, totalAmt, status, createdDate, createdBy)
            VALUES ("
            . tosql($orderNo, "Text") . ","
            . tosql($customerName, "Text") . ","
            . tosql($customerPhone, "Text") . ","
            . tosql($memberType, "Text") . ","
            . tosql($memberNo, "Text") . ","
            . tosql($catatan, "Text") . ","
            . tosql($totalAmt, "Number") . ",2,'$now',"
            . tosql($by, "Text") . ")");
        $orderID = $conn->Insert_ID();

        foreach ($cartItems as $item) {
            $conn->Execute("INSERT INTO pos_order_detail
                (orderID, produkID, usahaID, nama_produk, qty, harga, subtotal)
                VALUES ("
                . tosql($orderID, "Number") . ","
                . tosql($item['produkID'], "Number") . ","
                . tosql($item['usahaID'], "Number") . ","
                . tosql($item['nama_produk'], "Text") . ","
                . tosql($item['qty'], "Number") . ","
                . tosql($item['harga'], "Number") . ","
                . tosql($item['subtotal'], "Number") . ")");

            $rsSaldo = $conn->Execute(
                "SELECT COALESCE(SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END),0) AS saldo
                 FROM stok_usaha WHERE produkID=" . tosql($item['produkID'], "Number"));
            $stokBaru = ($rsSaldo && !$rsSaldo->EOF) ? floatval($rsSaldo->fields('saldo')) - floatval($item['qty']) : 0;
            $conn->Execute("INSERT INTO stok_usaha
                (produkID, usahaID, jenis, qty, stok_akhir, harga, keterangan, tarikh, createdDate, createdBy)
                VALUES ("
                . tosql($item['produkID'], "Number") . ","
                . tosql($item['usahaID'], "Number") . ",'keluar',"
                . tosql($item['qty'], "Number") . ","
                . tosql($stokBaru, "Number") . ","
                . tosql($item['harga'], "Number") . ","
                . "'Penjualan POS #$orderNo','" . date("Y-m-d") . "','$now',"
                . tosql($by, "Text") . ")");
        }
        print '<script>window.location="posReceipt.php?orderID=' . $orderID . '";</script>';
        exit;
    }
}

// --- Load Usaha ---
$rsUsaha = $conn->Execute("SELECT * FROM usaha WHERE status=1 ORDER BY nama_usaha");
$usahaList = array();
while ($rsUsaha && !$rsUsaha->EOF) {
    $usahaList[] = array(
        'usahaID'    => $rsUsaha->fields('usahaID'),
        'nama_usaha' => $rsUsaha->fields('nama_usaha'),
    );
    $rsUsaha->MoveNext();
}

// --- Load Produk ---
$sWhere = "WHERE p.status=1 AND u.status=1";
if ($usahaFilter) $sWhere .= " AND p.usahaID=" . tosql($usahaFilter, "Number");

$rsProduk = $conn->Execute(
    "SELECT p.produkID, p.usahaID, p.nama_produk, p.harga_jual, p.harga_anggota, p.harga_non_anggota, p.kategori, p.deskripsi, p.barcode,
            u.nama_usaha,
            COALESCE((SELECT SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END)
                      FROM stok_usaha WHERE produkID=p.produkID),0) AS stok
     FROM produk_usaha p
     JOIN usaha u ON p.usahaID=u.usahaID
     $sWhere
     ORDER BY u.nama_usaha, p.nama_produk");

$products = array();
$categories = array();
while ($rsProduk && !$rsProduk->EOF) {
    $kat = $rsProduk->fields('kategori');
    $products[] = array(
        'produkID'         => $rsProduk->fields('produkID'),
        'usahaID'          => $rsProduk->fields('usahaID'),
        'nama_produk'      => $rsProduk->fields('nama_produk'),
        'harga_jual'       => floatval($rsProduk->fields('harga_jual')),
        'harga_anggota'    => floatval($rsProduk->fields('harga_anggota')),
        'harga_non_anggota'=> floatval($rsProduk->fields('harga_non_anggota')),
        'kategori'         => $kat,
        'deskripsi'        => $rsProduk->fields('deskripsi'),
        'nama_usaha'       => $rsProduk->fields('nama_usaha'),
        'stok'             => floatval($rsProduk->fields('stok')),
        'barcode'          => $rsProduk->fields('barcode'),
    );
    if ($kat && !in_array($kat, $categories)) $categories[] = $kat;
    $rsProduk->MoveNext();
}
sort($categories);

// Category color map
$catColors = array(
    'Makanan'       => '#e74c3c',
    'Minuman'       => '#3498db',
    'Pakaian'       => '#9b59b6',
    'Elektronik'    => '#1abc9c',
    'Pertanian'     => '#27ae60',
    'Perkhidmatan'  => '#f39c12',
    'Kraftangan'    => '#e67e22',
);
$catIcons = array(
    'Makanan'       => 'mdi-food',
    'Minuman'       => 'mdi-cup',
    'Pakaian'       => 'mdi-tshirt-crew',
    'Elektronik'    => 'mdi-laptop',
    'Pertanian'     => 'mdi-sprout',
    'Perkhidmatan'  => 'mdi-tools',
    'Kraftangan'    => 'mdi-hand-extended',
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS &mdash; <?= htmlspecialchars($coopName) ?></title>
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/icons.min.css" rel="stylesheet">
<link rel="shortcut icon" href="assets/images/favicon.png">
<style>
:root {
  --dark:    #1e2d3d;
  --accent:  #3b82f6;
  --green:   #22c55e;
  --red:     #ef4444;
  --orange:  #f59e0b;
  --bg:      #f1f5f9;
  --card-bg: #ffffff;
  --border:  #e2e8f0;
  --text:    #1e293b;
  --muted:   #94a3b8;
}

* { box-sizing: border-box; }
html, body { height: 100%; margin: 0; }
body {
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  background: var(--bg);
  font-size: 13px;
  color: var(--text);
}

/* ── TOP BAR ── */
.topbar {
  background: var(--dark);
  height: 52px;
  display: flex;
  align-items: center;
  padding: 0 20px;
  gap: 14px;
  position: sticky;
  top: 0;
  z-index: 50;
  box-shadow: 0 2px 8px rgba(0,0,0,.3);
}
.topbar .brand {
  color: #fff;
  font-size: 16px;
  font-weight: 700;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 7px;
}
.topbar .brand span { color: var(--orange); }
.search-wrap {
  flex: 1;
  max-width: 400px;
  position: relative;
}
.search-wrap input {
  width: 100%;
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.2);
  color: #fff;
  border-radius: 8px;
  padding: 6px 12px 6px 36px;
  font-size: 13px;
  outline: none;
  transition: background .2s;
}
.search-wrap input::placeholder { color: rgba(255,255,255,.5); }
.search-wrap input:focus { background: rgba(255,255,255,.2); border-color: var(--accent); }
.search-wrap .ico {
  position: absolute;
  left: 11px; top: 50%;
  transform: translateY(-50%);
  color: rgba(255,255,255,.5);
  font-size: 15px;
  pointer-events: none;
}
.barcode-wrap {
  position: relative;
  width: 200px;
}
.barcode-wrap input {
  width: 100%;
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.2);
  color: #fff;
  border-radius: 8px;
  padding: 6px 12px 6px 34px;
  font-size: 13px;
  outline: none;
  transition: background .2s;
}
.barcode-wrap input::placeholder { color: rgba(255,255,255,.5); }
.barcode-wrap input:focus { background: rgba(255,255,255,.2); border-color: var(--orange); }
.barcode-wrap .ico {
  position: absolute;
  left: 10px; top: 50%;
  transform: translateY(-50%);
  color: rgba(255,255,255,.5);
  font-size: 15px;
  pointer-events: none;
}
.topbar .spacer { flex: 1; }
.topbar .time-badge {
  color: rgba(255,255,255,.7);
  font-size: 12px;
  white-space: nowrap;
}

/* ── LAYOUT ── */
.pos-layout {
  display: flex;
  height: calc(100vh - 52px);
  overflow: hidden;
}

/* ── LEFT PANEL (Products) ── */
.left-panel {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Filter Bar */
.filter-bar {
  background: #fff;
  border-bottom: 1px solid var(--border);
  padding: 10px 16px;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  align-items: center;
}
.filter-chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  border: 1.5px solid var(--border);
  background: #fff;
  color: var(--text);
  cursor: pointer;
  text-decoration: none;
  transition: all .15s;
  white-space: nowrap;
}
.filter-chip:hover { border-color: var(--accent); color: var(--accent); }
.filter-chip.active { background: var(--dark); color: #fff; border-color: var(--dark); }
.filter-chip i { font-size: 13px; }

/* Category tabs */
.cat-bar {
  background: #fff;
  border-bottom: 1px solid var(--border);
  padding: 0 16px;
  display: flex;
  gap: 0;
  overflow-x: auto;
  scrollbar-width: none;
}
.cat-bar::-webkit-scrollbar { display: none; }
.cat-tab {
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  color: var(--muted);
  white-space: nowrap;
  user-select: none;
  transition: all .15s;
}
.cat-tab:hover { color: var(--text); }
.cat-tab.active { color: var(--accent); border-bottom-color: var(--accent); }

/* Product area */
.product-area {
  flex: 1;
  overflow-y: auto;
  padding: 14px 16px;
}

/* Product grid */
.prod-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
  gap: 12px;
}

.prod-card {
  background: var(--card-bg);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,.07);
  border: 1.5px solid var(--border);
  transition: all .15s;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}
.prod-card:hover:not(.sold-out) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,.1);
  border-color: var(--accent);
}
.prod-card.sold-out { opacity: .55; cursor: not-allowed; }

.prod-thumb {
  height: 90px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  color: rgba(255,255,255,.9);
  position: relative;
}
.prod-thumb .cat-badge {
  position: absolute;
  top: 6px; right: 6px;
  background: rgba(0,0,0,.25);
  color: #fff;
  font-size: 9px;
  padding: 2px 6px;
  border-radius: 10px;
}
.prod-info {
  padding: 10px;
  flex: 1;
  display: flex;
  flex-direction: column;
}
.prod-name {
  font-weight: 700;
  font-size: 12.5px;
  line-height: 1.3;
  margin-bottom: 2px;
  color: var(--text);
}
.prod-usaha {
  font-size: 10.5px;
  color: var(--muted);
  margin-bottom: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.prod-price {
  font-size: 14px;
  font-weight: 800;
  color: var(--dark);
  margin-bottom: 4px;
}
.prod-barcode {
  font-size: 10px;
  color: var(--muted);
  margin-bottom: 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: monospace;
  letter-spacing: .5px;
}
.prod-stok {
  font-size: 10.5px;
  margin-bottom: 8px;
}
.prod-stok.ok  { color: var(--green); }
.prod-stok.low { color: var(--orange); }
.prod-stok.out { color: var(--red); }

.btn-add {
  width: 100%;
  padding: 6px;
  border: none;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  transition: all .15s;
}
.btn-add.can-add {
  background: var(--accent);
  color: #fff;
}
.btn-add.can-add:hover { background: #2563eb; }
.btn-add.added {
  background: #dcfce7;
  color: var(--green);
  cursor: default;
}
.btn-add:disabled {
  background: #f1f5f9;
  color: var(--muted);
  cursor: not-allowed;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--muted);
}
.empty-state i { font-size: 52px; }

/* ── RIGHT PANEL (Cart) ── */
.right-panel {
  width: 340px;
  background: var(--card-bg);
  border-left: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.cart-head {
  background: var(--dark);
  color: #fff;
  padding: 14px 16px;
  font-size: 15px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.cart-head .cart-count {
  background: var(--orange);
  border-radius: 12px;
  padding: 2px 10px;
  font-size: 12px;
}

.cart-items-wrap {
  flex: 1;
  overflow-y: auto;
  padding: 8px 0;
}

.cart-empty-msg {
  text-align: center;
  padding: 40px 20px;
  color: var(--muted);
}
.cart-empty-msg i { font-size: 40px; }

.cart-row {
  padding: 10px 14px;
  border-bottom: 1px solid #f8fafc;
  display: flex;
  gap: 10px;
  align-items: flex-start;
}
.cart-row:hover { background: #f8fafc; }
.cart-icon {
  width: 36px; height: 36px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; color: #fff;
  flex-shrink: 0;
}
.cart-details { flex: 1; min-width: 0; }
.cart-pname {
  font-weight: 700;
  font-size: 12.5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cart-pusaha { font-size: 10.5px; color: var(--muted); }
.cart-sub { font-weight: 800; font-size: 13px; color: var(--dark); white-space: nowrap; }
.cart-del {
  background: none; border: none;
  color: var(--muted); cursor: pointer;
  padding: 2px; font-size: 15px;
  line-height: 1;
}
.cart-del:hover { color: var(--red); }

.qty-box {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 4px;
}
.qty-box button {
  width: 22px; height: 22px;
  border-radius: 6px;
  border: 1.5px solid var(--border);
  background: var(--bg);
  font-size: 14px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700;
  line-height: 1;
  color: var(--text);
}
.qty-box button:hover { background: var(--border); }
.qty-box .qty-val {
  font-size: 13px;
  font-weight: 700;
  min-width: 22px;
  text-align: center;
}

/* Cart Footer */
.cart-foot {
  border-top: 2px solid var(--border);
  padding: 14px;
  background: #f8fafc;
}
.cart-summary-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 6px;
  font-size: 13px;
}
.cart-total-line {
  display: flex;
  justify-content: space-between;
  margin: 10px 0 12px;
  font-size: 16px;
  font-weight: 800;
  color: var(--dark);
  border-top: 1.5px solid var(--border);
  padding-top: 10px;
}
.kembalian-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  font-size: 13px;
  font-weight: 700;
}
.kembalian-val.ok  { color: var(--green); }
.kembalian-val.err { color: var(--red); }

.metode-btns {
  display: flex;
  gap: 6px;
  margin-bottom: 10px;
}
.metode-btn {
  flex: 1;
  padding: 6px 4px;
  border-radius: 8px;
  border: 1.5px solid var(--border);
  background: #fff;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  text-align: center;
  color: var(--text);
  transition: all .15s;
}
.metode-btn.active { background: var(--dark); color: #fff; border-color: var(--dark); }

.bayar-input {
  width: 100%;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  padding: 7px 10px;
  font-size: 14px;
  font-weight: 700;
  text-align: right;
  margin-bottom: 10px;
  outline: none;
  background: #fff;
}
.bayar-input:focus { border-color: var(--accent); }

.btn-checkout-main {
  width: 100%;
  padding: 12px;
  background: var(--green);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background .15s;
}
.btn-checkout-main:hover:not(:disabled) { background: #16a34a; }
.btn-checkout-main:disabled { background: var(--muted); cursor: not-allowed; }

.btn-clear-cart {
  width: 100%;
  margin-top: 6px;
  padding: 7px;
  background: none;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  font-size: 12px;
  color: var(--muted);
  cursor: pointer;
}
.btn-clear-cart:hover { border-color: var(--red); color: var(--red); }

/* Toast */
.toast-wrap {
  position: fixed;
  bottom: 24px; left: 50%;
  transform: translateX(-50%);
  z-index: 999;
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: center;
  pointer-events: none;
}
.toast-msg {
  background: rgba(30,45,61,.92);
  color: #fff;
  padding: 8px 18px;
  border-radius: 20px;
  font-size: 12.5px;
  font-weight: 600;
  animation: toastIn .25s ease;
  white-space: nowrap;
}
@keyframes toastIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Checkout Modal */
.modal-header { background: var(--dark); color: #fff; border-radius: 12px 12px 0 0; }
.modal-header .btn-close { filter: invert(1); }
.modal-content { border-radius: 12px; border: none; }
.order-item-row {
  display: flex;
  justify-content: space-between;
  padding: 5px 0;
  font-size: 13px;
  border-bottom: 1px solid #f1f5f9;
}
.order-total-row {
  display: flex;
  justify-content: space-between;
  font-size: 15px;
  font-weight: 800;
  padding-top: 10px;
  color: var(--dark);
}

/* Scrollbar */
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

@media (max-width: 768px) {
  .right-panel { display: none; }
  .topbar .time-badge { display: none; }
}
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
  <div class="brand">
    <i class="mdi mdi-point-of-sale"></i>
    <?= $coopName ? htmlspecialchars($coopName) : 'Serba Usaha' ?>
  </div>
  <div class="search-wrap">
    <i class="mdi mdi-magnify ico"></i>
    <input type="text" id="searchInput" placeholder="Cari produk... (F2)" autocomplete="off">
  </div>
  <div class="barcode-wrap">
    <i class="mdi mdi-barcode-scan ico"></i>
    <input type="text" id="barcodeInput" placeholder="Scan barcode... (F3)" autocomplete="off">
  </div>
  <div class="spacer"></div>
  <div class="time-badge" id="liveClock"></div>
</div>

<!-- POS LAYOUT -->
<div class="pos-layout">

  <!-- ═══ LEFT PANEL ═══ -->
  <div class="left-panel">

    <!-- Usaha Filter -->
    <div class="filter-bar">
      <a href="pos.php" class="filter-chip <?= $usahaFilter == '' ? 'active' : '' ?>">
        <i class="mdi mdi-store-outline"></i> Semua Usaha
      </a>
      <?php foreach ($usahaList as $u): ?>
      <a href="pos.php?usahaFilter=<?= $u['usahaID'] ?>"
         class="filter-chip <?= $usahaFilter == $u['usahaID'] ? 'active' : '' ?>">
        <?= htmlspecialchars($u['nama_usaha']) ?>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Category Tabs -->
    <?php if (count($categories) > 0): ?>
    <div class="cat-bar" id="catBar">
      <div class="cat-tab active" data-cat="">Semua</div>
      <?php foreach ($categories as $cat): ?>
      <div class="cat-tab" data-cat="<?= htmlspecialchars($cat) ?>">
        <i class="mdi <?= isset($catIcons[$cat]) ? $catIcons[$cat] : 'mdi-package-variant' ?>"></i>
        <?= htmlspecialchars($cat) ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Product Grid -->
    <div class="product-area">
      <?php if (count($products) == 0): ?>
      <div class="empty-state">
        <i class="mdi mdi-package-variant-closed"></i>
        <p class="mt-3">Tidak ada produk tersedia.</p>
      </div>
      <?php else: ?>

      <?php if (!empty($errCheckout)): ?>
      <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <?= $errCheckout ?>
      </div>
      <?php endif; ?>

      <div class="prod-grid" id="prodGrid">
        <?php foreach ($products as $p):
          $habis   = $p['stok'] <= 0;
          $stokLow = $p['stok'] > 0 && $p['stok'] <= 5;
          $bgColor = isset($catColors[$p['kategori']]) ? $catColors[$p['kategori']] : '#475569';
          $icoClass= isset($catIcons[$p['kategori']]) ? $catIcons[$p['kategori']] : 'mdi-package-variant';
          if ($p['stok'] > 100) { $stokTxt = 'Stok banyak'; $stokCls = 'ok'; }
          elseif ($p['stok'] > 5) { $stokTxt = 'Stok: '.(int)$p['stok']; $stokCls = 'ok'; }
          elseif ($p['stok'] > 0) { $stokTxt = 'Sisa: '.(int)$p['stok']; $stokCls = 'low'; }
          else { $stokTxt = 'Habis'; $stokCls = 'out'; }
        ?>
        <div class="prod-card <?= $habis ? 'sold-out' : '' ?>"
             data-name="<?= strtolower(htmlspecialchars($p['nama_produk'])) ?>"
             data-kat="<?= htmlspecialchars($p['kategori']) ?>"
             data-usaha="<?= htmlspecialchars($p['nama_usaha']) ?>"
             data-barcode="<?= htmlspecialchars($p['barcode']) ?>"
             data-harga-anggota="<?= $p['harga_anggota'] ?>"
             data-harga-non="<?= $p['harga_non_anggota'] ?>"
             data-harga-jual="<?= $p['harga_jual'] ?>">
          <div class="prod-thumb" style="background:<?= $bgColor ?>">
            <i class="mdi <?= $icoClass ?>"></i>
            <?php if ($p['kategori']): ?>
            <span class="cat-badge"><?= htmlspecialchars($p['kategori']) ?></span>
            <?php endif; ?>
          </div>
          <div class="prod-info">
            <div class="prod-name"><?= htmlspecialchars($p['nama_produk']) ?></div>
            <div class="prod-usaha"><i class="mdi mdi-store" style="font-size:10px"></i> <?= htmlspecialchars($p['nama_usaha']) ?></div>
            <div class="prod-price" id="price_<?= $p['produkID'] ?>">Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></div>
            <?php if ($p['barcode']): ?>
            <div class="prod-barcode">
              <i class="mdi mdi-barcode" style="font-size:10px"></i>
              <?= htmlspecialchars($p['barcode']) ?>
            </div>
            <?php endif; ?>
            <div class="prod-stok <?= $stokCls ?>">
              <i class="mdi <?= $habis ? 'mdi-close-circle' : ($stokLow ? 'mdi-alert-circle' : 'mdi-check-circle') ?>"></i>
              <?= $stokTxt ?>
            </div>
            <?php if (!$habis): ?>
            <button class="btn-add can-add" id="btn_<?= $p['produkID'] ?>"
                    onclick="addToCartAuto(<?= $p['produkID'] ?>,<?= $p['usahaID'] ?>,'<?= addslashes($p['nama_produk']) ?>','<?= addslashes($p['nama_usaha']) ?>',<?= $p['stok'] ?>,'<?= $bgColor ?>')">
              <i class="mdi mdi-cart-plus"></i> Tambah
            </button>
            <?php else: ?>
            <button class="btn-add" disabled>
              <i class="mdi mdi-block-helper"></i> Habis
            </button>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="empty-state" id="noResult" style="display:none">
        <i class="mdi mdi-emoticon-sad-outline"></i>
        <p class="mt-3">Produk tidak ditemukan.</p>
      </div>

      <?php endif; ?>
    </div>
  </div><!-- /left-panel -->

  <!-- ═══ RIGHT PANEL (Cart) ═══ -->
  <div class="right-panel">
    <div class="cart-head">
      <span><i class="mdi mdi-cart"></i> Keranjang</span>
      <span class="cart-count" id="cartCount">0 item</span>
    </div>
    <!-- Member toggle -->
    <div style="background:#f8fafc;border-bottom:1px solid var(--border);padding:8px 14px;display:flex;align-items:center;gap:8px">
      <span style="font-size:11px;font-weight:600;color:var(--muted)">Pelanggan:</span>
      <button class="metode-btn active" id="btnNonAnggota" style="flex:1;padding:4px 6px;font-size:11px" onclick="setMemberType('non_anggota')">
        <i class="mdi mdi-account-outline"></i> Bukan Anggota
      </button>
      <button class="metode-btn" id="btnAnggota" style="flex:1;padding:4px 6px;font-size:11px" onclick="setMemberType('anggota')">
        <i class="mdi mdi-account-check"></i> Anggota
      </button>
    </div>

    <div class="cart-items-wrap" id="cartWrap">
      <div class="cart-empty-msg" id="cartEmpty">
        <i class="mdi mdi-cart-outline"></i>
        <p class="mt-2" style="font-size:13px">Belum ada item.<br>Klik produk untuk tambah.</p>
      </div>
      <div id="cartItems"></div>
    </div>

    <div class="cart-foot">
      <div class="cart-summary-line">
        <span class="text-muted">Item:</span>
        <span id="summaryItem">0 item</span>
      </div>
      <div class="cart-total-line">
        <span>Total</span>
        <span id="cartTotal">Rp 0</span>
      </div>

      <!-- Metode Bayar -->
      <div class="metode-btns" id="metodeBtns">
        <button class="metode-btn active" data-metode="Tunai" onclick="setMetode(this)">
          <i class="mdi mdi-cash"></i><br>Tunai
        </button>
        <button class="metode-btn" data-metode="Transfer" onclick="setMetode(this)">
          <i class="mdi mdi-bank"></i><br>Transfer
        </button>
        <button class="metode-btn" data-metode="QRIS" onclick="setMetode(this)">
          <i class="mdi mdi-qrcode"></i><br>QRIS
        </button>
        <button class="metode-btn" data-metode="Debit" onclick="setMetode(this)">
          <i class="mdi mdi-credit-card"></i><br>Debit
        </button>
      </div>

      <!-- Uang Bayar (Tunai saja) -->
      <div id="tunaiSection">
        <input type="text" class="bayar-input" id="uangBayarInput"
               placeholder="Masukkan nominal bayar..."
               oninput="hitungKembalian()"
               onkeydown="if(event.key==='Enter') submitCheckout()">
        <div class="kembalian-line">
          <span class="text-muted">Kembalian:</span>
          <span id="kembalianVal" class="kembalian-val">—</span>
        </div>
      </div>

      <button class="btn-checkout-main" id="btnCheckout" onclick="submitCheckout()" disabled>
        <i class="mdi mdi-check-circle-outline"></i>
        <span id="btnCheckoutLbl">Bayar</span>
      </button>
      <button class="btn-clear-cart" onclick="clearCart()">
        <i class="mdi mdi-delete-sweep"></i> Kosongkan Keranjang
      </button>
    </div>
  </div><!-- /right-panel -->

</div><!-- /pos-layout -->

<!-- Toast container -->
<div class="toast-wrap" id="toastWrap"></div>

<!-- Checkout Modal (hidden, submit via JS) -->
<form method="post" action="pos.php" id="checkoutForm" style="display:none">
  <input type="hidden" name="action" value="checkout">
  <input type="hidden" name="cartJson" id="cartJsonInput">
  <input type="hidden" name="customerName" id="fCustomerName">
  <input type="hidden" name="customerPhone" id="fCustomerPhone">
  <input type="hidden" name="metodeBayar" id="fMetodeBayar">
  <input type="hidden" name="uangBayar" id="fUangBayar">
  <input type="hidden" name="catatan" id="fCatatan">
  <input type="hidden" name="memberType" id="fMemberType" value="non_anggota">
  <input type="hidden" name="memberNo" id="fMemberNo" value="">
</form>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="modalCheckout" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="mdi mdi-cash-register"></i> Konfirmasi Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="modalSummary" class="mb-3"></div>
        <div class="row g-2">
          <div class="col-8">
            <label class="form-label fw-bold mb-1" style="font-size:12px">* Nama Pembeli</label>
            <input type="text" class="form-control form-control-sm" id="mCustomerName" placeholder="Nama pembeli" required>
          </div>
          <div class="col-4">
            <label class="form-label mb-1" style="font-size:12px">No. HP</label>
            <input type="text" class="form-control form-control-sm" id="mCustomerPhone" placeholder="08xx...">
          </div>
          <div class="col-12" id="memberNoRow" style="display:none">
            <label class="form-label fw-bold mb-1" style="font-size:12px;color:var(--accent)">
              <i class="mdi mdi-account-check"></i> No. Anggota
            </label>
            <input type="text" class="form-control form-control-sm" id="mMemberNo" placeholder="Masukkan nomor anggota...">
          </div>
          <div class="col-12">
            <label class="form-label mb-1" style="font-size:12px">Catatan</label>
            <input type="text" class="form-control form-control-sm" id="mCatatan" placeholder="Catatan pesanan...">
          </div>
        </div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-success fw-bold px-4" onclick="doCheckout()">
          <i class="mdi mdi-check"></i> Proses Pembayaran
        </button>
      </div>
    </div>
  </div>
</div>

<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
var cart = {};
var activeMetode = 'Tunai';
var activeMemberType = 'non_anggota';
var cartColor = {};  // produkID → bgColor

/* ── HELPERS ── */
function fmt(n) {
  return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function fmtPlain(n) {
  return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function toast(msg, dur) {
  var t = document.createElement('div');
  t.className = 'toast-msg';
  t.textContent = msg;
  document.getElementById('toastWrap').appendChild(t);
  setTimeout(function() { if (t.parentNode) t.parentNode.removeChild(t); }, dur || 2000);
}

/* ── CLOCK ── */
function updateClock() {
  var d = new Date();
  var days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
  var h = d.getHours().toString().padStart(2,'0');
  var m = d.getMinutes().toString().padStart(2,'0');
  var s = d.getSeconds().toString().padStart(2,'0');
  document.getElementById('liveClock').textContent =
    days[d.getDay()] + ', ' + d.getDate() + '/' + (d.getMonth()+1) + '/' + d.getFullYear() +
    ' ' + h + ':' + m + ':' + s;
}
setInterval(updateClock, 1000);
updateClock();

/* ── SEARCH & FILTER ── */
var activeCat = '';

document.getElementById('searchInput').addEventListener('input', filterProducts);
document.addEventListener('keydown', function(e) {
  if (e.key === 'F2') { e.preventDefault(); document.getElementById('searchInput').focus(); }
  if (e.key === 'F3') { e.preventDefault(); document.getElementById('barcodeInput').focus(); }
  if (e.key === 'Escape') {
    document.getElementById('searchInput').value = '';
    document.getElementById('barcodeInput').value = '';
    filterProducts();
  }
});

document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
  if (e.key !== 'Enter') return;
  var bc = this.value.trim();
  this.value = '';
  if (!bc) return;
  var found = false;
  document.querySelectorAll('.prod-card').forEach(function(card) {
    if (card.getAttribute('data-barcode') === bc) {
      var btn = card.querySelector('.btn-add.can-add');
      if (btn) { btn.click(); found = true; }
      else { toast('Stok habis untuk barcode: ' + bc); found = true; }
    }
  });
  if (!found) toast('Barcode tidak ditemukan: ' + bc);
});

document.querySelectorAll('.cat-tab').forEach(function(tab) {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.cat-tab').forEach(function(t) { t.classList.remove('active'); });
    this.classList.add('active');
    activeCat = this.getAttribute('data-cat');
    filterProducts();
  });
});

function filterProducts() {
  var q = document.getElementById('searchInput').value.toLowerCase().trim();
  var cards = document.querySelectorAll('.prod-card');
  var visible = 0;
  cards.forEach(function(card) {
    var name   = card.getAttribute('data-name') || '';
    var kat    = card.getAttribute('data-kat') || '';
    var usaha  = card.getAttribute('data-usaha') || '';
    var matchQ = !q || name.indexOf(q) >= 0 || usaha.toLowerCase().indexOf(q) >= 0;
    var matchC = !activeCat || kat === activeCat;
    if (matchQ && matchC) { card.style.display = ''; visible++; }
    else card.style.display = 'none';
  });
  var noRes = document.getElementById('noResult');
  if (noRes) noRes.style.display = (visible === 0) ? 'block' : 'none';
}

/* ── MEMBER TYPE ── */
function setMemberType(type) {
  activeMemberType = type;
  document.getElementById('btnAnggota').classList.toggle('active', type === 'anggota');
  document.getElementById('btnNonAnggota').classList.toggle('active', type === 'non_anggota');
  updateAllPriceLabels();
  rebuildCartPrices();
}

function getCardPrice(card) {
  var hA  = parseFloat(card.getAttribute('data-harga-anggota')) || 0;
  var hN  = parseFloat(card.getAttribute('data-harga-non'))     || 0;
  var hJ  = parseFloat(card.getAttribute('data-harga-jual'))    || 0;
  if (activeMemberType === 'anggota')     return hA > 0 ? hA : hJ;
  if (activeMemberType === 'non_anggota') return hN > 0 ? hN : hJ;
  return hJ;
}

function updateAllPriceLabels() {
  document.querySelectorAll('.prod-card').forEach(function(card) {
    var pid = card.querySelector('.btn-add') ? card.querySelector('.btn-add').id.replace('btn_','') : null;
    if (!pid) return;
    var priceEl = document.getElementById('price_' + pid);
    if (priceEl) priceEl.textContent = 'Rp ' + fmtPlain(getCardPrice(card));
  });
}

function rebuildCartPrices() {
  Object.keys(cart).forEach(function(key) {
    var item = cart[key];
    var card = document.querySelector('.prod-card[data-name]');
    // Find the card matching this produkID
    document.querySelectorAll('.prod-card').forEach(function(c) {
      var btn = c.querySelector('#btn_' + item.produkID);
      if (btn) {
        var newHarga = getCardPrice(c);
        item.harga   = newHarga;
        item.subtotal = item.qty * newHarga;
      }
    });
  });
  renderCart();
}

/* ── CART ── */
function addToCartAuto(produkID, usahaID, nama, namaUsaha, stok, color) {
  var card = document.getElementById('btn_' + produkID);
  var parentCard = card ? card.closest('.prod-card') : null;
  var harga = parentCard ? getCardPrice(parentCard) : 0;
  addToCart(produkID, usahaID, nama, harga, namaUsaha, stok, color);
}

function addToCart(produkID, usahaID, nama, harga, namaUsaha, stok, color) {
  var key = 'p' + produkID;
  cartColor[produkID] = color || '#475569';
  if (cart[key]) {
    if (cart[key].qty >= stok) { toast('Stok tidak cukup!'); return; }
    cart[key].qty++;
    cart[key].subtotal = cart[key].qty * cart[key].harga;
    toast('+ ' + nama + ' (' + cart[key].qty + ')');
  } else {
    cart[key] = { produkID: produkID, usahaID: usahaID, nama_produk: nama,
                  harga: harga, nama_usaha: namaUsaha, stok: stok, qty: 1, subtotal: harga };
    toast('Ditambah: ' + nama);
  }
  renderCart();
}

function removeItem(key) {
  delete cart[key];
  renderCart();
}

function changeQty(key, delta) {
  if (!cart[key]) return;
  cart[key].qty += delta;
  if (cart[key].qty <= 0) { delete cart[key]; }
  else {
    if (cart[key].qty > cart[key].stok) { cart[key].qty = cart[key].stok; toast('Batas stok!'); }
    cart[key].subtotal = cart[key].qty * cart[key].harga;
  }
  renderCart();
}

function clearCart() {
  if (Object.keys(cart).length === 0) return;
  if (!confirm('Kosongkan keranjang?')) return;
  cart = {};
  renderCart();
}

function renderCart() {
  var keys = Object.keys(cart);
  var totalQty = 0, total = 0, html = '';

  keys.forEach(function(key) {
    var item = cart[key];
    totalQty += item.qty;
    total += item.subtotal;
    var bg = cartColor[item.produkID] || '#475569';
    html += '<div class="cart-row">'
      + '<div class="cart-icon" style="background:' + bg + '">'
      + '<i class="mdi mdi-package-variant" style="font-size:16px;color:#fff"></i></div>'
      + '<div class="cart-details">'
      + '<div class="cart-pname">' + item.nama_produk + '</div>'
      + '<div class="cart-pusaha">' + item.nama_usaha + '</div>'
      + '<div class="qty-box">'
      + '<button onclick="changeQty(\'' + key + '\',-1)">&#8722;</button>'
      + '<span class="qty-val">' + item.qty + '</span>'
      + '<button onclick="changeQty(\'' + key + '\',1)">&#43;</button>'
      + '<span style="margin-left:4px;font-size:11px;color:var(--muted)">&times; ' + fmtPlain(item.harga) + '</span>'
      + '</div>'
      + '</div>'
      + '<div style="text-align:right;flex-shrink:0">'
      + '<div class="cart-sub">' + fmtPlain(item.subtotal) + '</div>'
      + '<button class="cart-del" onclick="removeItem(\'' + key + '\')" title="Hapus">'
      + '<i class="mdi mdi-close-circle"></i></button>'
      + '</div>'
      + '</div>';
  });

  document.getElementById('cartItems').innerHTML = html;
  document.getElementById('cartEmpty').style.display = keys.length === 0 ? 'block' : 'none';
  document.getElementById('cartCount').textContent = totalQty + ' item';
  document.getElementById('summaryItem').textContent = keys.length + ' produk, ' + totalQty + ' pcs';
  document.getElementById('cartTotal').textContent = fmt(total);
  document.getElementById('btnCheckout').disabled = keys.length === 0;
  document.getElementById('btnCheckoutLbl').textContent = 'Bayar ' + fmt(total);
  hitungKembalian();
}

/* ── METODE BAYAR ── */
function setMetode(btn) {
  document.querySelectorAll('.metode-btn').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  activeMetode = btn.getAttribute('data-metode');
  document.getElementById('tunaiSection').style.display = activeMetode === 'Tunai' ? 'block' : 'none';
  hitungKembalian();
}

function hitungKembalian() {
  var totalEl = document.getElementById('cartTotal').textContent;
  var total = parseInt(totalEl.replace(/[^0-9]/g,'')) || 0;
  var raw = document.getElementById('uangBayarInput').value.replace(/[^0-9]/g,'');
  var bayar = parseInt(raw) || 0;
  var el = document.getElementById('kembalianVal');
  if (!bayar) { el.textContent = '—'; el.className = 'kembalian-val'; return; }
  var kem = bayar - total;
  el.textContent = fmt(kem);
  el.className = 'kembalian-val ' + (kem >= 0 ? 'ok' : 'err');
}

/* ── CHECKOUT ── */
function submitCheckout() {
  var keys = Object.keys(cart);
  if (keys.length === 0) return;

  // Validasi uang tunai
  if (activeMetode === 'Tunai') {
    var totalEl = document.getElementById('cartTotal').textContent;
    var total = parseInt(totalEl.replace(/[^0-9]/g,'')) || 0;
    var raw = document.getElementById('uangBayarInput').value.replace(/[^0-9]/g,'');
    var bayar = parseInt(raw) || 0;
    if (bayar > 0 && bayar < total) {
      toast('Uang bayar kurang dari total!'); return;
    }
  }

  // Isi summary modal
  var total = 0;
  var html = '<div style="max-height:180px;overflow-y:auto">';
  keys.forEach(function(key) {
    var item = cart[key];
    total += item.subtotal;
    html += '<div class="order-item-row">'
      + '<span>' + item.nama_produk + ' <small class="text-muted">x' + item.qty + '</small></span>'
      + '<span class="fw-bold">' + fmtPlain(item.subtotal) + '</span></div>';
  });
  html += '</div>';
  html += '<div class="order-total-row"><span>TOTAL</span><span style="color:var(--green)">' + fmt(total) + '</span></div>';
  if (activeMetode === 'Tunai') {
    var raw2 = document.getElementById('uangBayarInput').value.replace(/[^0-9]/g,'');
    var bayar2 = parseInt(raw2) || 0;
    if (bayar2 > 0) {
      html += '<div class="order-item-row" style="margin-top:4px">'
        + '<span class="text-muted">Bayar</span><span>' + fmt(bayar2) + '</span></div>';
      html += '<div class="order-item-row">'
        + '<span class="text-muted">Kembalian</span>'
        + '<span class="fw-bold" style="color:var(--green)">' + fmt(bayar2 - total) + '</span></div>';
    }
  }
  html += '<div class="order-item-row" style="margin-top:4px">'
    + '<span class="text-muted">Metode</span><span>' + activeMetode + '</span></div>';
  html += '<div class="order-item-row">'
    + '<span class="text-muted">Pelanggan</span>'
    + '<span style="font-weight:600;color:' + (activeMemberType === 'anggota' ? 'var(--accent)' : 'var(--muted)') + '">'
    + (activeMemberType === 'anggota' ? '<i class="mdi mdi-account-check"></i> Anggota' : 'Bukan Anggota')
    + '</span></div>';

  document.getElementById('modalSummary').innerHTML = html;
  document.getElementById('memberNoRow').style.display = activeMemberType === 'anggota' ? 'block' : 'none';
  var modal = new bootstrap.Modal(document.getElementById('modalCheckout'));
  modal.show();
  setTimeout(function() { document.getElementById('mCustomerName').focus(); }, 400);
}

function doCheckout() {
  var name = document.getElementById('mCustomerName').value.trim();
  if (!name) { document.getElementById('mCustomerName').focus(); return; }

  var cartArr = [];
  Object.keys(cart).forEach(function(key) { cartArr.push(cart[key]); });

  document.getElementById('cartJsonInput').value = JSON.stringify(cartArr);
  document.getElementById('fCustomerName').value = name;
  document.getElementById('fCustomerPhone').value = document.getElementById('mCustomerPhone').value;
  document.getElementById('fMetodeBayar').value = activeMetode;
  document.getElementById('fUangBayar').value = document.getElementById('uangBayarInput').value.replace(/[^0-9]/g,'');
  document.getElementById('fCatatan').value = document.getElementById('mCatatan').value;
  document.getElementById('fMemberType').value = activeMemberType;
  document.getElementById('fMemberNo').value = activeMemberType === 'anggota' ? document.getElementById('mMemberNo').value : '';
  document.getElementById('checkoutForm').submit();
}

// Enter on customer name
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('mCustomerName').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') doCheckout();
  });
});

renderCart();
</script>
</body>
</html>
