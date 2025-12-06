<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	rptF7.php
 *		   Description	:	Report Ringkasan Keseluruhan Maklumat Simpanan Mengikut Jabatan
 *          Date 		: 	07/04/2004
 *********************************************************************************/
session_start();
include("common.php");
$today = date("F j, Y, g:i a");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}
$title  = 'Ringkasan Keseluruhan Maklumat Simpanan Mengikut Jabatan';

$sSQL = "";
$sSQL = "SELECT	b.name as department, sum(a.totalFee) as yuran, sum(a.totalShare) as syer
		 FROM 	userdetails a, general b
		 WHERE 	a.status = '1'  
	 	 AND	b.ID = a.departmentID 
		 GROUP BY department
		 ORDER BY department";
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
	<tr bgcolor="#336699" style="font-family: Poppins, Helvetica, sans-serif; font-size: 9pt; font-weight: bold;">
		<th height="40"><font color="#FFFFFF">' . $title . ' Pada ' . date("d/m/Y") . '
		</th>
	</tr>
	<tr>
		<td><font size=1>Cetak pada : ' . $today . '</font></td>
	</tr>
	<tr>
		<td>
			<table border=0  cellpadding="2" cellspacing="1" align=left width="100%">
				<tr bgcolor="#C0C0C0" style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;">
					<th nowrap>&nbsp;</th>
					<th nowrap align="left">&nbsp;Nama Jabatan</th>
					<th nowrap align="center" width="200">&nbsp;Wajib</th>
					<th nowrap align="center" width="200">&nbsp;Pokok</th>
				</tr>';
$totalFee = 0;
$totalShare = 0;
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		$bil++;
		print '
						<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
							<td width="2%" align="right">' . $bil . ')&nbsp;</td>
							<td>&nbsp;' . $rs->fields(department) . '</a></td>
							<td align="right">&nbsp;' . $rs->fields(yuran) . '</a>&nbsp;&nbsp;&nbsp;</td>
							<td align="right">&nbsp;' . $rs->fields(syer) . '</a>&nbsp;&nbsp;&nbsp;</td>
						</tr>';
		$totalFee += $rs->fields(yuran);
		$totalShare += $rs->fields(syer);
		$rs->MoveNext();
	}
	print '
					<tr bgcolor="FFFFFF"><td colspan="4"><hr size=1></td></tr>						
					<tr style="font-family: Poppins, Helvetica, sans-serif; font-size: 8pt;" bgcolor="FFFFFF">
						<td colspan="2" align="right">Jumlah Keseluruhan Simpanan :</td>
						<td align="right">&nbsp;<b>' . sprintf("%01.2f", $totalFee) . '</b>&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;<b>' . $totalShare . '</b>&nbsp;&nbsp;&nbsp;</td>
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
	<tr><td>&nbsp;</td></tr>
	<tr><td><font size="1" color="#999999"><b>' . $retooFetis . '</b></font></td></tr>	
</table>
</body>
</html>';
