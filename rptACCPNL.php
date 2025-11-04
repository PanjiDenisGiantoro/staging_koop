<?php

/*********************************************************************************
 *   Project	 : iKOOP.com.my
 *   Filename : rptA17
 *	Date 	 : 22/7/2020
 *	By 		 : Farhan
 *********************************************************************************/
session_start();
include("common.php");
include("AccountQry.php");

date_default_timezone_set("Asia/Jakarta");
$today = date("F j, Y, g:i a");
$month = (int)substr($yrmth, 4, 2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$title  = 'PROFIT AND LOSS DARI ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '';
$title	= strtoupper($title);

$sSQL1 = "";
$sSQL1 = "SELECT * FROM generalacc WHERE category = 'AA' ORDER BY parentID, ID";
$rs1 = &$conn->Execute($sSQL1);

$sSQL2 = "";
$sSQL2 = "SELECT * FROM generalacc WHERE category = 'AA' ORDER BY parentID, ID";
$rs2 = &$conn->Execute($sSQL2);

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
		<td colspan="5" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="85 height="40"><font color="#FFFFFF">' . $title . '</font>
		</th>
	</tr>
	<tr>
		<td colspan="5"><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>

	<tr>
	<td colspan="5"><b><font size=4>REVENUE <br /></font></b></td>
	</tr>
	
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap align="right">&nbsp;DEBIT (RP)</td>
						<td nowrap align="right">&nbsp;KREDIT (RP)</td>
						<td nowrap align="right">&nbsp;SALDO (RP)</td>
				</tr>';

$totaldebit1 = 0;
$totalkredit1 = 0;

if ($rs1->RowCount() <> 0) {

	while (!$rs1->EOF) {
		$ID = $rs1->fields(ID);
		$date = toDate("d/m/Y", $rs->fields(tarikh_baucer));
		$parentID = $rs1->fields(parentID);
		if ($parentID == 13) {
			print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td colspan="4" ><font size=3><b>&nbsp;<u>' . $rs1->fields(code) . ' - ' . $rs1->fields(name) . '</u></b></td>';


			$sSQL1a = "";
			$sSQL1a = "SELECT * FROM generalacc WHERE parentID = '" . $ID . "' ORDER BY parentID, ID";
			$rs1a = &$conn->Execute($sSQL1a);

			if ($rs1a->RowCount() <> 0) {
				$countA = 	$rs1a->RowCount();
				while ($countA != 0) {
					$IDB = $rs1a->fields(ID);

					$getAmaunD = getAmaunD($rs1a->fields(ID), $dtFrom, $dtTo);
					$debit = $getAmaunD->fields(amaun);

					$getAmaunK = getAmaunK($rs1a->fields(ID), $dtFrom, $dtTo);
					$kredit = $getAmaunK->fields(amaun);

					$balance1 = ($debit - $kredit);

					print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td  >&nbsp;&nbsp;&nbsp;*' . $rs1a->fields(code) . ' - ' . $rs1a->fields(name) . '</td>
						<td align="right">&nbsp;' . number_format($debit, 2) . '</td>
						<td align="right">&nbsp;' . number_format($kredit, 2) . '</td>
						<td align="right">&nbsp;' . number_format($balance1, 2) . '</td>
					';

					$countA = $countA - 1;
					$totaldebit1 += $debit;
					$totalkredit1 += $kredit;
					$rs1a->MoveNext();
				}
			}
		}

		$rs1->MoveNext();
	}
	$totalbalance1 = ($totalkredit1 - $totaldebit1);
	print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="1" align="right"><b>Jumlah Keseluruhan (RP) &nbsp;</b></td>
		<td align="right">' . number_format($totaldebit1, 2) . '</td>
		<td align="right">' . number_format($totalkredit1, 2) . '</td>
		<td align="right">' . number_format($totalbalance1, 2) . '</td>
		</tr>';

	print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="1" align="right"><b>Jumlah Pendapatan Bersih (RP) &nbsp;</b></td>
		<td align="right">' . number_format($totalbalance1, 2) . '</td>
		<td colspan="2" align="right">&nbsp;</td>
		</tr>';
} else {
	print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
			</tr>';
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '</table> 

<table border="0" cellpadding="5" cellspacing="0" width="100%">

	<tr>
	<td colspan="5"><b><font size=4>EXPENSES <br /></font></b></td>
	</tr>
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap align="right">&nbsp;DEBIT (RP)</td>
						<td nowrap align="right">&nbsp;KREDIT (RP)</td>
						<td nowrap align="right">&nbsp;SALDO (RP)</td>
				</tr>';
$totaldebit2 = 0;
$totalkredit2 = 0;

if ($rs2->RowCount() <> 0) {

	while (!$rs2->EOF) {
		$ID = $rs2->fields(ID);
		$date = toDate("d/m/Y", $rs2->fields(tarikh_baucer));
		$parentID = $rs2->fields(parentID);





		if ($parentID == 1172) {
			print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="4" ><font size=3><b>&nbsp;<u>' . $rs2->fields(code) . ' - ' . $rs2->fields(name) . '</u></b></td>';



			$sSQL2a = "";
			$sSQL2a = "SELECT * FROM generalacc WHERE parentID = '" . $ID . "' ORDER BY parentID, ID";
			$rs2a = &$conn->Execute($sSQL2a);

			if ($rs2a->RowCount() <> 0) {
				$countA = 	$rs2a->RowCount();

				while ($countA != 0) {
					$IDB = $rs2a->fields(ID);

					$getAmaunD = getAmaunD($rs2a->fields(ID), $dtFrom, $dtTo);
					$debit1 = $getAmaunD->fields(amaun);

					$getAmaunK = getAmaunK($rs2a->fields(ID), $dtFrom, $dtTo);
					$kredit1 = $getAmaunK->fields(amaun);

					$balance2 = ($debit1 - $kredit1);

					print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td>&nbsp;&nbsp;&nbsp;*' . $rs2a->fields(code) . ' - ' . $rs2a->fields(name) . '</td>
					<td align="right">&nbsp;' . number_format($debit1, 2) . '</td>
					<td align="right">&nbsp;' . number_format($kredit1, 2) . '</td>
					<td align="right">&nbsp;' . number_format($balance2, 2) . '</td>
				';
					$countA = $countA - 1;
					$rs2a->MoveNext();

					$totaldebit2 += $debit1;
					$totalkredit2 += $kredit1;
				}
			}
		}
		$rs2->MoveNext();
	}
	$totalbalance2 = ($totaldebit2 - $totalkredit2);

	print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="1" align="right"><b>Jumlah Keseluruhan (RP) &nbsp;</b></td>
		<td align="right">' . number_format($totaldebit2, 2) . '</td>
		<td align="right">' . number_format($totalkredit2, 2) . '</td>
		<td align="right">' . number_format($totalbalance2, 2) . '</td>
		</tr>';

	print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="1" align="right"><b>Jumlah Keseluruhan Perbelanjaan (RP) &nbsp;</b></td>
		<td align="right">' . number_format($totalbalance2, 2) . '</td>
		<td colspan="2" align="right">&nbsp;</td>
		</tr>';

	$allbalance		= ($totalbalance1 - $totalbalance2);
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="1" align="right"><b>Jumlah Bersih Keuntungan (RP) &nbsp;</b></td>
		<td align="right">' . number_format($allbalance, 2) . '</td>
		<td colspan="2" align="right">&nbsp;</td>
		</tr>';
} else {
	print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
		</tr>';
}
print '</table>
<tr><td colspan="5">&nbsp;</td></tr>
<tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
