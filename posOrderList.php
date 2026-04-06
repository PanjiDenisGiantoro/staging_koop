<?php
/*********************************************************************************
 *      Project   : iKOOP.com.my
 *      Filename  : posOrderList.php
 *      Modul     : POS - Daftar & Manajemen Order (Admin)
 *********************************************************************************/
if (!isset($StartRec)) $StartRec = 1;
if (!isset($pg))       $pg       = 30;
if (!isset($q))        $q        = '';
if (!isset($filterStatus)) $filterStatus = '';
if (!isset($filterUsaha))  $filterUsaha  = '';

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

$isAdmin    = (get_session("Cookie_groupID") == '1' || get_session("Cookie_groupID") == '2' || get_session("Cookie_groupID") == '3');
$myMemberID = dlookup("userdetails", "memberID", "userID=" . tosql(get_session("Cookie_userID"), "Number"));

$sFileName = "?vw=posOrderList&mn=$mn";

// --- Update Status ---
if ($action == 'updateStatus' && $orderID && $newStatus !== '') {
    $by  = get_session("Cookie_userName");
    $now = date("Y-m-d H:i:s");
    $conn->Execute("UPDATE pos_order SET status=" . tosql($newStatus, "Number") . " WHERE orderID=" . tosql($orderID, "Number"));
    activityLog('', "Perbarui Status Order #$orderID ke status $newStatus", get_session('Cookie_userID'), $by, 3);
    print '<script>window.location="' . $sFileName . '&filterStatus=' . $filterStatus . '&filterUsaha=' . $filterUsaha . '";</script>';
    exit;
}

// --- Load usaha list untuk filter ---
$rsUsaha = $conn->Execute("SELECT usahaID, nama_usaha FROM usaha WHERE status=1 ORDER BY nama_usaha");
$usahaOpts = array();
while ($rsUsaha && !$rsUsaha->EOF) {
    $usahaOpts[$rsUsaha->fields('usahaID')] = $rsUsaha->fields('nama_usaha');
    $rsUsaha->MoveNext();
}

// --- Query orders ---
$sWhere = " WHERE 1=1";
if ($q) $sWhere .= " AND (o.orderNo LIKE '%" . $q . "%' OR o.customerName LIKE '%" . $q . "%')";
if ($filterStatus !== '') $sWhere .= " AND o.status=" . tosql($filterStatus, "Number");
if ($filterUsaha)  $sWhere .= " AND EXISTS (SELECT 1 FROM pos_order_detail d WHERE d.orderID=o.orderID AND d.usahaID=" . tosql($filterUsaha, "Number") . ")";

// Anggota biasa: hanya lihat order usaha sendiri
if (!$isAdmin) {
    $myUsahaIDs = array();
    $rsMyUsaha = $conn->Execute("SELECT usahaID FROM usaha WHERE memberID=" . tosql($myMemberID, "Text"));
    while ($rsMyUsaha && !$rsMyUsaha->EOF) {
        $myUsahaIDs[] = $rsMyUsaha->fields('usahaID');
        $rsMyUsaha->MoveNext();
    }
    if (count($myUsahaIDs) == 0) {
        $myUsahaIDs[] = 0;
    }
    $sWhere .= " AND EXISTS (SELECT 1 FROM pos_order_detail d WHERE d.orderID=o.orderID AND d.usahaID IN (" . implode(',', $myUsahaIDs) . "))";
}

$sSQL    = "SELECT o.* FROM pos_order o" . $sWhere . " ORDER BY o.createdDate DESC";
$GetList = $conn->Execute($sSQL);
$GetList->Move($StartRec - 1);
$TotalRec  = $GetList->RowCount();

$statusLabel = array(
    '0' => '<span class="badge bg-warning text-dark">Pending</span>',
    '1' => '<span class="badge bg-primary">Confirmed</span>',
    '2' => '<span class="badge bg-success">Selesai</span>',
    '3' => '<span class="badge bg-danger">Batal</span>'
);
$statusText = array('0' => 'Pending', '1' => 'Confirmed', '2' => 'Selesai', '3' => 'Batal');
?>

<div class="maroon" align="left"><b>POS - SENARAI ORDER</b></div>
<div>&nbsp;</div>

<h5 class="card-title">SENARAI PESANAN (POS)</h5>

<form name="MyForm" action="<?= $sFileName ?>" method="post">
<input type="hidden" name="action">
<input type="hidden" name="orderID" id="orderID">
<input type="hidden" name="newStatus" id="newStatus">

<div class="row mb-2">
  <div class="col-md-4">
    Cari:
    <input type="text" name="q" value="<?= $q ?>" size="25" class="form-control-sm d-inline" placeholder="No. Order / Nama">
    <input type="submit" class="btn btn-sm btn-secondary" value="Cari">
  </div>
  <div class="col-md-3">
    Status:
    <select name="filterStatus" class="form-select-sm d-inline w-auto" onchange="document.MyForm.submit()">
      <option value="">- Semua -</option>
      <option value="0" <?= $filterStatus==='0'?'selected':'' ?>>Pending</option>
      <option value="1" <?= $filterStatus==='1'?'selected':'' ?>>Confirmed</option>
      <option value="2" <?= $filterStatus==='2'?'selected':'' ?>>Selesai</option>
      <option value="3" <?= $filterStatus==='3'?'selected':'' ?>>Batal</option>
    </select>
  </div>
  <?php if ($isAdmin && count($usahaOpts) > 0): ?>
  <div class="col-md-3">
    Usaha:
    <select name="filterUsaha" class="form-select-sm d-inline w-auto" onchange="document.MyForm.submit()">
      <option value="">- Semua -</option>
      <?php foreach ($usahaOpts as $uid => $uname): ?>
      <option value="<?= $uid ?>" <?= $filterUsaha==$uid?'selected':'' ?>><?= $uname ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php endif; ?>
  <div class="col-md-2 text-end">
    <a href="pos.php" target="_blank" class="btn btn-sm btn-success">
      <i class="mdi mdi-cart"></i> Buka POS
    </a>
  </div>
