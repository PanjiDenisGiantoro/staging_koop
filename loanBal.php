<?php
include("common.php");
$today = date("F j, Y, g:i a");
$strTemp = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/mail.css" >
</head>
<body><table border="0" cellpadding="5" cellspacing="0" width="100%">';
$yr = '2007';
$sqlLoan = "SELECT a . * , (
				a.loanAmt * a.kadar_u /100 * a.loanPeriod /12
				) AS tot_untung, b.rnoBaucer
				FROM loans a, loandocs b
				WHERE a.loanID = b.loanID
				AND b.rnoBaucer <> '' and a.status = 3";

$rsLoan = $conn->Execute($sqlLoan);
while (!$rsLoan->EOF) {
	$i = 0;

	$sql = "select loanType FROM `loans` where loanID = '" . $rsLoan->fields(loanID) . "'";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) $loanType = $Get->fields(loanType);

	$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

	$id = $rsLoan->fields(userID);
	$loanType = $rsLoan->fields(loanType);
	$loanNo = $rsLoan->fields(loanNo);    //add loan No ref style
	$loanID = $rsLoan->fields(loanID);
	//get deduct code
	$id_kod_potongan = dlookup("general", "c_Deduct", "ID=" . $loanType);

	$nama_Pembiayaan = dlookup("general", "name", "ID=" . tosql($rsLoan->fields(loanType), "Number"));

	$bond = dlookup("loandocs", "rnoBond", "loanID=" . $rsLoan->fields(loanID));
	//		 AND deductID = ".$id_kod_potongan." 

	//remark - this is to display detail of trasaction
	/*
$sSQL = "";
$sSQL = "SELECT	*  
		 FROM transaction 
		 WHERE userID = '$id' 
		 AND pymtRefer = '$bond'
		 AND year(createdDate) <= $yr ORDER BY createdDate";
		 //AND yrmth like " . tosql("%".$yr."%","Text"); 
$rs = &$conn->Execute($sSQL);
*/
	//get loan maded total with interest
	$getJumlahLoan = "SELECT loanAmt + (loanAmt * kadar_u/100 * loanPeriod/12) AS jumlahPembiayaan
				  FROM loans  
				  WHERE loanID = " . $loanID;
	$rsJumlahLoan = $conn->Execute($getJumlahLoan);
	$jumlahPembiayaan = $rsJumlahLoan->fields(jumlahPembiayaan);

	$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID = '" . $c_Deduct . "' 
		AND userID = '" . $id . "' 
		AND year(createdDate) <= " . $yr . "
		GROUP BY userID";
	$rsOpen = $conn->Execute($getOpen);
	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);
	else $bakiAwal = 0;
	$bakiAkhir = 0;

	/* remark - this is to find interest bal.
$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '".$bond."'
		AND deductID <> '".$c_Deduct."' 
		AND userID = '".$id."' 
		AND year(createdDate) <= ".$yr."
		GROUP BY userID";
$rsOpen = $conn->Execute($getOpen);
if ($rsOpen->RowCount() == 1) $bakiAwalUnt = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
else $bakiAwalUnt = 0;
$bakiAkhirUnt = 0;
*/

	$strTemp .=	'
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td>' . dlookup("userdetails", "memberID", "userID=" . tosql($id, "Text")) . ',</td>
		<td>' . dlookup("users", "name", "userID=" . tosql($id, "Text")) . ',</td>
		<td>' . dlookup("userdetails", "newIC", "userID=" . tosql($id, "Text")) . ',</td>
		<td>' . $nama_Pembiayaan . ',</td>
		<td>' . $bakiAwal . '</td>
	</tr>
';
	$rsLoan->MoveNext();
}

$strTemp .= '	
</table>
</body>
</html>';

print $strTemp;
