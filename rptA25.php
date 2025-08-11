<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptACCGL.php
 *		   Description	:	Report General Ledger
 *		   Parameter	:   $dateFrom , $dateTo
 *          Date 		: 	12/12/2021
 *********************************************************************************/
session_start();
include("common.php");
include("AccountQry.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'IMBANGAN DUGA (TRIAL BALANCE)';

$sSQL = "";
//$sSQL = "SELECT *,ID as transID FROM transactionacc WHERE docID NOT IN (0) AND (tarikh_doc BETWEEN '".$dtFrom."' AND '".$dtTo."')  ORDER BY docNo ASC,tarikh_doc";

$sSQL = "SELECT a.*,b.* FROM transactionacc a, generalacc b WHERE a.deductID=b.ID AND a.docID NOT IN (0) AND (a.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') GROUP BY a.deductID ORDER BY b.code ";
//GROUP BY a.deductID 
$rs = &$conn->Execute($sSQL);
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td colspan="9" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="9" height="40"><font color="#FFFFFF">' . $title . '<br>
			DARI ' . toDate("d/m/Y", $dtFrom) . ' HINGGA ' . toDate("d/m/Y", $dtTo) . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="5"><font size=1>CETAK PADA : ' . $today . '</font></td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap align ="center">BIL</th>
					<th nowrap align ="center">KOD AKAUN</th>
					<th nowrap align ="left">NAMA AKAUN</th>
					<th nowrap align ="right">SIMPANAN(DEBIT) (RM)</th>
					<th nowrap align ="right">KELUARAN(KREDIT) (RM)</th>	
				</tr>';

$totaldebit 	= 0;
$totalkredit 	= 0;

if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;

		//$ID 			= $rs->fields(deductID);
		$jumlah 		= 0;
		$tarikh_baucer 	= toDate("d/m/y", $rs->fields(tarikh_doc));
		$glname 		= dlookup("generalacc", "name", "ID=" . tosql($rs->fields(deductID), "Text"));
		$glnameCode 	= dlookup("generalacc", "code", "ID=" . tosql($rs->fields(deductID), "Text"));

		$getAmaunTBD	= getAmaunTBD($rs->fields(deductID), $dtFrom, $dtTo);
		$debit1 		= $getAmaunTBD->fields(amaun);

		$getAmaunTBK	= getAmaunTBK($rs->fields(deductID), $dtFrom, $dtTo);
		$kredit1 		= $getAmaunTBK->fields(amaun);

		print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td width="2%" align="center">' . $bil . ')&nbsp;</td>
		<td align="center">' . $glnameCode . '</a></td>
		<td align="left">' . $glname . ' </a></td>';


		if ($debit1 == 0) {
			print '	<td class="Data" align ="right">0.00</td>';
		} else {
			print '	<td class="Data" align ="right">' . $debit1 . '</td>';
			$totaldebit += $debit1;
		}

		if ($kredit1 == 0) {
			print '	<td class="Data" align ="right">0.00</td>';
		} else {
			print '	<td class="Data" align ="right">' . $kredit1 . '</td>';
			$totalkredit += $kredit1;
		}

		print '</tr>';

		$rs->MoveNext();
	}

	print '	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="3" align="right"><b>&nbsp;JUMLAH KESELURUHAN (RM)</b></td>
		<td align="right">' . number_format($totaldebit, 2) . '</td>
		<td align="right">' . number_format($totalkredit, 2) . '</td>
	</tr>';

	$baki = ($totaldebit - $totalkredit);

	print '	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="3" align="right"><b>&nbsp;BAKI (RM)</b></td>
		<td colspan="2" align="right">' . number_format($baki, 2) . '</td>
	</tr>';
} else {
	print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="5" align="center"><b>- TIADA REKOD DICETAK-</b></td>
	</tr>';
}
print '</table></td></tr>
</table></body></html>
<tr><td colspan="5">&nbsp;</td></tr>
<center><tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
