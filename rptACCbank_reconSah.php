<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *			Nama 		: 	rptACCbank_reconSah.php
 *
 *
 ******************************************************************************/
session_start();
include("common.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
$today = date("F j, Y");

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

$title  = 'Laporan Rekonsilasi Bank (Ada Pengesahan) Dari ' . $dtFrom . ' Hingga ' . $dtTo . '';

$sSQL = "SELECT * FROM transactionacc WHERE stat_check IN (1) AND (tarikh_doc BETWEEN '" . $dtFrom . "' AND '" . $dtTo . "') ORDER BY tarikh_doc ASC ";
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
		<td colspan="8"><font size=1>Cetak Pada : ' . $today . '</font></td>
	</tr>
	<tr><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td colspan="8">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;Bil</th>
					<th nowrap>&nbsp;Nombor Rujukan</th>
					<th nowrap>&nbsp;Tarikh Rujukan</th>
					<th nowrap align="center">&nbsp;Kod Akaun</th>
					<th nowrap align="left">&nbsp;Perkara / Nama Akaun</th>
					<th nowrap align="left">&nbsp;Keterangan Perkara</th>
					<th nowrap align="right">&nbsp;Kredit / Amaun (RP)</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		$idcode 	= $rs->fields(deductID);
		$codeakaun 	= dlookup("generalacc", "code", "ID=" . $idcode);
		$namaakaun 	= dlookup("generalacc", "name", "ID=" . $idcode);
		$namadoc 	= $rs->fields(docNo);
		$debit 		= $rs->fields(pymtAmt);
		$date 		= toDate("d/m/y", $rs->fields(tarikh_doc));

		print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td width="2%" align="right">' . $bil . ')&nbsp;</td>
						<td align="center">&nbsp;' . $namadoc . '</td>
						<td align="center">&nbsp;' . $date . '</td>
						<td align="center">&nbsp;' . $codeakaun . '</td>
						<td align="left">&nbsp;' . $namaakaun . '</td>
						<td align="left">&nbsp;' . $rs->fields(desc_akaun) . '</td>
						<td align="right">&nbsp;' . number_format($debit, 2) . '</td>
					</tr>';
		$totalDb += $debit;

		$rs->MoveNext();
	}
	print '<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="right"><b>&nbsp;Jumlah Keseluruhan (RP) :</b></td>
						<td align="right"><b>&nbsp;' . number_format($totalDb, 2) . '</b></td>
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
