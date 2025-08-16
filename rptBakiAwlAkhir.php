<?php

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
$yr = (int)substr($yr, 0, 4);
$yr1 = $yr + 1;

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Jumlah Pecahan Baki Awal Dan Akhir Pembiayaan Bagi Tahun ' . $yr;


$sSQL =

	"SELECT c.name, a.deductID, 
		SUM(CASE WHEN a.addminus =  '0' THEN a.pymtAmt ELSE 0 END) AS bakiAwal, 
		SUM(CASE WHEN a.addminus =  '1' THEN a.pymtAmt ELSE 0 END) AS bakiAkhir
FROM transaction a, users b, general c, userdetails d, loans e, loandocs f WHERE year( a.createdDate ) < '" . $yr1 . "'
AND e.loanID = f.loanID
AND a.pymtRefer = f.rnoBond
AND e.status =3
AND a.userID = b.userID
AND a.deductID = c.ID
AND a.userID = d.userID
AND d.status IN (1,4) 
AND a.deductID IN ('1644','1838','0')
GROUP BY a.deductID ORDER BY 1 ASC ";
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
			<table border=0  cellpadding="2" cellspacing="1" align=center width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<td nowrap><div align="left">Jenis Pinjaman</div></td>
					<td width="92" align="center" nowrap>Bilangan Anggota</td>
					<td nowrap align="right" width="130">Baki Awal (RM)</td>
				    <td nowrap align="right" width="130">Baki Akhir (RM)</td>
				    <td nowrap align="right" width="130">Beza (RM)</td>
				</tr>';
if ($rs->RowCount() <> 0) {
	//	$countID=0;
	//$total=0;
	while (!$rs->EOF) {
		$totalAkhir = $rs->fields(BakiAwl) - $rs->fields(BakiAkhir);
		$totalBaki = $rs->fields(BakiAwl) - $totalAkhir;
		$deduct = $rs->fields(deductID);
		$totalyuranBakiA = $rs->fields(BakiAkhir) - $rs->fields(BakiAwl);
		$totalyuran = $totalyuranBakiA - $rs->fields(BakiAwl);

		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="112" align="left">' . $rs->fields(name) . '</td>
							<td align="center">' . $rs->fields(Bil) . '</td>
				     		<td align="right">' . $rs->fields(BakiAwl) . '</td>
							<td align="right">';
		if ($rs->fields(deductID) == 1595) {
			echo number_format($totalyuranBakiA, 2);
		} else {
			echo number_format($totalAkhir, 2);
		}
		print '</td>
							<td align="right">';
		if ($rs->fields(deductID) == 1595) {
			echo number_format($totalyuran, 2);
		} else {
			echo number_format($totalBaki, 2);
		}
		print '</td>
						</tr>';

		$rs->MoveNext();
	}
	print '
					<tr bgcolor="FFFFFF"><td colspan="5"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td align="right"></td>
						<td align="right">&nbsp;</td>
						<td colspan="3" align="right">&nbsp;&nbsp;&nbsp;</td>
					</tr>';
} else {
	print '
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="5" align="center"><b>- Tiada Rekod Dicetak-</b></td>
					</tr>';
}
print '</table></td></tr><tr><td>&nbsp;</td></tr>
	</table></body></html>
	<center><tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr></center>';
