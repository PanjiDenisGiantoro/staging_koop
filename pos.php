<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : pos.php
 *      Modul     : Point of Sale - Serba Usaha (Standalone)
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

// Ekstrak variabel POST/GET secara eksplisit (tidak bergantung pada register_globals)
$action        = isset($_POST['action'])        ? $_POST['action']        : (isset($_GET['action'])        ? $_GET['action']        : '');
$cartJson      = isset($_POST['cartJson'])      ? $_POST['cartJson']      : '';
$customerName  = isset($_POST['customerName'])  ? trim($_POST['customerName'])  : '';
$customerPhone = isset($_POST['customerPhone']) ? trim($_POST['customerPhone']) : '';
$catatan       = isset($_POST['catatan'])       ? trim($_POST['catatan'])       : '';
$usahaFilter   = isset($_GET['usahaFilter'])    ? $_GET['usahaFilter']    : (isset($_POST['usahaFilter'])  ? $_POST['usahaFilter']  : '');
$q             = isset($_GET['q'])              ? $_GET['q']              : (isset($_POST['q'])            ? $_POST['q']            : '');

$coopName = dlookup("setup", "name", "setupID=" . tosql(1, "Text"));

// Strip magic_quotes jika aktif (PHP 5.2 XAMPP)
if (get_magic_quotes_gpc()) {
    $cartJson      = stripslashes($cartJson);
    $customerName  = stripslashes($customerName);
    $customerPhone = stripslashes($customerPhone);
    $catatan       = stripslashes($catatan);
}

