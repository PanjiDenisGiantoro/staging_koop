<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptA22.php
 *		   Description	:	Laporan Penyata Ledger Mengikut Carta Akaun
 *          Date 		: 	13/7/2006
 *          Updated Date	: 	1/6/2024
 *********************************************************************************/
session_start();
include("common.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y, g:i a");

$title2  = 'PENYATA LEDGER';
$title2 = strtoupper($title2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));
if (get_session("Cookie_groupID") == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");window.close();</script>';
	exit;
}
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
	<LINK rel="stylesheet" href="images/mail.css" >
</head>
<body><table border="0" cellpadding="5" cellspacing="0" width="100%">';

print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="2" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="2" height="40"><font color="#FFFFFF">' . $title2 . ' ' . $dtFrom . ' HINGGA ' . $dtTo . '</font></th>
	</tr> 	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="2" align="right"></td>
	</tr>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $sqlLoan = "SELECT DISTINCT(a.deductID) AS deduct, a.MdeductID, b.* FROM transactionacc a, generalacc b WHERE (a.deductID=b.ID OR a.MdeductID=b.ID) AND b.ID = '$kodAkaun' AND (tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') ORDER BY b.code ASC";
// $sqlLoan = "SELECT DISTINCT(a.deductID) AS deduct, b.* FROM transactionacc a, generalacc b WHERE a.deductID=b.ID AND b.ID = '$kodAkaun' AND (tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') ORDER BY b.code ASC";
$sqlLoan = "SELECT DISTINCT(a.deductID) AS deduct, b.* FROM transactionacc a, generalacc b WHERE a.deductID=b.ID AND b.ID = '$kodAkaun' ORDER BY b.code ASC";

$rsLoan = $conn->Execute($sqlLoan);

