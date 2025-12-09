<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
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
$title  = 'Pinjaman Yang Dikeluarkan Dalam Bulan ' . displayBulan($month) . ' ' . $yr;
$title	= strtoupper($title);

$sSQL = "";
$sSQL = "select a.no_baucer,b.pymtAmt,(b.deductID)as deductID,b.UserID ,c.name,d.departmentID,b.createdDate
from vauchers a
INNER JOIN transaction b ON a.no_baucer = b.docNo 
INNER JOIN users c ON b.UserID = c.UserID
INNER JOIN userdetails d ON d.UserID = c.UserID
WHERE month(b.createdDate) = " . $month . " AND year(b.createdDate) = " . $yr . " AND a.no_baucer <> ''
AND b.deductID IN('1549','1539','1540','1567','1568','1575','1579','1613','1614','1615','1616','1619','1620','1622','1623','1624','1625','1626','1627','1628','1629','1630','1631','1665','1666','1667','1673','1675','1691','1702','1703','1704','1705','1710','1709','1747','1768','1827','1838','1850','1826','1858','1994') ORDER BY b.createdDate";

//'1549','1539','1540','1567','1568','1575','1579','1613','1614','1615','1616','1619','1620','1622','1623','1624','1625','1626','1627','1628','1629','1630','1631','1665','1666','1667','1673','1675','1691','1702','1703','1704','1705','1710','1709','1747','1768','1827','1838','1850','1826'


$rs = &$conn->Execute($sSQL);
//kodPrbd
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
					<th nowrap>&nbsp;</th>
					<th nowrap>&nbsp;Nama</th>
					<th nowrap>&nbsp;Nomor Anggota</th>
					<th nowrap>&nbsp;Cawangan/Jabatan</th>
					<th nowrap>&nbsp;Nomor Voucher</th>
					<th nowrap>&nbsp;Jenis</th>
					<th nowrap>&nbsp;Jumlah</th>
				</tr>';
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		//$time = explode(" ", $rs->fields(sendDate));
		//$rs->fields(referID);
		$jabatan = dlookup("general", "name", "ID=" . tosql($rs->fields(departmentID), "Number"));
		$jenis = dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), ""));
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(name) . '</a></td>
							<td>&nbsp;' . $rs->fields(UserID) . '</a></td>
							<td>&nbsp;' . $jabatan . ' </a></td>
							<td>&nbsp;' . $rs->fields(no_baucer) . '</a></td>
							<td>&nbsp;' . $jenis . ' </a></td>
							<td align="right">&nbsp;' . thousand($rs->fields(pymtAmt)) . '</a></td>
						</tr>';
		$totLoan += $rs->fields(pymtAmt);
		$rs->MoveNext();
	}
	print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td colspan="6" align="right">Jumlah keseluruhan : </td>
							<td align="right">&nbsp;' . number_format($totLoan, 2) . '</a></td>
						</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="7" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '		</table> 
		</td>
	</tr>
	<tr><td colspan="5">&nbsp;</td></tr>
	<tr align="center"><td colspan="5"><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
