<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptE.php
 *		   Description	:	Report Status Kelulusan Pembiayaan
 *		   Parameter	:   $dateFrom , $dateTo
 *          Date 		: 	12/12/2003
 *********************************************************************************/
session_start();
include("common.php");

$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Kelulusan Pembiayaan RP5001-RP10000 Lulus (>41%)';

$sSQL = "";

$sSQL = "SELECT a.*,b.*,c.grossPay, DATEDIFF(a.applyDate,b.ajkDate2)as date1
		FROM loans a, loandocs b, userdetails c
		WHERE a.loanID = b.loanID
		AND a.userID = c.userID
		AND b.result = 'lulus'
		AND (b.ajkDate2 BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "')
		AND (b.gajiTot >= 5001 AND b.gajiTot <=10000) AND (a.Nisbahdsr >40)
		ORDER BY date1 ASC";



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
			Dari ' . toDate("d/m/Y", $dtFrom) . ' Hingga ' . toDate("d/m/Y", $dtTo) . '</font>
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
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;No Ahli</th>
					<th nowrap>&nbsp;No Rujukan Pembiayaan</th>
					<th nowrap>&nbsp;Tempoh Pembiayaan</th>
					<th nowrap>&nbsp;Jumlah Dipohon</th>	
					<th nowrap>&nbsp;Total Gaji Pokok + Elaun</th>
					<th nowrap>&nbsp;Bawah 10000</th>
									
					<th nowrap>&nbsp;(C Bahagi E) Nisbah Pembayaran Balik Hutang DSR(%)</th>
					<th nowrap>&nbsp;> 40%</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		//$jabatan = dlookup("userdetails", "grossPay", "userID=" . tosql($rs->fields(userID), "Text"));		
		$totalsum = $totalsum + $rs->fields('loanAmt');
		$dsr40 = dlookup("loans", "Nisbahdsr", "loanID=" . tosql($rs->fields(loanID), "Text"));
		$year = ($rs->fields('loanPeriod') / 12);


		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . dlookup("users", "name", "userID=" . tosql($rs->fields(userID), "Text")) . '</a></td>
							<td>&nbsp;</a>' . dlookup("userdetails", "memberID", "userID=" . tosql($rs->fields(userID), "Text")) . '</td>
							<td>&nbsp;' . $rs->fields(loanNo) . '</td>
							
							<td align="center">&nbsp;' . number_format($year) . 'THN</a></td>
							
							<td align="right">' . number_format($rs->fields('loanAmt'), 2) . '&nbsp; </a></td>
							<td align="right">&nbsp;' . $rs->fields(gajiTot) . '</td>
							<td align="right">&nbsp;' . $rs->fields(gajiTot) . '</td>
							
							<td align="right">' . number_format($rs->fields('Nisbahdsr'), 2) . '&nbsp; </a></td>
							<td align="right">' . number_format($rs->fields('Nisbahdsr'), 2) . '&nbsp; </a></td>';

		print '</tr>';
		$rs->MoveNext();
	}
	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="5">&nbsp;Jumlah Keseluruhan:</td>
							<td align="right">&nbsp;' . number_format($totalsum, 2) . '</td>
							<td colspan="5">&nbsp;</td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="10" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="9">&nbsp;</td></tr>
	<tr align="center"><td colspan="9"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
