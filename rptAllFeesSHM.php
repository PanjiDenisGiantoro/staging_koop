<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAllFeesSHM.php
 *		   Description	:	Ringkasan Keseluruhan Pokok Anggota 
 *          Date 		: 	14/09/2018
 *********************************************************************************/
session_start();
include("common.php");

include("koperasiQry.php");
$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Keseluruhan Pokok Anggota Mengikut Nombor Keanggotaan';

$sSQL = "SELECT b.memberID as userID, c.name as name, 
SUM(CASE WHEN a.addminus = '0' THEN -a.pymtAmt ELSE a.pymtAmt END ) AS jumlah 
FROM transaction a, userdetails b, users c 
WHERE a.userID = b.userID 
AND b.userID = c.userID 
AND a.deductID in (1596,1780) 
AND b.status IN (1,4) 
GROUP BY a.userID order by CAST(b.memberID AS SIGNED INTEGER)";

$rs = &$conn->Execute($sSQL);

$total = 0;
$arrName = array();
$arrTotal = array();

if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$totalFee = $rs->fields(jumlah);
		$userID = $rs->fields(userID);
		$name = $rs->fields(name);
		$arrTotal[$userID] =  $totalFee;
		$arrName[$userID] =  $name;
		$total += $totalFee;
		$rs->MoveNext();
	}
} else { //
}

$sSQL = "";
$sSQL = "SELECT	CAST( b.userID AS SIGNED INTEGER ) as userID, b.name as name, a.totalFee as jumlah 
		 FROM 	userdetails a, users b
		 WHERE 	a.status in (1,4)
	 	 AND	b.userID = a.userID 
		 ORDER BY userID";

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
		<td align="right">' . strtoupper($emaNetis) . '</td>
	</tr>
	<tr bgcolor="#008080" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . ' Pada ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak Pada : ' . $today . '<br />Oleh : ' . get_session('Cookie_fullName') . '</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap>&nbsp;</td>
					<td nowrap align="left">&nbsp;Nomor Anggota - Nama</td>
					<td nowrap align="right" width="200">Jumlah (RP)</td>
				</tr>';
$total = 0;
if ($rs->RowCount() <> 0) {
	$yr = date("Y");
	while (!$rs->EOF) {
		$totalFee = $arrTotal[$rs->fields(userID)];
		$bil++;
		$total += $totalFee;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')</td>
							<td>' . $rs->fields(userID) . ' - &nbsp;' . $rs->fields(name) . '</a></td>
							<td align="right">' . number_format($totalFee, 2) . '</a></td>
						</tr>';

		$rs->MoveNext();
	}
	print '
					<tr bgcolor="FFFFFF"><td colspan="3"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan  :</td>
						<td align="right"><b>' . number_format($total, 2) . '</b></td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="3" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	
</table>
</body>
</html>
<tr><td>&nbsp;</td></tr>
<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
