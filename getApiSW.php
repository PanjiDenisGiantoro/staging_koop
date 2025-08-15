<?php 
ob_start();
//////////////////////////
include("header.php");
include("koperasiQry.php");
//////////////////////////
$sFileName = "index.php";
//////////////////////////
date_default_timezone_set("Asia/Jakarta"); 
$date = date("Y-m-d H:i:s"); 
$today = date("Y-m-d"); 
if (!isset($mm))  $mm=date("m");
if (!isset($yy))  $yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
//////////////////////////

$raw = file_get_contents('php://input');

$data = json_decode($raw, true);
if (!$data) {
    http_response_code(400);
    exit("Invalid JSON");
}

$secretKey = 'YOUR_SECRET_KEY'; // Replace with actual secret from Swittle
$checksumFromSwittle = $data['Checksum'] ?? '';
$copyData = $data;
unset($copyData['Checksum']);

// JSON encode again, or build string exactly as Swittle expects (check their docs!)
$payloadForChecksum = json_encode($copyData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// Compute HMAC-SHA256 using your secret key
$calculatedChecksum = hash_hmac('sha256', $payloadForChecksum, $secretKey);

if ($checksumFromSwittle !== $calculatedChecksum) {
    http_response_code(403);
    exit("Checksum mismatch");
}

$billId         = $data['BillId'];
$status         = $data['TransactionExecutionStatusTag'] ?? '';
$paymentAmount  = $data['PaymentAmount'] ?? 0;
$cajamaun       = 1;
$amauntot       = ($paymentAmount - $cajamaun);

if ($status === 'EXECUTED') {
    // Update DB as paid

  $sSQL = "";
  $sWhere = "";       
  $sWhere = "no_resit=" . tosql($billId, "Text");
  $sWhere = " WHERE (" . $sWhere . ")";   
  $sSQL = "UPDATE resitonline SET "."
            statusRP=" . 1 ."
          ,updatedDate=" . tosql($date, "Text")."
          ,updatedBy=" . tosql($reference, "Text") ;
  $sSQL = $sSQL . $sWhere;

  $sSQL1 = "";
  $sSQL1 = "";
  $sSQL1 = "INSERT INTO transaction (" . 
          "docNo, " .
          "userID, " .
          "yrmth, " .
          "deductID, " .
          "MdeductID, " .
          "transID, " .
          "addminus, " .
          "pymtRefer, " .
          "pymtAmt, " .
          "updatedBy, " .
          "updatedDate, " .
          "createdBy, " .
          "createdDate) " .
                " VALUES (".
          "'". $billId . "', ".
          "'". $reference . "', ".
          "'". $yymm . "', ".
          "'". 1596 . "', ".
          "'". 1160 . "', ".
          "'". 79 . "', ".
          "'". 1 . "', ".
          "'". 'Online Banking' . "', ".
          "'". $amauntot . "', ".
          "'". $reference . "', ".
          "'". $date . "', ".
          "'". $reference . "', ".
          "'". $date . "') ";    

  $last_id = mysql_insert_id();

  $sSQL2 = "";
  $sSQL2 = "";
  $sSQL2 = "INSERT INTO transactionacc (" . 
          "docID, " .
          "IDtrans, " .
          "docNo, " .
          "tarikh_doc, " .
          "userID, " .
          "yrmth, " .
          "batchNo, " .
          "JdeductID, " .
          "deductID, " .
          "MdeductID, " .
          "addminus, " .
          "pymtAmt, " .
          "updatedBy, " .
          "updatedDate, " .
          "createdBy, " .
          "createdDate) " .
                " VALUES (".
          "'". 10 . "', ".
          "'". $last_id . "', ".
          "'". $bills_no . "', ".
          "'". $date . "', ".
          "'". $reference . "', ".
          "'". $yymm . "', ".
          "'". 0 . "', ".
          "'". 1596 . "', ".
          "'". 1160 . "', ".
          "'". 1159 . "', ".
          "'". 1 . "', ".
          "'". $amauntot . "', ".
          "'". $reference . "', ".
          "'". $date . "', ".
          "'". $reference . "', ".
          "'". $date . "') ";  

  $rs   = &$conn->Execute($sSQL);
  $rs1  = &$conn->Execute($sSQL1);         
  $rs2  = &$conn->Execute($sSQL2);


    // Your DB update code here
    http_response_code(200);
    echo "Payment received";
} else {
    // Log failed or other status for manual review
    http_response_code(202);
    echo "Payment status: $status";
}






ob_end_flush();
?>