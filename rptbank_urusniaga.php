<?php
#put this in the very beginning
//$timestart = microtime();
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAllFee.php
 *		   Description	:	Ringkasan Keseluruhan Yuran Anggota 
 *          Date 		: 	31/5/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");
//$month = (int)substr($yrmth,4,2);
$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);
$yr1 = $yr + 1;
if (!isset($yrmth));
$mth1 = $mth + 1;

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Senarai Baki Akhir Bagi Pada Bulan ' . displayBulan($mth) . ' Tahun ' . $yr;

$sSQL = "SELECT * FROM general WHERE category = 'Z' ORDER BY CAST( ID AS SIGNED INTEGER ) ASC";

$rs = &$conn->Execute($sSQL);

print '
<html>
<head>
	<title>' . $emaNetis . '</title>
</head>
<body>';
print '
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>';
if ($rs->RowCount() <> 0) {

	print '
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;Bil</th>
					<th nowrap>&nbsp;Bank</th>
					<th nowrap>&nbsp;Debit</th>
					<th nowrap>&nbsp;Kredit</th>
				</tr>';

	$bil = 1;
	while (!$rs->EOF) {
		$getsumkredit = getsumkredit($rs->fields(ID), $yrmth);
		$sumkredit = $getsumkredit->fields(kredit);
		$getsumdebit = getsumdebit($rs->fields(ID), $yrmth);
		$sumdebit = $getsumdebit->fields(debit);
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td class="Data" align="center">' . $bil . ')&nbsp;</td>
							<td class="Data">&nbsp;' . $rs->fields(name) . '</td>
							
							<td class="Data" align="right">&nbsp;' . $sumdebit . '</td>
							<td class="Data" align="right">&nbsp;' . $sumkredit . '</td>
						</tr>';

		$rs->MoveNext();
		$bil = $bil + 1;
		$totalDb = $totalDb + $sumdebit;
		$totalKt = $totalKt + $sumkredit;
	}


	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="2">&nbsp;Jumlah Keseluruhan:</td>
							<td align="right"><b>&nbsp;' . number_format($totalDb, 2) . '</b></td>
							<td align="right"><b>&nbsp;' . number_format($totalKt, 2) . '</b></td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="8" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
$rs->Close();
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr align="center"><td colspan="8"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