// --- Proses Checkout ---
if ($action == 'checkout') {
    $now  = date("Y-m-d H:i:s");
    $by   = $customerName ? $customerName : 'guest';

    // Generate order number
    $rsNo   = $conn->Execute("SELECT MAX(CAST(RIGHT(orderNo,6) AS SIGNED INTEGER)) AS n FROM pos_order");
    $nomor  = $rsNo ? intval($rsNo->fields('n')) + 1 : 1;
    $orderNo = 'POS' . sprintf("%06d", $nomor);

    // Decode cart JSON
    $cartItems = json_decode($cartJson, true);

    if (!$cartItems || count($cartItems) == 0) {
        $errCheckout = "Keranjang kosong.";
    } elseif (!$customerName) {
        $errCheckout = "Nama pembeli wajib diisi.";
    } else {
        $totalAmt = 0;
        foreach ($cartItems as $item) {
            $totalAmt += floatval($item['subtotal']);
        }

        // Insert order header
        $sSQL = "INSERT INTO pos_order (orderNo, customerName, customerPhone, catatan, totalAmt, status, createdDate, createdBy)
                 VALUES ("
               . tosql($orderNo, "Text") . ","
               . tosql($customerName, "Text") . ","
               . tosql($customerPhone, "Text") . ","
               . tosql($catatan, "Text") . ","
               . tosql($totalAmt, "Number") . ","
               . "0,'{$now}',"
               . tosql($by, "Text") . ")";
        $conn->Execute($sSQL);
        $orderID = $conn->Insert_ID();

        // Insert order details
        foreach ($cartItems as $item) {
            $sSQL2 = "INSERT INTO pos_order_detail (orderID, produkID, usahaID, nama_produk, qty, harga, subtotal)
                      VALUES ("
                    . tosql($orderID, "Number") . ","
                    . tosql($item['produkID'], "Number") . ","
                    . tosql($item['usahaID'], "Number") . ","
                    . tosql($item['nama_produk'], "Text") . ","
                    . tosql($item['qty'], "Number") . ","
                    . tosql($item['harga'], "Number") . ","
                    . tosql($item['subtotal'], "Number") . ")";
            $conn->Execute($sSQL2);

            // Kurangkan stok
            $rsSaldo = $conn->Execute(
                "SELECT COALESCE(SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END),0) as saldo
                 FROM stok_usaha WHERE produkID=" . tosql($item['produkID'], "Number")
            );
            $stokBaru = $rsSaldo ? floatval($rsSaldo->fields('saldo')) - floatval($item['qty']) : 0;
            $conn->Execute(
                "INSERT INTO stok_usaha (produkID, usahaID, jenis, qty, stok_akhir, harga, keterangan, tarikh, createdDate, createdBy)
                 VALUES ("
                . tosql($item['produkID'], "Number") . ","
                . tosql($item['usahaID'], "Number") . ","
                . "'keluar',"
                . tosql($item['qty'], "Number") . ","
                . tosql($stokBaru, "Number") . ","
                . tosql($item['harga'], "Number") . ","
                . "'Penjualan POS #" . $orderNo . "',"
                . "'" . date("Y-m-d") . "','{$now}',"
                . tosql($by, "Text") . ")"
            );
        }

        // Redirect ke receipt
        print '<script>window.location="posReceipt.php?orderID=' . $orderID . '";</script>';
        exit;
    }
}

// --- Load semua Usaha aktif ---
$rsUsaha = $conn->Execute("SELECT * FROM usaha WHERE status=1 ORDER BY nama_usaha");
$usahaList = array();
while ($rsUsaha && !$rsUsaha->EOF) {
    $usahaList[] = array(
        'usahaID'    => $rsUsaha->fields('usahaID'),
        'nama_usaha' => $rsUsaha->fields('nama_usaha'),
        'kategori'   => $rsUsaha->fields('kategori')
    );
    $rsUsaha->MoveNext();
}

// --- Load Produk ---
$sWhere = "WHERE p.status=1 AND u.status=1";
if ($usahaFilter) $sWhere .= " AND p.usahaID=" . tosql($usahaFilter, "Number");
if ($q)           $sWhere .= " AND p.nama_produk LIKE '%" . $q . "%'";

$sSQL = "SELECT p.produkID, p.usahaID, p.nama_produk, p.harga_jual, p.kategori, p.deskripsi,
                u.nama_usaha,
                COALESCE((SELECT SUM(CASE WHEN jenis='masuk' THEN qty ELSE -qty END) FROM stok_usaha WHERE produkID=p.produkID),0) as stok
         FROM produk_usaha p
         JOIN usaha u ON p.usahaID = u.usahaID
         " . $sWhere . "
         ORDER BY u.nama_usaha, p.nama_produk";
$rsProduk = $conn->Execute($sSQL);

$products = array();
while ($rsProduk && !$rsProduk->EOF) {
    $products[] = array(
        'produkID'   => $rsProduk->fields('produkID'),
        'usahaID'    => $rsProduk->fields('usahaID'),
        'nama_produk'=> $rsProduk->fields('nama_produk'),
        'harga_jual' => floatval($rsProduk->fields('harga_jual')),
        'kategori'   => $rsProduk->fields('kategori'),
        'deskripsi'  => $rsProduk->fields('deskripsi'),
        'nama_usaha' => $rsProduk->fields('nama_usaha'),
        'stok'       => floatval($rsProduk->fields('stok'))
    );
    $rsProduk->MoveNext();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS - <?= $coopName ?></title>
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/icons.min.css" rel="stylesheet">
<link rel="shortcut icon" href="assets/images/favicon.png">
<style>
  body { background: #f4f6f9; font-size: 14px; }

  /* Navbar */
  .pos-navbar {
    background: #2b3a4a;
    padding: 10px 20px;
    position: sticky; top: 0; z-index: 100;
    display: flex; align-items: center; justify-content: space-between;
  }
  .pos-navbar .brand { color: #fff; font-size: 18px; font-weight: 700; }
  .pos-navbar .search-box {
    flex: 1; max-width: 420px; margin: 0 20px;
  }
  .pos-navbar .search-box input {
    border-radius: 20px; padding-left: 16px; font-size: 13px;
  }
  .cart-btn {
    background: #f0ad4e; border: none; border-radius: 20px;
    padding: 6px 16px; font-weight: 600; color: #fff; cursor: pointer;
    position: relative;
  }
  .cart-btn .badge {
    position: absolute; top: -6px; right: -8px;
    background: #e74c3c; color: #fff; border-radius: 50%;
    width: 20px; height: 20px; font-size: 11px;
    display: flex; align-items: center; justify-content: center;
  }

  /* Filter Usaha */
  .usaha-filter { background: #fff; padding: 10px 20px; border-bottom: 1px solid #e0e0e0; }
  .usaha-filter .btn-usaha {
    margin: 3px; border-radius: 20px; font-size: 12px;
    padding: 4px 14px; border: 1px solid #2b3a4a; color: #2b3a4a;
    background: #fff; cursor: pointer;
  }
  .usaha-filter .btn-usaha.active {
    background: #2b3a4a; color: #fff;
  }

  /* Product Grid */
  .product-grid { padding: 16px 20px; }
  .product-card {
    background: #fff; border-radius: 10px; overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08); transition: transform .15s;
    cursor: pointer; height: 100%;
  }
  .product-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.13); }
  .product-card .prod-img {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 120px; display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.7); font-size: 36px;
  }
  .product-card .prod-body { padding: 10px 12px; }
  .product-card .prod-name { font-weight: 600; font-size: 13px; margin-bottom: 2px; }
  .product-card .prod-usaha { font-size: 11px; color: #888; margin-bottom: 4px; }
  .product-card .prod-price { font-size: 15px; font-weight: 700; color: #2b3a4a; }
  .product-card .prod-stok { font-size: 11px; margin-top: 4px; }
  .product-card .btn-add {
    width: 100%; margin-top: 8px; border-radius: 6px;
    background: #2b3a4a; color: #fff; border: none;
    padding: 5px; font-size: 12px; font-weight: 600;
  }
  .product-card .btn-add:disabled { background: #ccc; cursor: not-allowed; }
  .out-of-stock { opacity: 0.6; }

  /* Cart Sidebar */
  .cart-sidebar {
    position: fixed; right: -380px; top: 0; width: 360px; height: 100vh;
    background: #fff; box-shadow: -4px 0 20px rgba(0,0,0,0.15);
    z-index: 200; transition: right .3s ease;
    display: flex; flex-direction: column;
  }
  .cart-sidebar.open { right: 0; }
  .cart-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.4); z-index: 199;
  }
  .cart-overlay.open { display: block; }
  .cart-header {
    background: #2b3a4a; color: #fff; padding: 16px 20px;
    display: flex; align-items: center; justify-content: space-between;
    font-weight: 700; font-size: 16px;
  }
  .cart-body { flex: 1; overflow-y: auto; padding: 12px; }
  .cart-item {
    display: flex; align-items: flex-start; padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
  }
  .cart-item .item-info { flex: 1; padding-right: 8px; }
  .cart-item .item-name { font-weight: 600; font-size: 13px; }
  .cart-item .item-usaha { font-size: 11px; color: #888; }
  .cart-item .item-price { font-size: 12px; color: #555; margin-top: 2px; }
  .cart-item .qty-control {
    display: flex; align-items: center; gap: 6px; margin-top: 6px;
  }
  .qty-control button {
    width: 26px; height: 26px; border-radius: 50%; border: 1px solid #ccc;
    background: #f5f5f5; font-size: 16px; line-height: 1;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
  }
  .qty-control span { font-weight: 700; min-width: 24px; text-align: center; }
  .cart-item .item-subtotal { font-weight: 700; font-size: 13px; color: #2b3a4a; white-space: nowrap; }
  .cart-item .btn-remove { background: none; border: none; color: #e74c3c; font-size: 16px; cursor: pointer; padding: 0; }
  .cart-footer { padding: 16px; border-top: 1px solid #eee; }
  .cart-total { display: flex; justify-content: space-between; font-size: 16px; font-weight: 700; margin-bottom: 12px; }
  .btn-checkout {
    width: 100%; padding: 12px; background: #27ae60; color: #fff;
    border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer;
  }
  .btn-checkout:hover { background: #219150; }
  .cart-empty { text-align: center; color: #aaa; padding: 40px 20px; }

  /* Checkout Modal */
  .modal-header { background: #2b3a4a; color: #fff; }
  .modal-header .btn-close { filter: invert(1); }

  @media (max-width: 768px) {
    .cart-sidebar { width: 100%; right: -100%; }
    .product-grid { padding: 10px; }
  }
</style>
</head>
<body>

<!-- Navbar -->
<div class="pos-navbar">
  <div class="brand">
    <i class="mdi mdi-store"></i> <?= $coopName ? $coopName : 'Serba Usaha' ?>
  </div>
  <form method="get" action="pos.php" class="search-box d-flex" style="flex:1;max-width:420px;margin:0 20px;">
    <input type="hidden" name="usahaFilter" value="<?= $usahaFilter ?>">
    <input type="text" name="q" value="<?= $q ?>" placeholder="Cari produk..." class="form-control">
    <button type="submit" class="btn btn-secondary ms-1" style="border-radius:20px;padding:0 14px;">
      <i class="mdi mdi-magnify"></i>
    </button>
  </form>
  <button class="cart-btn" onclick="openCart()" id="cartBtn">
    <i class="mdi mdi-cart"></i> Keranjang
    <span class="badge" id="cartCount">0</span>
  </button>
</div>

<!-- Filter Usaha -->
<div class="usaha-filter">
  <a href="pos.php?q=<?= urlencode($q) ?>"
     class="btn-usaha <?= $usahaFilter == '' ? 'active' : '' ?>">
     Semua Usaha
  </a>
  <?php foreach ($usahaList as $u): ?>
  <a href="pos.php?usahaFilter=<?= $u['usahaID'] ?>&q=<?= urlencode($q) ?>"
     class="btn-usaha <?= $usahaFilter == $u['usahaID'] ? 'active' : '' ?>">
     <?= $u['nama_usaha'] ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Product Grid -->
<div class="product-grid">
  <?php if (count($products) == 0): ?>
  <div class="text-center py-5 text-muted">
    <i class="mdi mdi-package-variant-closed" style="font-size:48px;"></i>
    <p class="mt-2">Tidak ada produk ditemukan.</p>
  </div>
  <?php else: ?>
  <div class="row g-3">
    <?php foreach ($products as $p):
      $habis    = $p['stok'] <= 0;
      $icon     = array('Makanan'=>'mdi-food','Minuman'=>'mdi-cup','Pakaian'=>'mdi-tshirt-crew',
                        'Elektronik'=>'mdi-laptop','Pertanian'=>'mdi-sprout',
                        'Perkhidmatan'=>'mdi-tools','Kraftangan'=>'mdi-hand-extended');
      $icoClass = isset($icon[$p['kategori']]) ? $icon[$p['kategori']] : 'mdi-package-variant';
    ?>
    <div class="col-6 col-md-3 col-lg-2">
      <div class="product-card <?= $habis ? 'out-of-stock' : '' ?>">
        <div class="prod-img">
          <i class="mdi <?= $icoClass ?>" style="font-size:40px;color:rgba(255,255,255,0.85)"></i>
        </div>
        <div class="prod-body">
          <div class="prod-name"><?= $p['nama_produk'] ?></div>
          <div class="prod-usaha"><i class="mdi mdi-store"></i> <?= $p['nama_usaha'] ?></div>
          <div class="prod-price">Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></div>
          <div class="prod-stok <?= $habis ? 'text-danger' : 'text-success' ?>">
            <i class="mdi <?= $habis ? 'mdi-close-circle' : 'mdi-check-circle' ?>"></i>
            <?= $habis ? 'Habis' : 'Stok: ' . number_format($p['stok'], 0) ?>
          </div>
          <button class="btn-add"
                  <?= $habis ? 'disabled' : '' ?>
                  onclick="addToCart(<?= $p['produkID'] ?>, <?= $p['usahaID'] ?>, '<?= addslashes($p['nama_produk']) ?>', <?= $p['harga_jual'] ?>, '<?= addslashes($p['nama_usaha']) ?>', <?= $p['stok'] ?>)">
            <i class="mdi mdi-cart-plus"></i> Tambah
          </button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Cart Overlay -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>

<!-- Cart Sidebar -->
<div class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <span><i class="mdi mdi-cart"></i> Keranjang Belanja</span>
    <button onclick="closeCart()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;">&times;</button>
  </div>
  <div class="cart-body" id="cartBody">
    <div class="cart-empty" id="cartEmpty">
      <i class="mdi mdi-cart-off" style="font-size:48px;"></i>
      <p>Keranjang Anda kosong</p>
    </div>
    <div id="cartItems"></div>
  </div>
  <div class="cart-footer">
    <div class="cart-total">
      <span>Jumlah:</span>
      <span id="cartTotal">Rp 0</span>
    </div>
    <button class="btn-checkout" id="btnCheckout" onclick="openCheckout()" disabled>
      <i class="mdi mdi-cash-register"></i> Bayar Sekarang
    </button>
  </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="mdi mdi-cash-register"></i> Checkout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="pos.php" id="checkoutForm">
        <input type="hidden" name="action" value="checkout">
        <input type="hidden" name="cartJson" id="cartJsonInput">
        <div class="modal-body">
          <div id="orderSummary" class="mb-3 p-3 bg-light rounded"></div>
          <div class="mb-3">
            <label class="form-label fw-bold">* Nama Pembeli</label>
            <input type="text" name="customerName" class="form-control" placeholder="Nama anda" required>
          </div>
          <div class="mb-3">
            <label class="form-label">No. Telepon / HP</label>
            <input type="text" name="customerPhone" class="form-control" placeholder="cth: 081234567890">
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan pesanan..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success fw-bold">
            <i class="mdi mdi-check-circle"></i> Konfirmasi Pesanan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
var cart = {};

function formatRp(n) {
  return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function addToCart(produkID, usahaID, nama, harga, namaUsaha, stok) {
  var key = 'p' + produkID;
  if (cart[key]) {
    if (cart[key].qty >= stok) {
      alert('Stok tidak cukup! Maksimum: ' + stok);
      return;
    }
    cart[key].qty++;
    cart[key].subtotal = cart[key].qty * harga;
  } else {
    cart[key] = {
      produkID: produkID, usahaID: usahaID,
      nama_produk: nama, harga: harga,
      nama_usaha: namaUsaha, stok: stok,
      qty: 1, subtotal: harga
    };
  }
  renderCart();
  openCart();
}

function removeFromCart(key) {
  delete cart[key];
  renderCart();
}

function changeQty(key, delta) {
  if (!cart[key]) return;
  cart[key].qty += delta;
  if (cart[key].qty <= 0) {
    delete cart[key];
  } else {
    if (cart[key].qty > cart[key].stok) {
      cart[key].qty = cart[key].stok;
      alert('Stok tidak cukup!');
    }
    cart[key].subtotal = cart[key].qty * cart[key].harga;
  }
  renderCart();
}

function renderCart() {
  var keys = Object.keys(cart);
  var count = 0, total = 0;
  var html = '';

  keys.forEach(function(key) {
    var item = cart[key];
    count += item.qty;
    total += item.subtotal;
    html += '<div class="cart-item">'
          + '<div class="item-info">'
          + '<div class="item-name">' + item.nama_produk + '</div>'
          + '<div class="item-usaha">' + item.nama_usaha + '</div>'
          + '<div class="item-price">' + formatRp(item.harga) + ' / unit</div>'
          + '<div class="qty-control">'
          + '<button onclick="changeQty(\'' + key + '\',-1)">&#8722;</button>'
          + '<span>' + item.qty + '</span>'
          + '<button onclick="changeQty(\'' + key + '\',1)">&#43;</button>'
          + '</div>'
          + '</div>'
          + '<div style="text-align:right">'
          + '<div class="item-subtotal">' + formatRp(item.subtotal) + '</div>'
          + '<button class="btn-remove" onclick="removeFromCart(\'' + key + '\')" title="Hapus">'
          + '<i class="mdi mdi-delete"></i></button>'
          + '</div>'
          + '</div>';
  });

  document.getElementById('cartItems').innerHTML = html;
  document.getElementById('cartEmpty').style.display = keys.length == 0 ? 'block' : 'none';
  document.getElementById('cartCount').textContent = count;
  document.getElementById('cartTotal').textContent = formatRp(total);
  document.getElementById('btnCheckout').disabled = keys.length == 0;
}

function openCart() {
  document.getElementById('cartSidebar').classList.add('open');
  document.getElementById('cartOverlay').classList.add('open');
}
function closeCart() {
  document.getElementById('cartSidebar').classList.remove('open');
  document.getElementById('cartOverlay').classList.remove('open');
}

function openCheckout() {
  var keys = Object.keys(cart);
  if (keys.length == 0) return;
  var total = 0;
  var summary = '<strong>Ringkasan Pesanan:</strong><table class="table table-sm mt-2 mb-0">';
  keys.forEach(function(key) {
    var item = cart[key];
    total += item.subtotal;
    summary += '<tr><td>' + item.nama_produk + '</td>'
             + '<td class="text-end">' + item.qty + ' x ' + formatRp(item.harga) + '</td>'
             + '<td class="text-end fw-bold">' + formatRp(item.subtotal) + '</td></tr>';
  });
  summary += '<tr class="table-secondary"><td colspan="2" class="fw-bold">TOTAL</td>'
           + '<td class="text-end fw-bold">' + formatRp(total) + '</td></tr></table>';

  document.getElementById('orderSummary').innerHTML = summary;

  // Serialize cart to JSON for form submit
  var cartArr = [];
  keys.forEach(function(key) { cartArr.push(cart[key]); });
  document.getElementById('cartJsonInput').value = JSON.stringify(cartArr);

  var modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
  modal.show();
  closeCart();
}

// Init
renderCart();
</script>
</body>
</html>
