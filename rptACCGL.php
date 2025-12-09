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

date_default_timezone_set("Asia/Jakarta");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'GENERAL LEDGER';

$sSQL = "";
$sSQL = "SELECT *,ID as transID FROM transactionacc WHERE docID NOT IN (0) AND (createdDate BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') ORDER BY docNo ASC,createdDate";
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
	<tr bgcolor="#336699" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
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
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;NOMBOR RUJUKAN</th>
					<th nowrap>&nbsp;TARIKH</th>
					<th nowrap>&nbsp;NAMA AKAUN GL</th>
					<th nowrap align ="right">&nbsp;SIMPANAN(DEBIT)(RP)</th>
					<th nowrap align ="right">&nbsp;KELUARAN(KREDIT)(RP)</th>	
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {

		$jumlah = 0;
		$tarikh_baucer = toDate("d/m/y", $rs->fields(createdDate));
		$glname = dlookup("generalacc", "name", "ID=" . tosql($rs->fields(deductID), "Text"));

		$bil++;
		print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td width="2%" align="right">' . $bil . ')&nbsp;</td>
		<td>&nbsp;' . $rs->fields(docNo) . '</td>
		<td align="center">' . $tarikh_baucer . '</a></td>
		<td>&nbsp;' . $glname . '</a></td>';

		if ($rs->fields(addminus) == 0) {
			$amaundebit = $rs->fields(pymtAmt);
			print '	<td class="Data" align ="right">&nbsp;' . $amaundebit . '</td>';
			print '	<td class="Data" align ="right">&nbsp;0.00</td>';

			$totaldebit += $amaundebit;
		}
		if ($rs->fields(addminus) == 1) {
			$amaunkredit = $rs->fields(pymtAmt);
			print '	<td class="Data" align ="right">&nbsp;0.00</td>';
			print '	<td class="Data" align ="right">&nbsp;' . $amaunkredit . '</td>';

			$totalkredit += $amaunkredit;
		}

		print '</tr>';

		$rs->MoveNext();
	}



	print '	
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="4" align="right">&nbsp;JUMLAH KESELURUHAN</td>
		<td align="right">RP&nbsp;' . number_format($totaldebit, 2) . '</td>
		<td align="right">RP&nbsp;' . number_format($totalkredit, 2) . '</td>
	</tr>';
} else {
	print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="5" align="center"><b>- TIADA REKOD DICETAK-</b></td>
	</tr>';
}
print '</table></td></tr>

	<tr><td colspan="5">&nbsp;</td></tr>
	<tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table></body></html>';
