<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptbank_debkre.php
 *          Date 		: 	10/06/2022
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");

date_default_timezone_set("Asia/Jakarta");
$today = date("F j, Y, g:i a");

$yr = (int)substr($yrmth, 0, 4);
$mth = (int)substr($yrmth, 4, 2);
$yrmth2 = substr($yrmth, 0, 4) . substr($yrmth, 4, 2);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

$title  = 'Laporan Harian Transaksi Atas Talian (Online SWIPE) Dari ' . $dtFrom . ' Hingga ' . $dtTo . '';
$title	= strtoupper($title);

$sSQL = "SELECT * FROM resitonline WHERE statusRP IN (1)  
		AND createdDate BETWEEN " . tosql($dtFrom, "Text") . " AND " . tosql($dtTo, "Text") . "
		ORDER BY createdDate";

$rs = &$conn->Execute($sSQL);

/*
	AND MONTH(createdDate) = ".$mth." 
	AND YEAR(createdDate) = ".$yr."
*/

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
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr>
		<td colspan="5">
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%" bgcolor="999999">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th width="2%" nowrap align="center">Bil</th>
					<th nowrap align="center">Tarikh Transaksi</th>
					<th nowrap align="center">Nomor Anggota</th>
					<th nowrap align="left">Nama Anggota</th>
					<th nowrap align="center">Nombor Kartu Identitas</th>
					<th nowrap align="center">Nomor Voucher</th>
					<th nowrap align="right">Belian (RP)</th>
				</tr>';

$totalA = 0;
$totalB = 0;
if ($rs->RowCount() <> 0) {


	while (!$rs->EOF) {

		$date = toDate("d/m/Y", $rs->fields(createdDate));
		$namaang = dlookup("users", "name", "userID=" . tosql($rs->fields('bayar_nama'), "Number"));

		$icang 	= dlookup("userdetails", "newIC", "userID=" . tosql($rs->fields('bayar_nama'), "Number"));

		$bil++;

		print '
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
				<td align="center">' . $bil . ')</td>
				<td align="center">' . $rs->fields(createdDate) . '</td>
				<td align="center">' . $rs->fields(bayar_nama) . '</td>
				<td align="left">' . $namaang . '</td>
				<td align="center">' . $icang . '</td>';

		$total2 = $rs->fields(amount);

		print '
			<td align="center">' . $rs->fields(no_resit) . '</td>
			<td align="right">' . number_format($total2, 2) . '</td>';

		$totalB += $total2;


		$rs->MoveNext();
	}
	print '
			</tr>
			
			<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
				<td colspan="5" align="right"></td>
				<td align="right">Jumlah Keseluruhan Belian (RP) &nbsp;</td>
				<td align="right">' . number_format($totalB, 2) . '</td>
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
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
