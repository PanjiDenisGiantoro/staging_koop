<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : posReceipt.php
 *      Modul     : POS - Receipt / Resit Pesanan
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : (isset($_POST['orderID']) ? $_POST['orderID'] : '');

if (!$orderID) {
    print '<script>window.location="pos.php";</script>';
    exit;
}

$coopName = dlookup("setup", "name", "setupID=" . tosql(1, "Text"));

$rsOrder = $conn->Execute("SELECT * FROM pos_order WHERE orderID=" . tosql($orderID, "Number"));
if (!$rsOrder || $rsOrder->EOF) {
    print '<script>window.location="pos.php";</script>';
    exit;
}

$orderNo       = $rsOrder->fields('orderNo');
$customerName  = $rsOrder->fields('customerName');
$customerPhone = $rsOrder->fields('customerPhone');
$catatan       = $rsOrder->fields('catatan');
$totalAmt      = $rsOrder->fields('totalAmt');
$createdDate   = $rsOrder->fields('createdDate');

$rsDetail = $conn->Execute(
    "SELECT d.*, u.nama_usaha FROM pos_order_detail d
     LEFT JOIN usaha u ON d.usahaID = u.usahaID
     WHERE d.orderID=" . tosql($orderID, "Number") . " ORDER BY d.detailID"
);
$details = array();
while ($rsDetail && !$rsDetail->EOF) {
    $details[] = array(
        'nama_produk' => $rsDetail->fields('nama_produk'),
        'nama_usaha'  => $rsDetail->fields('nama_usaha'),
        'qty'         => $rsDetail->fields('qty'),
        'harga'       => $rsDetail->fields('harga'),
        'subtotal'    => $rsDetail->fields('subtotal')
    );
    $rsDetail->MoveNext();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resit Pesanan - <?= $orderNo ?></title>
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/icons.min.css" rel="stylesheet">
<style>
  body { background: #f4f6f9; font-size: 14px; }
  .receipt-wrap { max-width: 520px; margin: 40px auto; }
  .receipt-card {
    background: #fff; border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden;
  }
  .receipt-header {
    background: #2b3a4a; color: #fff;
    padding: 24px; text-align: center;
  }
  .receipt-header .check-icon {
    background: #27ae60; width: 60px; height: 60px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px; font-size: 30px;
  }
  .receipt-header h4 { margin: 0; font-size: 20px; }
  .receipt-header .order-no { opacity: 0.8; font-size: 13px; margin-top: 4px; }
  .receipt-body { padding: 24px; }
  .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f5f5f5; }
  .info-row:last-child { border-bottom: none; }
  .item-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; }
  .item-row .item-left .name { font-weight: 600; font-size: 13px; }
  .item-row .item-left .sub { font-size: 11px; color: #888; }
  .total-row {
    display: flex; justify-content: space-between;
    background: #f0f4f8; padding: 12px 16px; border-radius: 8px;
    font-weight: 700; font-size: 16px; margin-top: 12px;
  }
  .receipt-footer { padding: 0 24px 24px; }
  .btn-back {
    width: 100%; padding: 12px; background: #2b3a4a; color: #fff;
    border: none; border-radius: 8px; font-size: 14px; font-weight: 600;
    cursor: pointer; text-decoration: none; display: block; text-align: center;
  }
  .btn-print {
    width: 100%; padding: 10px; background: #fff; color: #2b3a4a;
    border: 1px solid #2b3a4a; border-radius: 8px; font-size: 14px;
    cursor: pointer; margin-bottom: 10px;
  }
  @media print {
    body { background: #fff; }
    .receipt-wrap { max-width: 100%; margin: 0; }
    .receipt-card { box-shadow: none; border-radius: 0; }
    .no-print { display: none !important; }
  }
</style>
</head>
<body>

<div class="receipt-wrap">
  <!-- Success Banner -->
  <div class="receipt-card mb-3">
    <div class="receipt-header">
      <div class="check-icon">
        <i class="mdi mdi-check-bold"></i>
      </div>
      <h4>Pesanan Berjaya!</h4>
      <div class="order-no">No. Pesanan: <b><?= $orderNo ?></b></div>
      <div class="order-no"><?= date("d/m/Y H:i", strtotime($createdDate)) ?></div>
    </div>

    <div class="receipt-body">
      <!-- Info Pembeli -->
      <p class="fw-bold mb-2" style="font-size:13px;color:#888;text-transform:uppercase;">Info Pembeli</p>
      <div class="info-row">
        <span class="text-muted">Nama</span>
        <span class="fw-bold"><?= $customerName ?></span>
      </div>
      <?php if ($customerPhone): ?>
      <div class="info-row">
        <span class="text-muted">Telepon</span>
        <span><?= $customerPhone ?></span>
      </div>
      <?php endif; ?>
      <?php if ($catatan): ?>
      <div class="info-row">
        <span class="text-muted">Catatan</span>
        <span><?= $catatan ?></span>
      </div>
      <?php endif; ?>

      <!-- Daftar Item -->
      <p class="fw-bold mb-2 mt-3" style="font-size:13px;color:#888;text-transform:uppercase;">Pesanan</p>
      <?php foreach ($details as $d): ?>
      <div class="item-row">
        <div class="item-left">
          <div class="name"><?= $d['nama_produk'] ?></div>
          <div class="sub">
            <i class="mdi mdi-store"></i> <?= $d['nama_usaha'] ?>
            &nbsp;&bull;&nbsp; <?= number_format($d['qty'], 0) ?> x Rp <?= number_format($d['harga'], 0, ',', '.') ?>
          </div>
        </div>
        <div class="fw-bold">Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></div>
      </div>
      <?php endforeach; ?>

      <div class="total-row">
        <span>TOTAL</span>
        <span>Rp <?= number_format($totalAmt, 0, ',', '.') ?></span>
      </div>
    </div>

    <div class="receipt-footer">
      <button onclick="window.print()" class="btn-print no-print">
        <i class="mdi mdi-printer"></i> Cetak Resit
      </button>
      <a href="pos.php" class="btn-back no-print">
        <i class="mdi mdi-arrow-left"></i> Kembali Beli
      </a>
      <div class="text-center text-muted mt-3" style="font-size:11px;">
        Terima kasih! &mdash; <?= $coopName ?>
      </div>
    </div>
  </div>
</div>

<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
