<?php

/*********************************************************************************
 *          Project		:	Sistem e-Koperasi(e-Koop) SEKATARAKYAT
 *          Filename		: 	rptBiayaBAK_AKT.php
 *		   Description	:	Report Status Kelulusan Pembiayaan
 *		   Parameter	:   $dateFrom , $dateTo
 *          Date 		: 	12/12/2022
 *********************************************************************************/
session_start();

include("common.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y, g:i a");

$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);

$title  = 'Laporan Keseluruhan Baki Akhir Pembiayaan Aktif';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBaki($id, $yrmth, $bond)
{
	global $conn;

	$getASHOpen = "SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN 
		(1539,1613,1614,1619,1631,1644,1646,1661,1673,1675,1709,1841,1899,1930,1936,1974,3394,3409,3415,3419,3423,3505,3543,3544,3558,3562,3597,1644,1660,1679,1707,1722,1800,3616,3618)

		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsASHOpen = $conn->Execute($getASHOpen);
	if ($rsASHOpen->RowCount() == 1) {
		$DB = $rsASHOpen->fields(yuranDb);
		$KT = $rsASHOpen->fields(yuranKt);
		$bakiAwalASH = ($DB - $KT);
	} else $bakiAwalASH = 0;
	return $bakiAwalASH;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBakiU($id, $yrmth, $bond)
{
	global $conn;

	$getASHOpen = "SELECT SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction 
		WHERE
		deductID IN 
		(1642,1645,1649,1663,1676,1973,1975,1976,1977,1978,1979,1980,1981,1982,3418,3422,3425,3431,3506,3545,3546,3559,3563,3598,3617,3619)

		AND userID = '" . $id . "'
		AND pymtRefer = '" . $bond . "'
		AND yrmth <= '" . $yrmth . "'
		GROUP BY pymtRefer";

	$rsASHOpen = $conn->Execute($getASHOpen);
	if ($rsASHOpen->RowCount() == 1) {
		$DB = $rsASHOpen->fields(yuranDb);
		$KT = $rsASHOpen->fields(yuranKt);
		$bakiAwalASH = ($DB - $KT);
	} else $bakiAwalASH = 0;
	return $bakiAwalASH;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sSQL = "";
$sSQL = "SELECT a.*,b.*,c.userID FROM loans a, loandocs b, userdetails c
		WHERE a.loanID = b.loanID
		AND a.userID = c.userID
		AND b.rnoBaucer <> ''
		AND a.status IN (3)ORDER BY CAST(a.userID AS SIGNED INTEGER)";
// AND a.userID IN (10412) 
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
		<th colspan="9" height="40"><font color="#FFFFFF">' . $title . ' Pada ' . $mth . '/' . $yr . ' <br></font>
		</th>
	</tr>
	<tr>
		<td colspan="9"><font size=1>Cetak pada : ' . $today . '</font></td>
	</tr>
	<tr><td colspan="9">&nbsp;</td></tr>
	<tr>
		<td colspan="9">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;Bil</th>
					<th nowrap align="center">&nbsp;Nombor Anggota</th>
					<th nowrap align="left">&nbsp;Nama Anggota</th>
					<th nowrap align="left">&nbsp;Nombor Rujukan Pembiayaan</th>
					<th nowrap align="left">&nbsp;Nama Pembiayaan</th>
					<th nowrap align="left">&nbsp;Nombor Bond</th>
					<th nowrap align="right">&nbsp;Jumlah Pembiayaan Pokok (RM)</th>	
					<th nowrap align="right">&nbsp;Jumlah Pembiayaan Untung (RM)</th>	
					<th nowrap align="right">&nbsp;Baki Pembiayaan Pokok (RM)</th>
					<th nowrap align="right">&nbsp;Baki Pembiayaan Untung (RM)</th>		
				</tr>';

/*
					<th nowrap align="right">&nbsp;Baki Pembiayaan Sebenar (RM)</th>*/

$totalPALL  = 0;
$totalUALL  = 0;
$totalALL  	= 0;

if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {

		$bil++;
		$year 		= ($rs->fields('loanPeriod') / 12);
		$date 		= toDate("d/m/Y", $rs->fields(approvedDate));
		$loanName 	= dlookup("general", "name", "ID=" . tosql($rs->fields(loanType), "Text"));

		$bond 		= $rs->fields(rnoBond);

		$baki 		= getBaki($rs->fields(userID), $yrmth2, $bond);
		$bakiU 		= getBakiU($rs->fields(userID), $yrmth2, $bond);

		$bakiUP 	= ($rs->fields(lpotUntungN)) - ($bakiU);
		$bakiALL 	= ($baki + $bakiUP);

		$loanPOKUNT = (($rs->fields(lpotUntungN)) + ($rs->fields(loanAmt)));
		$bakiSEBEN	= ($loanPOKUNT - $bakiALL);



		print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
				<td width="2%" align="right">' . $bil . ')&nbsp;</td>

				<td  align="center">&nbsp;</a>' . dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")) . '</td>
				<td  align="left">&nbsp;' . dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text")) . '</a></td>

				<td  align="center">&nbsp;' . $rs->fields(loanNo) . '</td>
				<td  align="left">&nbsp;' . $loanName . '</td>
				<td  align="center">&nbsp;' . $bond . '</td>

				<td align="right">' . number_format($rs->fields('loanAmt'), 2) . '&nbsp; </a></td>
				<td align="right">' . number_format($rs->fields('lpotUntungN'), 2) . '&nbsp; </a></td>
				<td align="right">' . number_format($baki, 2) . '&nbsp; </a></td>
				<td align="right">' . number_format($bakiUP, 2) . '&nbsp; </a></td>';
		// <td align="right">'.number_format($bakiALL, 2).'&nbsp; </a></td>

		print '</tr>';
		$totalpokok 	= $totalpokok + $rs->fields('loanAmt');
		$totaluntung 	= $totaluntung + $rs->fields('lpotUntungN');


		$totalPALL  	= $totalPALL + $baki;
		$totalUALL  	= $totalUALL + $bakiUP;

		$totalALL  		= $totalALL + $bakiALL;




		$rs->MoveNext();
	}
	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
					<td colspan="6" align="right">&nbsp;<b>Jumlah Keseluruhan (RM):</b></td>
					<td align="right"><b>&nbsp;' . number_format($totalpokok, 2) . '</b></td>
					<td align="right"><b>&nbsp;' . number_format($totaluntung, 2) . '</b></td>
					<td align="right"><b>&nbsp;' . number_format($totalPALL, 2) . '</b></td>
					<td align="right"><b>&nbsp;' . number_format($totalUALL, 2) . '</b></td>
				</tr>';
	//				<td align="right"><b>&nbsp;'.number_format($totalALL,2).'</b></td>

} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="9" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="9">&nbsp;</td></tr>
	<tr><td colspan="9" align="center"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