while (!$rsLoan->EOF) {
	$i = 0;
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sql = "SELECT * FROM generalacc WHERE ID = '" . $rsLoan->fields(deduct) . " ' ";
	$Get =  &$conn->Execute($sql);
	if ($Get->RowCount() > 0) {
		$id = $rsLoan->fields("deduct");
	} else {
		$id = $rsLoan->fields("MdeductID");
	}

	$nameakaun = dlookup("generalacc", "name", "ID=" . tosql($id, "Number"));
	$codeakaun = dlookup("generalacc", "code", "ID=" . tosql($id, "Number"));

	$title  = 'CARTA AKAUN :- (' . $codeakaun . ') - ' . $nameakaun . ' ';
	$title = strtoupper($title);
	echo $yr;
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sSQL = "";
	// $sSQL = "SELECT	* FROM transactionacc WHERE (deductID = '$id' OR MdeductID = '$id') AND docID NOT IN (0) AND (tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."') ORDER BY tarikh_doc ASC,docNo";
	$sSQL = "SELECT	* FROM transactionacc WHERE deductID = '$id' AND docID NOT IN (15) AND (tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') ORDER BY tarikh_doc ASC,docNo";

	$rs = &$conn->Execute($sSQL);
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// $getOpen = "SELECT * FROM transactionacc WHERE docID IN (15) AND MdeductID = '".$id."' AND YEAR(tarikh_doc) = ".(int)substr($dtFrom,0,4)." ";
	// $getOpen = "SELECT * FROM transactionacc WHERE docID IN (15) AND deductID = '".$id."' AND YEAR(tarikh_doc) = ".(int)substr($dtFrom,0,4)." ";
	// $getOpen = "SELECT * FROM transactionacc WHERE docID IN (15) AND deductID = '".$id."' AND tarikh_doc < '".$dtFrom."' ";
	// $getOpen = "SELECT * FROM transactionacc WHERE docID IN (15) AND deductID = '".$id."' AND YEAR(tarikh_doc) = ".(int)substr($dtFrom,0,4)." ORDER BY tarikh_doc ASC LIMIT 1 ";
	$getOpen = "SELECT pymtAmt, addminus FROM transactionacc WHERE docID IN (15) AND deductID = '" . $id . "' AND tarikh_doc <= '" . $dtTo . "' ORDER BY tarikh_doc ASC LIMIT 1";
	$rsOpen = $conn->Execute($getOpen);

	$amaunD = 0;
	$amaunK = 0;

	//OP sahaja
	if ($rsOpen->fields(addminus) == 0) $amaunD = $rsOpen->fields(pymtAmt);
	if ($rsOpen->fields(addminus) == 1) $amaunK = $rsOpen->fields(pymtAmt);

	$getYuranOpen = "SELECT 
	SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
	SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
	FROM transactionacc
	WHERE
	deductID = '" . $id . "' AND docID NOT IN (15) 
	AND tarikh_doc < '" . $dtFrom . "'";
	$rsYuranOpen = $conn->Execute($getYuranOpen);

	$balanced = 0;
	$balancek = 0;

	//OP + transaction sebelum dtFrom
	$balanced = $rsYuranOpen->fields(yuranDb) + $amaunD;
	$balancek = $rsYuranOpen->fields(yuranKt) + $amaunK;

	//baki = OP sahaja
	$totBal = ($amaunD - $amaunK);

	//baki = campur OP dan transaction sebelum dtFrom
	$totalbalance = ($balanced - $balancek);

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	print '

	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="2" height="40" align="left"><b>' . $title . '</b></th>
	</tr> 
	
	<tr>
		<td colspan="2">
			<table border=1  cellpadding="2" cellspacing="0" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>Bil</th>
					<th nowrap align="center">Tarikh</th>
					<th nowrap align="left">Batch</th>
					<th nowrap align="left">Nombor Rujukan</th>
					<th nowrap align="left">Perkara</th>
					<th nowrap align="right">Debit(RM)</th>
					<th nowrap align="right">Kredit(RM)</th>
					<th nowrap align="right">Baki(RM)</th>
				</tr>';


	print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td width="10%" colspan=5 align="right">&nbsp;<b>Baki H/B</b></td>
			<td width="10%" align="right">&nbsp;<b>' . number_format($balanced, 2) . '</b></td>
			<td width="10%" align="right">&nbsp;<b>' . number_format($balancek, 2) . '</b></td>
			<td width="10%" align="right">&nbsp;<b>' . number_format($totalbalance, 2) . '</b></td>
		</tr>';

	$totaldebit = 0;
	$totalkredit = 0;
	$debTkre = 0;

	if ($rs->RowCount() <> 0) {
		while (!$rs->EOF) {

			$namabatch = dlookup("generalacc", "name", "ID=" . tosql($rs->fields(batchNo), "Number"));

			print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
				<td width="5%" align="center">' . ++$i . '.</td>
				<td width="5%" align="center">&nbsp;' . toDate('d/m/y', $rs->fields(tarikh_doc)) . '</td>
				<td width="10%">' . $namabatch . '</td>
				<td width="2%">' . $rs->fields(docNo) . '</td>';

			if ($rs->fields(docID) == 11) {
				$namaded = dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), "Number"));

				print '<td width="20%">' . $namaded . '</td>';
			} elseif ($rs->fields(docID) == 10) {
				$namaded = dlookup("resit", "catatan", "no_resit=" . tosql($rs->fields(docNo), "Text"));

				print '<td width="20%">' . $namaded . '</td>';
			} elseif ($rs->fields(docID) == 12) {
				$namaded = dlookup("vauchers", "keterangan", "no_baucer=" . tosql($rs->fields(docNo), "Text"));

				print '<td width="20%">' . $namaded . '</td>';
			} else {
				print '<td width="20%">' . $rs->fields(desc_akaun) . '</td>';
			}

			if ($rs->fields(addminus) == 0) {

				$debit = $rs->fields(pymtAmt);
				$totaldebit += $debit;

				print '<td width="5%" align="right">' . number_format($debit, 2) . '</td>
						<td width="5%" align="right">0.00</td>';
			}

			if ($rs->fields(addminus) == 1) {

				$kredit = $rs->fields(pymtAmt);
				$totalkredit += $kredit;
				print '<td width="5%" align="right">0.00</td>
						<td width="5%" align="right">' . number_format($kredit, 2) . '</td>';
			}


			$debTkre = ($totaldebit - $totalkredit);

			$belen = ($totalbalance + $debTkre);

			print '	<td width="5%" align="right">' . number_format($belen, 2) . '</td>
			</tr>';



			$rs->MoveNext();
		}

		print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;font-weight:bold;" bgcolor="FFFFFF">
			<td width="10%" colspan=4 align="right">&nbsp;</td>
			<td width="10%" align="right"><b>Jumlah </b></td>
			<td width="10%" align="right">&nbsp;' . number_format($totaldebit, 2) . '</td>
			<td width="10%" align="right">&nbsp;' . number_format($totalkredit, 2) . '</td>
			<td width="10%" align="right">&nbsp;</td>
		</tr>';



		print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td width="10%" colspan=4 align="right">&nbsp;</td>
			<td width="10%" align="right">&nbsp;<b>Baki B/B</b></td>
			<td width="10%" align="left">&nbsp;</td>
			<td width="10%" align="left">&nbsp;</td>
			<td width="10%" align="right">&nbsp;<b>' . number_format($belen, 2) . '</b></td>
		</tr>';
	} else {
		print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td colspan="8" align="center"><b>- Tiada Rekod Urusniaga -</b></td>
			</tr>';
	}
	print '		</table></td></tr>';

	$rsLoan->MoveNext();
}

if ($rsLoan->RecordCount() < 1)
	print '	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="7" align="center"><b>- Tiada Rekod -</b></td>
		</tr>';
print '	
</table></body></html>';