</div>

<div class="table-responsive">
<table class="table table-sm table-striped table-bordered">
  <thead class="table-primary">
    <tr>
      <th width="5%">No</th>
      <th>No. Order</th>
      <th>Nama Pembeli</th>
      <th>Telepon</th>
      <th class="text-end">Total (Rp)</th>
      <th>Status</th>
      <th>Tanggal</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $i = $StartRec;
  $found = false;
  while ($GetList && !$GetList->EOF && $i < ($StartRec + $pg)):
    $found      = true;
    $oid        = $GetList->fields('orderID');
    $oNo        = $GetList->fields('orderNo');
    $cName      = $GetList->fields('customerName');
    $cPhone     = $GetList->fields('customerPhone');
    $total      = $GetList->fields('totalAmt');
    $oStatus    = $GetList->fields('status');
    $oDate      = toDate("d/m/Y H:i", $GetList->fields('createdDate'));
  ?>
  <tr>
    <td><?= $i ?></td>
    <td><b><?= $oNo ?></b></td>
    <td><?= $cName ?></td>
    <td><?= $cPhone ?></td>
    <td class="text-end"><b>Rp <?= number_format($total, 0, ',', '.') ?></b></td>
    <td><?= isset($statusLabel[$oStatus]) ? $statusLabel[$oStatus] : '-' ?></td>
    <td><?= $oDate ?></td>
    <td nowrap>
      <a href="#" class="btn btn-sm btn-info" onclick="showDetail(<?= $oid ?>); return false;">Detail</a>
      <?php if ($oStatus == 0): ?>
        <button class="btn btn-sm btn-primary" onclick="ubahStatus(<?= $oid ?>, 1)">Confirm</button>
        <button class="btn btn-sm btn-danger" onclick="ubahStatus(<?= $oid ?>, 3)">Batal</button>
      <?php elseif ($oStatus == 1): ?>
        <button class="btn btn-sm btn-success" onclick="ubahStatus(<?= $oid ?>, 2)">Selesai</button>
      <?php endif; ?>
    </td>
  </tr>
  <?php
    $i++;
    $GetList->MoveNext();
  endwhile;
  if (!$found): ?>
  <tr><td colspan="8" class="text-center text-muted">Tidak ada data pesanan.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
</div>

<!-- Pagination -->
<?php
$EndRec = min($StartRec + $pg - 1, $TotalRec);
echo "<div class='row mt-1'>";
echo "<div class='col-md-6'>Jumlah: <b>$TotalRec</b> | Paparan: <b>$StartRec - $EndRec</b></div>";
echo "<div class='col-md-6 text-end'>";
if ($StartRec > 1) {
    $PrevRec = max(1, $StartRec - $pg);
    echo "<a href='$sFileName&StartRec=$PrevRec&pg=$pg&q=$q&filterStatus=$filterStatus' class='btn btn-sm btn-outline-secondary'>&laquo; Prev</a> ";
}
if ($EndRec < $TotalRec) {
    $NextRec = $StartRec + $pg;
    echo "<a href='$sFileName&StartRec=$NextRec&pg=$pg&q=$q&filterStatus=$filterStatus' class='btn btn-sm btn-outline-secondary'>Next &raquo;</a>";
}
echo "</div></div>";
?>
</form>

<!-- Modal Detail Order -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:#2b3a4a;color:#fff;">
        <h5 class="modal-title"><i class="mdi mdi-receipt"></i> Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button>
      </div>
      <div class="modal-body" id="detailContent">
        <div class="text-center py-4"><i class="mdi mdi-loading mdi-spin" style="font-size:32px;"></i> Memuatkan...</div>
      </div>
    </div>
  </div>
</div>

<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
function ubahStatus(orderID, newStatus) {
  var labels = {1:'Confirm',2:'Selesai',3:'Batal'};
  if (!confirm('Tukar status order ke: ' + labels[newStatus] + '?')) return;
  document.getElementById('orderID').value = orderID;
  document.getElementById('newStatus').value = newStatus;
  document.MyForm.action.value = 'updateStatus';
  document.MyForm.submit();
}

function showDetail(orderID) {
  var modal = new bootstrap.Modal(document.getElementById('detailModal'));
  modal.show();
  document.getElementById('detailContent').innerHTML =
    '<div class="text-center py-4"><i class="mdi mdi-loading mdi-spin" style="font-size:32px;"></i></div>';

  $.get('posOrderDetail.php', {orderID: orderID}, function(html) {
    document.getElementById('detailContent').innerHTML = html;
  });
}
</script>
