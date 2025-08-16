<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *
 *
 *
 ******************************************************************************/
session_start();
include("common.php");

$today = date("F j, Y");
$today1 = date("j F Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);
$yr1 = $yr + 1;
if (!isset($yrmth));
$mth1 = $mth + 1;

$title  = 'Penyata Baki Percubaan (Trial Balance) pada ' . $today1;
$sSQL = "SELECT c.ID,c.code as codeObj, c.name as name, c.c_Panel as codeAcc,a.pymtAmt as kredit,b.kod_bank
		FROM transaction a, resit b ,general c
		WHERE 
		a.docNo = b.no_resit
		AND c.ID = a.deductID
		AND a.yrmth = '" . $yrmth . "'
		ORDER BY a.deductID";

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
		<td colspan="8" align="right">' . strtoupper($emaNetis) . '</td>
	</tr>

	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th colspan="8" height="40"><font color="#FFFFFF">' . $title . '<br></font>
		</th>
	</tr>

	<tr>
		<td colspan="8"><font size=1>Cetak pada : ' . $today . '</font></td>
	</tr>

	<tr>
		<td colspan="8" align="right"><font size=2>Bulan Terkini : ' . $today1 . '</font></td>
	</tr>

	<tr>
		<td colspan="8" align="right"><font size=2>Hari/Bulan/Hari : ' . $today1 . '</font></td>
	</tr>

	<tr>
		<td colspan="8">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;">
					<th nowrap>&nbsp;NO AKAUN</th>
					<th nowrap>&nbsp;KETERANGAN</th>
					<th nowrap>&nbsp;DEBIT</th>
					<th nowrap>&nbsp;KREDIT</th>
					<th nowrap>&nbsp;DEBIT</th>
					<th nowrap>&nbsp;KREDIT</th>
				</tr>

				<tr>
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;MYR</th>
					<th nowrap>&nbsp;MYR</th>
					<th nowrap>&nbsp;MYR</th>
					<th nowrap>&nbsp;MYR</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td align="center">&nbsp;' . $rs->fields(codeAcc) . '</td>
							<td>&nbsp;' . $rs->fields(name) . '</td>
							<td align="center">&nbsp;' . number_format($rs->fields(kredit), 2) . '</td>
							<td align="center">&nbsp;' . number_format($rs->fields(kredit), 2) . '</td>
							<td align="center">&nbsp;' . number_format($rs->fields(kredit), 2) . '</td>
							<td align="center">&nbsp;' . number_format($rs->fields(kredit), 2) . '</td>
						</tr>';
		$totalKt = $totalKt + $rs->fields(kredit);

		$rs->MoveNext();
	}
	print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td align="center"><b>&nbsp;</b></td>
							<td align="center"><b>&nbsp;</b></td>
							<td align="center"><b>&nbsp;' . number_format($totalKt, 2) . '</b></td>
							<td align="center"><b>&nbsp;' . number_format($totalKt, 2) . '</b></td>
							<td align="center"><b>&nbsp;' . number_format($totalKt, 2) . '</b></td>
							<td align="center"><b>&nbsp;' . number_format($totalKt, 2) . '</b></td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr align="center"><td colspan="8"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
