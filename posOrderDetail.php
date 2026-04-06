<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : posOrderDetail.php
 *      Modul     : POS - Detail Order (AJAX fragment)
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");

$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : (isset($_POST['orderID']) ? $_POST['orderID'] : '');

if (!$orderID) { print 'Order tidak ditemukan.'; exit; }

$rsOrder = $conn->Execute("SELECT * FROM pos_order WHERE orderID=" . tosql($orderID, "Number"));
if (!$rsOrder || $rsOrder->EOF) { print 'Order tidak ditemukan.'; exit; }

$orderNo       = $rsOrder->fields('orderNo');
$customerName  = $rsOrder->fields('customerName');
$customerPhone = $rsOrder->fields('customerPhone');
$catatan       = $rsOrder->fields('catatan');
$totalAmt      = $rsOrder->fields('totalAmt');
$status        = $rsOrder->fields('status');
$createdDate   = toDate("d/m/Y H:i", $rsOrder->fields('createdDate'));

$statusLabel = array('0'=>'Pending','1'=>'Confirmed','2'=>'Selesai','3'=>'Batal');
$statusColor = array('0'=>'warning','1'=>'primary','2'=>'success','3'=>'danger');

$rsDetail = $conn->Execute(
    "SELECT d.*, u.nama_usaha FROM pos_order_detail d
     LEFT JOIN usaha u ON d.usahaID = u.usahaID
     WHERE d.orderID=" . tosql($orderID, "Number") . " ORDER BY d.usahaID, d.nama_produk"
);
?>
<table class="table table-sm mb-3">
  <tr>
    <td width="140"><b>No. Order</b></td>
    <td><b><?= $orderNo ?></b></td>
    <td width="140"><b>Tanggal</b></td>
    <td><?= $createdDate ?></td>
  </tr>
  <tr>
    <td><b>Nama Pembeli</b></td>
    <td><?= $customerName ?></td>
    <td><b>Status</b></td>
    <td><span class="badge bg-<?= isset($statusColor[$status]) ? $statusColor[$status] : 'secondary' ?>">
      <?= isset($statusLabel[$status]) ? $statusLabel[$status] : '-' ?>
    </span></td>
  </tr>
  <?php if ($customerPhone): ?>
  <tr>
    <td><b>Telepon</b></td>
    <td><?= $customerPhone ?></td>
    <td></td><td></td>
  </tr>
  <?php endif; ?>
  <?php if ($catatan): ?>
  <tr>
    <td><b>Catatan</b></td>
    <td colspan="3"><?= $catatan ?></td>
  </tr>
  <?php endif; ?>
</table>

<hr>
<b>Item Pesanan:</b>
<table class="table table-sm table-bordered mt-2">
  <thead class="table-primary">
    <tr>
      <th>Produk</th>
      <th>Usaha</th>
      <th class="text-end">Qty</th>
      <th class="text-end">Harga (Rp)</th>
      <th class="text-end">Subtotal (Rp)</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($rsDetail && !$rsDetail->EOF): ?>
  <tr>
    <td><?= $rsDetail->fields('nama_produk') ?></td>
    <td><?= $rsDetail->fields('nama_usaha') ?></td>
    <td class="text-end"><?= number_format($rsDetail->fields('qty'), 0) ?></td>
    <td class="text-end">Rp <?= number_format($rsDetail->fields('harga'), 0, ',', '.') ?></td>
    <td class="text-end"><b>Rp <?= number_format($rsDetail->fields('subtotal'), 0, ',', '.') ?></b></td>
  </tr>
  <?php $rsDetail->MoveNext(); endwhile; ?>
  <tr class="table-secondary">
    <td colspan="4" class="text-end fw-bold">TOTAL</td>
    <td class="text-end fw-bold">Rp <?= number_format($totalAmt, 0, ',', '.') ?></td>
  </tr>
  </tbody>
</table>
