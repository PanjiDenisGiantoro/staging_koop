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
$month = (int)substr($yrmth, 4, 2);
$yr = (int)substr($yrmth, 0, 4);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Jumlah Dan Pecahan Pinjaman Yang Dikeluarkan Dalam Bulan ' . displayBulan($month) . ' Tahun ' . $yr;


$sSQL = "SELECT sum(a.pymtAmt)as jum , 
a.deductID,b.name, count(a.deductID)as Bil FROM 
transaction a, general b where 
year(a.createdDate)=" . $yr . " and month(a.createdDate)=" . $month . " 
and a.deductID=b.ID and a.addminus='0' and a.deductID IN ('1607','1539','1613','1614','1644','1626','1631','1595','1622','1623','1624','1702','1704','1709','1736','1747','1551','1549','1545','1547','1545','1537','1542','1541','0') group by a.deductID";

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
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap><div align="left">Jenis Pinjaman</div></td>
					<td width="188" align="center" nowrap>Bilangan Anggota</td>
					<td nowrap align="right" width="244">Jumlah (RP)</td>
				</tr>';


if ($rs->RowCount() <> 0) {
	//	$countID=0;
	//$total=0;
	while (!$rs->EOF) {
		//$totalFee = $arrTotal[$rs->fields(userID)];
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="645" align="left">' . $rs->fields(name) . '</td>
							<td align="center">' . $rs->fields(Bil) . '</td>
							<td align="right">' . number_format($rs->fields(jum), 2) . '</td>
						</tr>';

		$countID2 += $rs->fields(Bil);
		$total += $rs->fields(jum);
		$rs->MoveNext();
	}
	print '
					<tr bgcolor="FFFFFF"><td colspan="3"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td align="right">Jumlah Keseluruhan  :</td>
						<td align="center">' . $countID2 . '</td>
						<td align="right">' . number_format($total, 2) . '</td>
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
