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
$title  = 'Senarai Baki Akhir  Keanggotaan Pada Bulan ' . displayBulan($mth) . ' Tahun ' . $yr;


$sSQL = "SELECT SUM(CASE WHEN a.addminus =  '0' THEN a.pymtAmt ELSE 0 END ) AS yuranDb, 
SUM(CASE WHEN a.addminus =  '1' THEN a.pymtAmt ELSE 0 END ) AS yuranKt, a.userID, b.name
FROM transaction a, users b, userdetails c, loans d
WHERE a.deductID IN  (
'1549',  '1539',  '1551',  '1613',  '1622',  '1623',  '1624',  '1625',  '1626',  '1627',  '1628',  '1661',  '1673',  '1675',  '1702',  '1704',  '1709',  '1736',  '1747',  '1762',  '1763',  '1768',  '1788',  '1791',  '1802',  '1804',  '1826',  '1827',  '1646',  '1648',  '1650',  '1662',  '1698', '1719',  '1720',  '1721',  '1722',  '1749',  '1800',  '1679','1595','1607')
AND a.userID = b.userID 
AND a.userID = c.userID
AND a.userID = d.userID
AND d.status =3
AND c.status IN (1,4) 
AND a.yrmth <= '" . $yrmth . "'
GROUP BY a.userID
ORDER BY CAST( a.userID AS SIGNED INTEGER ) ";



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
		<td>
			<table width="100%" border=0 align="center"  cellpadding="2" cellspacing="1">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap><div align="center">Bil</div></td>
					<td nowrap align="center">Nombor Anggota </td>
					<td width="514" align="left" nowrap>Nama Anggota</td>
					<td width="169" align="right" nowrap>Baki Awal (RM)</td>
					<td width="169" align="right" nowrap>Baki Akhir (RM)</td>
					<td width="169" align="right" nowrap>Jumlah Baki Akhir (RM)</td>
			    </tr>';
if ($rs->RowCount() <> 0) {
	//	$countID=0;
	$bil = 1;
	while (!$rs->EOF) {
		//$totalFee = $arrTotal[$rs->fields(userID)];

		$BakiAkhir = ($rs->fields(yuranDb) - $rs->fields(yuranKt));
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="34" align="center">' . $bil . '&nbsp;</td>
							<td width="108" align="center">' . $rs->fields(userID) . '&nbsp;</td>
							<td align="right"><div align="left">' . $rs->fields(name) . '&nbsp;</div></td>
							<td align="right"><div align="right">' . $rs->fields(yuranDb) . '&nbsp;</div></td>
							<td align="right"><div align="right">' . $rs->fields(yuranKt) . '&nbsp;</div></td>
							<td align="right">' . number_format($BakiAkhir, 2) . '&nbsp;</td>
					    </tr>';


		$JumBakiAkhir += $BakiAkhir;
		//$total +=$rs->fields(jum);
		$rs->MoveNext();

		$bil = $bil + 1;
	}
	print '
					<tr bgcolor="FFFFFF"><td colspan="6"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right">Jumlah Keseluruhan (RM) : </td>
						<td align="right">' . number_format($JumBakiAkhir, 2) . '</td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="6" align="center"><b>- Tiada Rekod Dicetak-</b></td>
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
