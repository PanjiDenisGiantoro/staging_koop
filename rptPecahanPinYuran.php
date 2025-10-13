<?php
#put this in the very beginning
//$timestart = microtime();
/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAllFee.php
 *		   Description	:	Ringkasan Keseluruhan Wajib Anggota 
 *          Date 		: 	31/5/2006
 *********************************************************************************/
session_start();
include("common.php");
include("koperasiQry.php");
$today = date("F j, Y");
$month = (int)substr($yrmth, 4, 2);
$yr = (int)substr($yrmth, 0, 4);
$yr1 = $yr + 1;
$mth1 = $month + 1;

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Jumlah Dan Pecahan Pinjaman Yang Dikeluarkan Dalam Bulan ' . displayBulan($month) . ' Tahun ' . $yr;

$sSQL2 = "SELECT 
		SUM(CASE WHEN a.addminus = '0' AND year(a.createdDate) < " . $yr1 . " THEN a.pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN a.addminus = '1' AND year(a.createdDate) < " . $yr1 . "  THEN a.pymtAmt ELSE 0 END) AS yuranKt,
		SUM(CASE WHEN a.addminus = '0' AND year(a.createdDate) = " . $yr . " AND month(a.createdDate)= " . $month . " THEN a.pymtAmt ELSE 0 END) AS Baucer,
		SUM(CASE WHEN a.addminus = '1' AND year(a.createdDate) = " . $yr . " AND month(a.createdDate)= " . $month . " THEN a.pymtAmt ELSE 0 END) AS Resit, b.name
		FROM transaction a, general b
		WHERE a.deductID=b.ID AND a.deductID IN ('1607','1539','1613','1614','1644','1626','1631','1595','1622','1623','1624','1702','1704','1709','1736','1747','1551','1549','1545','1547','1545','1537','1542','1541','0')GROUP BY a.deductID";

$rs2 = &$conn->Execute($sSQL2);



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
			<table border=0  cellpadding="2" cellspacing="1" align=center width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap><div align="left">Jenis Pinjaman</div></td>
					<td nowrap  width="143"><div align="right">Baki Awal</div></td>
				    <td nowrap  width="192"><div align="right">Tambahan (+) </div></td>
				    <td nowrap  width="185"><div align="right">Kekurangan (-) </div></td>
				    <td nowrap  width="130"><div align="right">Baki Akhir (RP)</div></td>
				</tr>';


if ($rs2->RowCount() <> 0) {

	while (!$rs2->EOF) {

		$yuranDb = $rs2->fields(yuranDb);
		$yuranKt = $rs2->fields(yuranKt);

		$BakiAwl = $yuranDb - $yuranKt;

		$jum = ($BakiAwl + $rs2->fields(Baucer)) - $rs2->fields(Resit);

		//$totalFee = $arrTotal[$rs->fields(userID)];
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="134" align="left">' . $rs2->fields(name) . '&nbsp;</td>
							<td align="right">' . number_format($BakiAwl, 2) . '&nbsp;</td>
						    <td align="right">' . number_format($rs2->fields(Baucer), 2) . '&nbsp;</td>
						    <td align="right">' . number_format($rs2->fields(Resit), 2) . '&nbsp;</td>
						    <td align="right">&nbsp;' . number_format($jum, 2) . '&nbsp;</td>
						</tr>';

		//	$countID2 +=$rs->fields(Bil);
		$jumlah += $jum;

		$rs2->MoveNext();
	}
	print '
					<tr bgcolor="FFFFFF"><td colspan="6"><hr size=1></td></tr>						
						<td align="right">&nbsp;</td>
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
