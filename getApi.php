<?php 
ob_start();
include("header.php");
include("koperasiQry.php");

$sFileName = "index.php";

  date_default_timezone_set("Asia/Kuala_Lumpur"); 
  $date = date("Y-m-d H:i:s"); 
  $today = date("Y-m-d"); 

if (!isset($mm))  $mm=date("m");
if (!isset($yy))  $yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

$bills_no         = $_GET['payment_link_id']; 
$paymentstatus    = $_GET['payment_status'];
$amaun            = $_GET['payment_amount'];
$reference        = $_GET['payment_link_reference'];
$cajamaun         = 1;
$amauntot         = ($amaun - $cajamaun);

echo hash_hmac('SHA256', '-'.$data, '-');

$data1     = json_decode($data);

if($paymentstatus == 0)
{
  echo "Transaksi tidak berjaya.";
}

else{ 

  $sSQL = "";
  $sWhere = "";       
  $sWhere = "no_resit=" . tosql($bills_no, "Text");
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
          "'". $bills_no . "', ".
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
          "'". 1596 . "', ".
          "'". 1596 . "', ".
          "'". 1 . "', ".
          "'". $amauntot . "', ".
          "'". $reference . "', ".
          "'". $date . "', ".
          "'". $reference . "', ".
          "'". $date . "') ";  

  $rs   = &$conn->Execute($sSQL);
  $rs1  = &$conn->Execute($sSQL1);         
  $rs2  = &$conn->Execute($sSQL2);

  print '<script>
          alert ("Transaksi Berjaya.")'; 
         header("Location: ".$sFileName);
  print'</script>';
} 

ob_end_flush();
?>