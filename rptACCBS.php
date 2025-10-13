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

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y, g:i a");
$month = (int)substr($yrmth, 4, 2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$title  = 'BALANCE SHEET DARI ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '';
$title	= strtoupper($title);

$sSQL1 = "";
$sSQL1 = "SELECT * FROM generalacc WHERE category = 'AA' ORDER BY parentID, ID";
$rs1 = &$conn->Execute($sSQL1);

$sSQL2 = "";
$sSQL2 = "SELECT * FROM generalacc WHERE category = 'AA' ORDER BY parentID, ID";
$rs2 = &$conn->Execute($sSQL2);

$sSQL3 = "";
$sSQL3 = "SELECT * FROM generalacc WHERE category = 'AA' ORDER BY parentID, ID";
$rs3 = &$conn->Execute($sSQL3);

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
	<td colspan="5"><b><font size=4>ASET <br /></font></b></td>
	</tr>
	
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left bgcolor="999999">
				<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap align="right">&nbsp;DEBIT (RP)</td>
						<td nowrap align="right">&nbsp;KREDIT (RP)</td>
						<td nowrap align="right">&nbsp;BAKI (RP)</td>
				</tr>';
//'.number_format($debit,2).'
$totaldebit = 0;
$totalkredit = 0;
$totaldebitA = 0;
$totalkreditA = 0;

if ($rs1->RowCount() <> 0) {

	while (!$rs1->EOF) {

		$ID = $rs1->fields(ID);
		$parentID = $rs1->fields(parentID);

		if ($parentID == 8) {
			print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td colspan="4" ><font size=3><b>&nbsp;<u>' . $rs1->fields(code) . ' - ' . $rs1->fields(name) . '</u></b></td>';

			$sSQL1a = "";
			$sSQL1a = "SELECT * FROM generalacc WHERE parentID = '" . $ID . "' ORDER BY parentID, code";
			$rs1a = &$conn->Execute($sSQL1a);

			if ($rs1a->RowCount() <> 0) {

				$countA = 	$rs1a->RowCount();
				while ($countA != 0) {

					$IDB = $rs1a->fields(ID);

					$getAmaunD = getAmaunD($rs1a->fields(ID), $dtFrom, $dtTo);
					$debitA = $getAmaunD->fields(amaun);

					$getAmaunK = getAmaunK($rs1a->fields(ID), $dtFrom, $dtTo);
					$kreditA = $getAmaunK->fields(amaun);

					$balanceA = ($debitA - $kreditA);

					print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td >&nbsp;&nbsp;&nbsp;<b>' . $rs1a->fields(code) . ' - ' . $rs1a->fields(name) . '</u></td>

			<td width="0%"  align="right">&nbsp;<b>' . number_format($debitA, 2) . '</b></td>
			<td width="0%"  align="right">&nbsp;<b>' . number_format($kreditA, 2) . '</b></td>
			<td width="5%"  align="right">&nbsp;<b>' . number_format($balanceA, 2) . '</b></td>';

					$sSQL1aa = "";
					$sSQL1aa = "SELECT * FROM generalacc WHERE parentID = '" . $IDB . "' ORDER BY parentID, code";
					$rs1aa = &$conn->Execute($sSQL1aa);

					if ($rs1aa->RowCount() <> 0) {
						$countB = 	$rs1aa->RowCount();

						while ($countB != 0) {

							$getAmaunD = getAmaunD($rs1aa->fields(ID), $dtFrom, $dtTo);
							$debit = $getAmaunD->fields(amaun);

							$getAmaunK = getAmaunK($rs1aa->fields(ID), $dtFrom, $dtTo);
							$kredit = $getAmaunK->fields(amaun);

							$balance = ($debit - $kredit);

							print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
			<td width="95%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*' . $rs1aa->fields(code) . ' - ' . $rs1aa->fields(name) . '</td>
			<td width="0%"  align="right">&nbsp;' . number_format($debit, 2) . '</td>
			<td width="0%"  align="right">&nbsp;' . number_format($kredit, 2) . '</td>
			<td width="5%"  align="right">&nbsp;' . number_format($balance, 2) . '</td>';

							$countB = $countB - 1;
							$totaldebit += $debit;
							$totalkredit += $kredit;

							$rs1aa->MoveNext();
						}
					}
					$countA = $countA - 1;
					$rs1a->MoveNext();
					$totaldebitA += $debitA;
					$totalkreditA += $kreditA;
				}
			}
		}
		$rs1->MoveNext();
	}
	$debitAset = ($totaldebit + $totaldebitA);
	$kreditAset = ($totalkredit + $totalkreditA);
	$totalbalance = ($debitAset - $kreditAset);

	print '
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="1" align="right"><b>Jumlah Keseluruhan (RP) &nbsp;</b></td>
	<td align="right">' . number_format($debitAset, 2) . '</td>
	<td align="right">' . number_format($kreditAset, 2) . '</td>
	<td align="right">' . number_format($totalbalance, 2) . '</td>
	</tr>';

	print '
	</tr>
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="1" align="right"><b>Jumlah Aset (RP) &nbsp;</b></td>
	<td align="right">' . number_format($totalbalance, 2) . '</td>
	<td colspan="2" align="right">&nbsp;</td>
	</tr>';
} else {
	print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
	</tr>';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '</table> 

	<tr>
	<td colspan="5"><b><font size=4>EKUITI <br /></font></b></td>
	</tr>
	<tr>
	<td colspan="5">
		<table border=0  cellpadding="2" cellspacing="1" align=left bgcolor="999999">
		<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
			<td nowrap height="20">&nbsp;</td>
			<td nowrap align="right">&nbsp;DEBIT (RP)</td>
			<td nowrap align="right">&nbsp;KREDIT (RP)</td>
			<td nowrap align="right">&nbsp;BAKI (RP)</td>
		</tr>';

$totaldebit1 = 0;
$totalkredit1 = 0;

if ($rs2->RowCount() <> 0) {

	while (!$rs2->EOF) {

		$ID = $rs2->fields(ID);
		$date = toDate("d/m/Y", $rs2->fields(tarikh_baucer));
		$parentID = $rs2->fields(parentID);

		if ($parentID == 10) {
			print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF" >
			<td colspan="4" ><font size=3><b>&nbsp;<u>' . $rs2->fields(code) . ' - ' . $rs2->fields(name) . '</u></b></td>';

			$sSQL2a = "";
			$sSQL2a = "SELECT * FROM generalacc WHERE parentID = '" . $ID . "' ORDER BY parentID, code";
			$rs2a = &$conn->Execute($sSQL2a);

			if ($rs2a->RowCount() <> 0) {
				$countA = 	$rs2a->RowCount();

				while ($countA != 0) {

					$IDB = $rs2a->fields(ID);

					print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td  colspan="4">&nbsp;&nbsp;&nbsp;<b>' . $rs2a->fields(code) . ' - ' . $rs2a->fields(name) . '</u></td>';

					$sSQL2aa = "";
					$sSQL2aa = "SELECT * FROM generalacc WHERE parentID = '" . $IDB . "' ORDER BY parentID, code";
					$rs2aa = &$conn->Execute($sSQL2aa);

					if ($rs2aa->RowCount() <> 0) {
						$countB = 	$rs2aa->RowCount();

						while ($countB != 0) {

							$getAmaunD = getAmaunD($rs2aa->fields(ID), $dtFrom, $dtTo);
							$debit1 = $getAmaunD->fields(amaun);

							$getAmaunK = getAmaunK($rs2aa->fields(ID), $dtFrom, $dtTo);
							$kredit1 = $getAmaunK->fields(amaun);

							$balance1 = ($debit1 - $kredit1);
							/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////keuntungan terkumpul//////////////////////////////////////////////////
							$sSQLK = "";
							$sSQLK = "SELECT SUM(b.pymtAmt) AS totKU FROM generalacc a, transactionacc b WHERE a.ID = b.MdeductID AND a.a_Kodkump = '36' AND b.addminus IN (1) AND (b.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')";
							$rsK = &$conn->Execute($sSQLK);

							$sSQD = "";
							$sSQLD = "SELECT SUM(b.pymtAmt) AS totDU FROM generalacc a, transactionacc b WHERE a.ID = b.MdeductID AND a.a_Kodkump = '36' AND b.addminus IN (0) AND (b.tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')";
							$rsD = &$conn->Execute($sSQLD);

							$KETK = $rsK->fields(totKU);
							$KETD = $rsD->fields(totDU);
							$TUntungTM = ($KETK - $KETD);
							/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////keuntungan terkumpul//////////////////////////////////////////////////
							print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td width="95%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*' . $rs2aa->fields(code) . ' - ' . $rs2aa->fields(name) . '</td>';
							if ($rs2aa->fields(ID) == '1140') {

								print '<td width="0%"  align="right">&nbsp;' . number_format($TUntungTM, 2) . '</td>';
							} else {
								print '<td width="0%"  align="right">&nbsp;' . number_format($debit1, 2) . '</td>';
							}

							print '
		<td width="0%"  align="right">&nbsp;' . number_format($kredit1, 2) . '</td>
		<td width="5%"  align="right">&nbsp;' . number_format($balance1, 2) . '</td>';

							$countB = $countB - 1;
							$rs2aa->MoveNext();
							$totaldebit1 += $debit1;
							$totalkredit1 += $kredit1;
						}
					}
					$countA = $countA - 1;
					$rs2a->MoveNext();
				}
			}
		}
		$rs2->MoveNext();
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
		<td colspan="1" align="right"><b>Jumlah Ekuiti (RP) &nbsp;</b></td>
		<td align="right">' . number_format($totalbalance1, 2) . '</td>
		<td colspan="2" align="right">&nbsp;</td>
		</tr>';
} else {
	print '
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
	<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
	</tr>';
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
print '</table> 
	<tr>
	<td colspan="5"><b><font size=4>LIABILITI <br /></font></b></td>
	</tr>
	
	<tr>
	<td colspan="5">
		<table border=0  cellpadding="2" cellspacing="1" align=left  bgcolor="999999">
		<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
						<td nowrap height="20">&nbsp;</td>
						<td nowrap align="right">&nbsp;DEBIT (RP)</td>
						<td nowrap align="right">&nbsp;KREDIT (RP)</td>
						<td nowrap align="right">&nbsp;BAKI (RP)</td>
				</tr>';

$totaldebit2 = 0;
$totalkredit2 = 0;

if ($rs3->RowCount() <> 0) {

	while (!$rs3->EOF) {

		$ID = $rs3->fields(ID);
		$date = toDate("d/m/Y", $rs3->fields(tarikh_baucer));
		$parentID = $rs3->fields(parentID);

		if ($parentID == 12) {
			print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF" >
			<td colspan="4" ><font size=3><b>&nbsp;<u>' . $rs3->fields(code) . ' - ' . $rs3->fields(name) . '</u></b></td>';

			$sSQL3a = "";
			$sSQL3a = "SELECT * FROM generalacc WHERE parentID = '" . $ID . "' ORDER BY parentID, code";
			$rs3a = &$conn->Execute($sSQL3a);

			if ($rs3a->RowCount() <> 0) {
				$countA = 	$rs3a->RowCount();

				while ($countA != 0) {

					$IDB = $rs3a->fields(ID);

					print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td  colspan="4">&nbsp;&nbsp;&nbsp;<b>' . $rs3a->fields(code) . ' - ' . $rs3a->fields(name) . '</u></td>';

					$sSQL3aa = "";
					$sSQL3aa = "SELECT * FROM generalacc WHERE parentID = '" . $IDB . "' ORDER BY parentID, code";
					$rs3aa = &$conn->Execute($sSQL3aa);

					if ($rs3aa->RowCount() <> 0) {
						$countB = 	$rs3aa->RowCount();

						while ($countB != 0) {

							$getAmaunD = getAmaunD($rs3aa->fields(ID), $dtFrom, $dtTo);
							$debit2 = $getAmaunD->fields(amaun);

							$getAmaunK = getAmaunK($rs3aa->fields(ID), $dtFrom, $dtTo);
							$kredit2 = $getAmaunK->fields(amaun);

							$balance2 = ($debit2 - $kredit2);

							print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td width="95%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*' . $rs3aa->fields(code) . ' - ' . $rs3aa->fields(name) . '</td>					
		<td width="0%"  align="right">&nbsp;' . number_format($debit2, 2) . '</td>
		<td width="0%"  align="right">&nbsp;' . number_format($kredit2, 2) . '</td>
		<td width="5%"  align="right">&nbsp;' . number_format($balance2, 2) . '</td>';

							$countB = $countB - 1;
							$rs3aa->MoveNext();
							$totaldebit2 += $debit2;
							$totalkredit2 += $kredit2;
						}
					}
					$countA = $countA - 1;
					$rs3a->MoveNext();
				}
			}
		}
		$rs3->MoveNext();




		// 	if ($rs3->RowCount() <> 0) {	

		// 		while(!$rs3->EOF) {	

		// 		$ID=$rs3->fields(ID);
		// 		$parentID=$rs3->fields(parentID);	

		// 		if($parentID==12){
		// 		print '
		// 		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		// 		<td colspan="4" ><font size=3><b>&nbsp;<u>'.$rs3->fields(code).' - '.$rs3->fields(name).'</u></b></td>';

		// 		$sSQL3a = "";
		// 		$sSQL3a = "SELECT * FROM generalacc WHERE parentID = '".$ID."' ORDER BY parentID, code";	

		// 		//$sSQL3a = "SELECT a.*,b.* FROM generalacc a,transactionacc b WHERE a.ID = b.deductID AND a.parentID = '".$ID."' GROUP BY b.deductID ORDER BY a.parentID, a.code";	

		// 		$rs3a = &$conn->Execute($sSQL3a);

		// 		if($rs3a->RowCount() <> 0){
		// 		$countA = 	$rs3a->RowCount();

		// 		while($countA != 0) {							

		// 		$getAmaunD = getAmaunD($rs3a->fields(ID),$dtFrom,$dtTo);
		// 		$debit2 = $getAmaunD->fields(amaun);	

		// 		$getAmaunK = getAmaunK($rs3a->fields(ID),$dtFrom,$dtTo);
		// 		$kredit2 = $getAmaunK->fields(amaun);

		// 		$balance2 = ($debit2 - $kredit2);

		// 		print '
		// 			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		// 			<td width="95%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*'.$rs3a->fields(code).' - '.$rs3a->fields(name).'</td>
		// 			<td width="0%" align="right">&nbsp;'.number_format($debit2,2).'</td>
		// 			<td width="0%" align="right">&nbsp;'.number_format($kredit2,2).'</td>
		// 			<td width="5%" align="right">&nbsp;'.number_format($balance2,2).'</td>';	

		// 		$countA = $countA - 1;
		// 		$rs3a->MoveNext();	
		// 		$totaldebit2 += $debit2;		
		// 		$totalkredit2 += $kredit2;
		// 		}	
		// 	}
		// }
		// 	$rs3->MoveNext();
	}
	$totalbalance2 = ($totalkredit2 - $totaldebit2);

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
		<td colspan="1" align="right"><b>Jumlah Liabiliti (RP) &nbsp;</b></td>
		<td align="right">' . number_format($totalbalance2, 2) . '</td>
		<td colspan="2" align="right">&nbsp;</td>
		</tr>';

	$total_liaeku 	= ($totalbalance1 + $totalbalance2);
	$allbalance		= ($totalbalance - $total_liaeku);
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="1" align="right"><b>Jumlah Liabiliti + Ekuiti (RP) &nbsp;</b></td>
		<td align="right">' . number_format($total_liaeku, 2) . '</td>
		<td colspan="2" align="right">&nbsp;</td>
		</tr>';
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	print '
		</tr>
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="1" align="right"><b>- &nbsp;</b></td>
		<td align="right">' . number_format($allbalance, 2) . '</td>
		<td colspan="2" align="right">&nbsp;</td>
		</tr>';
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} else {
	print '
		<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
		<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
		</tr>';
}
print '</table>
</td></tr>
<tr><td colspan="5">&nbsp;</td></tr>
<tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
