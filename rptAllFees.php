<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptAllFee.php
 *		   Description	:	Ringkasan Keseluruhan Yuran Anggota 
 *          Date 		: 	31/5/2006
 *********************************************************************************/
session_start();
include("common.php");

$today = date("F j, Y");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Keseluruhan Yuran Anggota Mengikut Cawangan';
$sSQL = "SELECT c.name as department,
		SUM(CASE WHEN a.addminus = '0' THEN -a.pymtAmt ELSE a.pymtAmt END ) AS jumlah
		FROM transaction a, userdetails b, general c
		WHERE 
		a.userID = b.userID
		AND c.ID = b.departmentID 
		AND b.status = 1
		AND a.deductID in (1595,1607) 
		GROUP BY b.departmentID
		ORDER BY b.departmentID";

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
					<td nowrap align="left">Nama Cawangan/Zon</td>
					<td nowrap align="right" width="200">Jumlah (RM)</td>
				</tr>';
$total = 0;
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')</td>
							<td>' . $rs->fields(department) . '</a></td>
							<td align="right">' . number_format($rs->fields(jumlah), 2) . '</a></td>
						</tr>';
		$total += $rs->fields(jumlah);
		$rs->MoveNext();
	}
	//--------------------------------

	$getDeptYuran = "SELECT 
		SUM(CASE WHEN a.addminus = '0' THEN -a.pymtAmt ELSE a.pymtAmt END ) AS totalYuran
		FROM transaction a, userdetails b
		WHERE
		a.userID = b.userID
		AND a.deductID in (1595,1607) 
		AND b.status = 4";

	$rsOpen = $conn->Execute($getDeptYuran);

	if ($rsOpen->RowCount() == 1) $bakiAwal = $rsOpen->fields(totalYuran); //$rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);
	else $bakiAwal = 0;


	print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . ++$bil . ')&nbsp;</td>
							<td>&nbsp;Bersara</a></td>
							<td align="right">&nbsp;' . number_format($bakiAwal, 2) . '</a>&nbsp;&nbsp;&nbsp;</td>
						</tr>';

	$total += $bakiAwal;
	//---------------------------		
	print '
					<tr bgcolor="FFFFFF"><td colspan="3"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan :</td>
						<td align="right">&nbsp;<b>' . number_format($total, 2) . '</b>&nbsp;&nbsp;&nbsp;</td>
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
