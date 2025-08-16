<?php
include	("common.php");
// Redirect //
date_default_timezone_set("Asia/Kuala_Lumpur");

if (!isset($mm))	$mm=date("m");
if (!isset($yy))	$yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
// get all params
$amount = $_GET['amount'];
$billId = $_GET['bill_id'];
$billNo = $_GET['bill_no'];
$currency = $_GET['currency'];
$paid = $_GET['paid'];
$paymentMethod = $_GET['payment_method'];
$ref1 = $_GET['ref1'];
$ref2 = $_GET['ref2'];
$refId = $_GET['ref_id'];
$status = $_GET['status'];
$signature = $_GET['signature'];

// semua kecuali signature
$combinationFieldsValues = 'amount:' . $amount . '|' . 'bill_id:' . $billId . '|';

// compare signature
$generatedSignature = hash_hmac('sha256',
            $combinationFieldsValues, 'EZUR6my_6lTowE5AgqeCvTg9w66LqKdU');
            
if (strcmp($generatedSignature, $signature)) {
    // show thank you page based on paid
}        

// End Redirect
// Calback
/*
// get all params
$amount = $_POST['amount'];
$billId = $_POST['bill_id'];
$billNo = $_POST['bill_no'];
$currency = $_POST['currency'];
$paid = $_POST['paid'];
$paymentMethod = $_POST['payment_method'];
$ref1 = $_POST['ref1'];
$ref2 = $_POST['ref2'];
$refId = $_POST['ref_id'];
$status = $_POST['status'];
$signature = $_POST['signature'];
*/

// semua kecuali signature
$combinationFieldsValues = 'amount:' . $amount . '|' . 'bill_id:' . $billId . '|' . 'ref1:' . $ref1 . '|' . 'ref2:' . $ref2 .'|' . 'bill_no:' . $billNo . '|'; 

// compare signature
$generatedSignature = hash_hmac('sha256',
            $combinationFieldsValues, 'EZUR6my_6lTowE5AgqeCvTg9w66LqKdU');
            
if ($paid == 1) {
		$updatedDate = date("Y-m-d H:i:s");  	  		

			  		$sSQL = "";
					$sWhere = "";		
				    $sWhere = "no_resit='" . $billNo ."'";
					$sWhere = " WHERE (" . $sWhere . ")";	
	
		$sSQL	=	"UPDATE resitonline SET " .
					"updatedDate='" . $updatedDate . "',".
					"statusRP='" . 1 ."'";		
					$sSQL = $sSQL . $sWhere;
					$rs = &$conn->Execute($sSQL);

		$sSQL1	= "INSERT INTO transaction (" . 
				  "docNo," . 
				  "userID," . 
				  "yrmth," .			
				  "deductID," . 
				  "transID," .			
				  "addminus," . 
				  "pymtID," . 
				  "pymtRefer," .			
				  "pymtAmt," . 
				  "createdDate," . 
				  "createdBy," . 
				  "updatedDate," . 
				  "updatedBy)" . 
				  " VALUES (" . 
				"'". $billNo . "', ".
				"'". $ref1 . "', ".
				"'". $yymm . "', ".
				"'". 1596 . "', ".
				"'". 79 . "', ".
				"'". 1 . "', ".
				"'". 66 . "', ".
				"'". $paymentMethod . "', ".
				"'". $amount . "', ".
				"'". $updatedDate . "', ".
				"'". $ref1 . "', ".
				"'". $updatedDate . "', ".
				"'". $ref1 . "')";
				$rs1 = &$conn->Execute($sSQL1);

// process payment and email user
}  
header("Location: https://ikoop.com.my/demo/index.php");
// End Callback